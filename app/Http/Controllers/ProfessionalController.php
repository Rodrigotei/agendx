<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Professional;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProfessionalController extends Controller
{
    public function index()
    {
        $professionals = Professional::select('id', 'name', 'email', 'phone', 'specialization')->get();
        return view('admin.professionals', compact('professionals'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:professionals,email',
                    'phone' => 'nullable|string|max:20',
                    'specialization' => 'nullable|string|max:255',
                ], 
                [
                    'name.required' => 'O nome é obrigatório.',
                    'email.required' => 'O e-mail é obrigatório.',
                    'email.email' => 'Informe um e-mail válido.',
                    'email.unique' => 'Este e-mail já está cadastrado.',
                ]
            );
            Professional::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'specialization' => $request->specialization,
            ]);
            return redirect()->route('professionals')->with('success', 'Profissional salvo com sucesso.');
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
        try {
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:professionals,email,'.$id,
                    'phone' => 'nullable|string|max:20',
                    'specialization' => 'nullable|string|max:255',
                ], 
                [
                    'name.required' => 'O nome é obrigatório.',
                    'email.required' => 'O e-mail é obrigatório.',
                    'email.email' => 'Informe um e-mail válido.',
                    'email.unique' => 'Este e-mail já está cadastrado.',
                ]
            );
            
            $professional = Professional::findOrFail($id);
            
            $professional->update([
                'name' => $request->name, 
                'email' => $request->email, 
                'phone' => $request->phone, 
                'specialization' => $request->specialization
            ]);
            return redirect()->route('professionals')->with('success', 'Profissional atualizado com sucesso.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with(['edit_id' => $id]);
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput()->with(['edit_id' => $id]);
        } catch (ModelNotFoundException $e){
            return back()->withErrors(['error' => 'ID do profissional não encontrado.'])->withInput()->with(['edit_id' => $id]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Ocorreu um erro inesperado.'])->withInput()->with(['edit_id' => $id]);
        }
    }

    public function destroy($id)
    {   
       $professional = Professional::findOrFail($id);
        if ($professional->appointments()->exists()) {
            return back()->withErrors(['error' => 'Não é possível excluir o profissional porque ele possui agendamentos vinculados.'])->with(['closeModal' => true]);
        }
        $professional->delete();

        return redirect()->back();
    }
}
