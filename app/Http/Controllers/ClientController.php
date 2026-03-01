<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();

        return view('admin.clients', compact('clients'));
    }

    public function store(Request $request)
    {
       try{
            $request->validate(
                [
                    'name' => 'required',
                    'document' => 'required|unique:clients',
                    'email' => 'required|email',
                    'phone' => 'nullable'
                ],
                [
                    'name.required' => 'O nome do cliente é obrigatório.',
                    'document.required' => 'O documento é obrigatório.',
                    'document.unique' => 'Este documento já está cadastrado.',
                    'email.required' => 'O email é obrigatório.',
                    'email.email' => 'O email deve ser válido.',
                ]
            );
            Client::create([
                'document' => $request->document,
                'name' => $request->name,   
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
            return back()->with('success', 'Cliente salvo com sucesso.');
        }catch(ValidationException $e){
            return back()->withErrors($e->errors())->withInput();
        }catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput();
        }catch(\Throwable $e){    
            return back()->withErrors(['error' => 'Ocorre um erro inesperado.'])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $request->validate(
                [
                    'name' => 'required',
                    'document' => 'required|unique:clients,document,'.$id,
                    'email' => 'required|email',
                    'phone' => 'nullable'
                ],
                [
                    'name.required' => 'O nome do cliente é obrigatório.',
                    'document.required' => 'O documento é obrigatório.',
                    'document.unique' => 'Este documento já está cadastrado.',
                    'email.required' => 'O email é obrigatório.',
                    'email.email' => 'O email deve ser válido.',
                ]
            );
            $client = Client::findOrFail($id);
            $client->update([
                'document' => $request->document,
                'name' => $request->name,   
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
            return redirect()->route('clients')->with('success', 'Cliente atualizado com sucesso.');
        }catch(ValidationException $e){
            return back()->withErrors($e->errors())->withInput()->with('edit_id', $id);
        }catch (ModelNotFoundException $e) {
            return back()->withErrors(['error' => 'Cliente não encontrado.'])->withInput()->with('edit_id', $id);
        }
        catch (QueryException $e) {
            return back()->withErrors(['error' => 'Erro ao salvar no banco de dados.'])->withInput()->with('edit_id', $id);
        }catch(\Throwable $e){    
            return back()->withErrors(['error' => 'Ocorre um erro inesperado.'])->withInput()->with('edit_id', $id);
        }    
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        if ($client->appointments()->exists()) {
            return back()->withErrors(['error' => 'Não é possível excluir o cliente porque ele possui agendamentos vinculados.'])->with(['closeModal' => true]);
        }
        $client->delete();

        return redirect()->back();
    }
}