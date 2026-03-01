<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professional;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class PublicAppointmentController extends Controller
{
    public function index()
    {
        $professionals = Professional::orderBy('name')->get();
        return view('home', compact('professionals'));
    }
    
    public function store(Request $request)
    {   
        try {
            $request->validate(
                [
                    'document' => 'required|string|max:20',
                    'professional_id' => 'required|exists:professionals,id',
                    'date' => 'required',
                    'start_time' => 'required',
                    'end_time' => 'required',
                ], 
                [
                    'document.required' => 'O documento é obrigatório.',
                    'professional_id.required' => 'O profissional é obrigatório.',
                    'date.required' => 'A data é obrigatória.',
                    'start_time.required' => 'O horário inicial da marcação é obrigatório.',
                    'end_time.required' => 'O horário final da marcação é obrigatório.',
                ]
            );

            $client = Client::where('document', '=', $request->document)->firstOrFail();

            Appointment::create([
                'client_id' => $client->id,
                'professional_id' => $request->professional_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status ?? 'scheduled',
                'notes' => $request->notes,
            ]);
            return redirect()->route('public.appointments.index')->with('success', 'Agendamento criado com sucesso');
        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['error' => 'Erro: Cliente não cadastrado.'])->withInput();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput();
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Ocorreu um erro inesperado.'])->withInput();
        }
    }

    public function byDocument(Request $request)
    {
        try {
            $request->validate(
                [
                    'document' => 'required|string'
                ],
                [
                    'document.required' => 'O documento é obrigatório.'
                ]
        );
        $client = Client::where('document', '=', $request->document)->first();
        $appointments = Appointment::with('professional')
            ->where('client_id', $client->id)
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'professional' => $item->professional->name ?? '-',
                    'date' => Carbon::parse($item->date)->format('d/m/Y'),
                    'start_time' => $item->start_time,
                ];
            });

        return response()->json($appointments);
        } catch (\Throwable $th) {
            return response()->json([]);
        }
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'professional_id' => 'required',
            'date' => 'required|date',
        ]);

        $professionalId = $request->professional_id;
        $date = $request->date;

        $availabilities = Availability::where('professional_id', $professionalId)
            ->where('date', $date)
            ->where('is_active', 1)
            ->get();

        if (!$availabilities) {
            return response()->json([]);
        }

        $appointments = Appointment::where('professional_id', $professionalId)
            ->where('date', $date)
            ->get();

        $slots = [];
        
        foreach ($availabilities as $availability) {

            $start = Carbon::parse($availability->start_time);
            $end   = Carbon::parse($availability->end_time);

            $duration = $availability->duration_minutes;

            while ($start->copy()->addMinutes($duration)->lte($end)) {

                $slotStart = $start->format('H:i:s');
                $slotEnd   = $start->copy()->addMinutes($duration)->format('H:i:s');
            
                $busy = $appointments->contains(function ($appt) use ($slotStart, $slotEnd) {
                    return !(
                        $appt->end_time <= $slotStart ||
                        $appt->start_time >= $slotEnd
                    );
                });

                if (!$busy) {
                    $slots[] = [
                        'start' => $slotStart,
                        'end'   => $slotEnd
                    ];
                }

                $start->addMinutes($duration);
            }
        }
        return response()->json($slots);
    }

}
