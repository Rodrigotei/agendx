<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Professional;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = Availability::with('professional')->get();
        $professionals = Professional::all();

        return view('admin.availabilities', compact(
            'availabilities',
            'professionals'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'professional_id' => 'required|exists:professionals,id',
                    'date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required|after:start_time',
                    'duration_minutes' => 'required|integer|min:1',
                ],
                [
                    'professional_id.required' => 'O campo profissional é obrigatório.',
                    'professional_id.exists' => 'O profissional selecionado é inválido.',
                    'date.required' => 'O campo data é obrigatório.',
                    'date.date' => 'O campo data deve ser uma data válida.',
                    'start_time.required' => 'O campo hora inicial é obrigatório.',
                    'end_time.required' => 'O campo hora final é obrigatório.',
                    'end_time.after' => 'A hora final deve ser posterior à hora inicial.',
                    'duration_minutes.required' => 'O campo duração em minutos é obrigatório.',
                    'duration_minutes.integer' => 'O campo duração em minutos deve ser um número inteiro.',
                    'duration_minutes.min' => 'O campo duração em minutos deve ser no mínimo 1 minuto.',
                ]
            );
            Availability::create([
                'professional_id' => $request->professional_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $request->duration_minutes,
                'is_active' => $request->is_active ?? 0,
            ]);

            return redirect()->route('availabilities')->with('success', 'Disponibilidade salva com sucesso.');
       } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
       } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput();
       } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Ocorreu um erro inesperado.'])->withInput();
       }
    }

    public function update(Request $request, $id)
    {
        try{
            $request->validate(
                 [
                    'professional_id' => 'required|exists:professionals,id',
                    'date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required|after:start_time',
                    'duration_minutes' => 'required|integer|min:1',
                ],
                [
                    'professional_id.required' => 'O campo profissional é obrigatório.',
                    'professional_id.exists' => 'O profissional selecionado é inválido.',
                    'date.required' => 'O campo data é obrigatório.',
                    'date.date' => 'O campo data deve ser uma data válida.',
                    'start_time.required' => 'O campo hora inicial é obrigatório.',
                    'end_time.required' => 'O campo hora final é obrigatório.',
                    'end_time.after' => 'A hora final deve ser posterior à hora inicial.',
                    'duration_minutes.required' => 'O campo duração em minutos é obrigatório.',
                    'duration_minutes.integer' => 'O campo duração em minutos deve ser um número inteiro.',
                    'duration_minutes.min' => 'O campo duração em minutos deve ser no mínimo 1 minuto.',
                ]
            );

            $availability = Availability::findOrFail($id);
            $hasAppointmentsDate = Appointment::where('date', $availability->date)->where('professional_id', $availability->professional_id)->exists(); //TRUE | FALSE
            
            $changingStructure =
                $availability->date != $request->date ||
                $availability->professional_id != $request->professional_id ||
                $availability->start_time != $request->start_time ||
                $availability->end_time != $request->end_time;
            // dd($changingStructure);
           if ($hasAppointmentsDate && ($changingStructure || $request->is_active == null)) {
                 return back()->withErrors(['error' => 'Não é possível alterar ou desativar esta disponibilidade porque já existem agendamentos vinculados.'])->withInput()->with('edit_id', $id);
            }
            $availability = Availability::findOrFail($id);
            
            $availability->update([
                'professional_id' => $request->professional_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $request->duration_minutes,
                'is_active' => $request->is_active ?? 0,
            ]);

            return redirect()->route('availabilities')->with('success', 'Disponibilidade atualizada com sucesso');
        } catch (ValidationException $e){
            return back()->withErrors($e->errors())->withInput()->with('edit_id', $id);
        } catch (QueryException $e){
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados'])->withInput()->with('edit_id', $id);
        } catch (\Throwable $e){
            return back()->withErrors(['error' => 'Ocorreu um erro inesperado.'])->withInput()->with('edit_id', $id);
        }
    }

    public function destroy($id)
    {
        Availability::destroy($id);

        return redirect()->route('availabilities');
    }
}
