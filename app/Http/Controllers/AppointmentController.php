<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Client;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['client', 'professional']);

        if ($request->professional) {
            $query->where('professional_id', $request->professional);
        }

        if ($request->date) {
            $query->where('date', $request->date);
        }

        $appointments = $query->orderBy('date')
                              ->orderBy('start_time')
                              ->get();

        $professionals = Professional::all();

        return view('admin.appointments', compact(
            'appointments',
            'professionals'
        ));
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
                'status' => $request->status,
                'notes' => $request->notes,
            ]);
            return redirect()->route('appointments')->with('success', 'Agendamento criado com sucesso');
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

    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'document' => 'required|exists:clients,document',
                    'professional_id' => 'required|exists:professionals,id',
                    'date' => 'required',
                    'start_time' => 'required|string',
                    'end_time' => 'required|string',
                    'slot' => 'required'
                ], 
                [
                    'document.required' => 'O documento é obrigatório.',
                    'document.exists' => 'O cliente informado não está cadastrado.',
                    'professional_id.required' => 'O profissional é obrigatório.',
                    'professional_id.exists' => 'O profissional selecionado não existe.',
                    'date.required' => 'A data é obrigatória.',
                    'start_time.required' => 'O horário inicial da marcação é obrigatório.',
                    'end_time.required' => 'O horário final da marcação é obrigatório.',
                    'slot.required' => 'O horário selecionado é inválido.',
                ]
            );

            $client = Client::where('document', '=', $request->document)->firstOrFail();
            $availability = Availability::where('professional_id', $request->professional_id)
                ->where('date', $request->date)
                ->where('is_active', 1)
                ->firstOrFail();
            $appointment = Appointment::findOrFail($id);
            $appointment->update([
                'client_id' => $client->id,
                'professional_id' => $request->professional_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);
            return redirect()->route('appointments')->with('success', 'Agendamento atualizado com sucesso');
        } catch (ModelNotFoundException $e) {
            if ($e->getModel() === Client::class) {
                return back()->withErrors(['error' => 'Erro: Cliente não cadastrado.'])->withInput()->with('edit_id', $id);
            }
            if ($e->getModel() === Appointment::class) {
                return back()->withErrors(['error' => 'Erro: Agendamento não encontrado.'])->withInput()->with('edit_id', $id);
            }
            if ($e->getModel() === Availability::class) {
                return back()->withErrors(['error' => 'Erro: Disponibilidade não encontrada.'])->withInput()->with('edit_id', $id);
            }
            return back()->withErrors(['error' => 'Registro não encontrado.'])->withInput()->with('edit_id', $id);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('edit_id', $id);
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput()->with('edit_id', $id);
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Ocorreu um erro inesperado.'])->withInput()->with('edit_id', $id);
        }
    }

    public function destroy($id)
    {
        Appointment::destroy($id);

        return redirect()->route('appointments');
    }
}
