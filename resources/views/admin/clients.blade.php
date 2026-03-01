<x-app-layout>
    <x-slot name="header">
         <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Novo Cliente</button>
        </div>

    </x-slot>

    {{-- Clients Table --}}
    
    <div class="bg-white  shadow overflow-x-auto container mx-auto my-5">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Nome</th>
                    <th class="p-3 text-left">Documento</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Telefone</th>
                    <th class="p-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $client->name }}</td>
                        <td class="p-3">{{ $client->document }}</td>
                        <td class="p-3">{{ $client->email }}</td>
                        <td class="p-3">{{ $client->phone }}</td>
                        <td class="p-3 text-center flex justify-center gap-2 ">
                            <button onclick='openEditModal(@json($client))' class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Editar</button>
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Deseja excluir o cliente: {{ $client->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-5 text-center text-gray-500">
                            Nenhum cliente cadastrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}

    <div id="ClientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4" id="modalTitle">Novo Cliente</h3>
            <form id="ClientForm" action="{{ route('clients.store') }}" method="POST" class="space-y-4" novalidate>
                @csrf
                <div>
                    <label class="block text-sm">Nome</label>
                    <input id="name" name="name" required class="w-full border rounded p-2" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm">Documento</label>
                    <input id="document" name="document" required class="w-full border rounded p-2"  oninput="validateDocument(this)" value="{{ old('document') }}" required>
                    @error('document')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm">Email</label>
                    <input id="email" type="email" name="email" required class="w-full border rounded p-2" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm">Telefone</label>
                    <input id="phone" name="phone" class="w-full border rounded p-2" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Messages Form --}}

    <div class="absolute top-10 right-0 mt-4 mr-4">
        <div class="bg-green-100 rounded-lg shadow-md message">
            @if (session('success'))
                <p class="text-green-600 p-4">{{ session('success') }}</p>
            @endif
        </div>
        <div class="bg-red-100 rounded-lg shadow-md message">
            @error('error')
                <p class="text-red-600 p-4">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Open Form if Error Exists --}}

    @if ($errors->any() && session('closeModal') != true)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('ClientModal').classList.remove('hidden');
            document.getElementById('ClientModal').classList.add('flex');
        });
    </script>
    @endif


    {{-- Open form if Edit Error  --}}

    @if (session('edit_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const id = "{{ session('edit_id') }}";
            const form = document.getElementById('ClientForm');

            form.action = `/clients/${id}`;

            form.querySelectorAll('input[name="_method"]').forEach(e => e.remove());
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            document.getElementById('modalTitle').innerText = 'Editar Cliente';
        });
    </script>
    @endif


<script>
function openModal() {
    document.getElementById('ClientForm').reset();
    document.getElementById('modalTitle').innerText = 'Novo Cliente';
    document.getElementById('ClientForm').action = '{{ route("clients.store") }}';
    document.getElementById('ClientModal').classList.remove('hidden');
    document.getElementById('ClientModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('ClientModal').classList.add('hidden');
    document.getElementById('ClientModal').classList.remove('flex');
}
function validateDocument(input){
    let value = input.value.replace(/\D/g, '');
    if(value.length > 11) value = value.slice(0, 11);
    input.value = value;
}
function openEditModal(client) {
    let form = document.getElementById('ClientForm');

    document.getElementById('modalTitle').innerText = 'Editar Cliente';
    document.getElementById('name').value = client.name;
    document.getElementById('document').value = client.document;
    document.getElementById('email').value = client.email;
    document.getElementById('phone').value = client.phone;
    
    form.action = `/clients/${client.id}`;
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) {
        oldMethod.remove();
    }
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    document.getElementById('ClientModal').classList.add('flex');
    document.getElementById('ClientModal').classList.remove('hidden');
}
document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});
    
</script>

</x-app-layout>