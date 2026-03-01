<x-app-layout>
    <x-slot name="header">
         <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profissionais</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Novo Profissional</button>
        </div>
    </x-slot>

    {{-- Professionals Table --}}

    <div class="bg-white shadow overflow-x-auto container mx-auto p-6 my-5">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left min-w-[150px]">Nome</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Telefone</th>
                    <th class="p-3 text-left">Especialidade</th>
                    <th class="p-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
            @forelse($professionals as $professional)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $professional->name }}</td>
                    <td class="p-3 hover:underline"><a href="mailto:{{ $professional->email }}">{{ $professional->email }}</a></td>
                    <td class="p-3 hover:underline"><a href="tel:{{ $professional->phone ?? '#' }}">{{ $professional->phone ?? '-' }}</a></td>
                    <td class="p-3">{{ $professional->specialization ?? '-'}}</td>
                    <td class="p-3 text-center flex justify-center gap-2 ">
                        <button onclick='editProfessional(@json($professional))' class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Editar</button>
                        <form action="{{ route('professionals.destroy', $professional->id) }}" method="POST" class="inline">
                            @csrf   
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Deseja excluir {{ $professional->name }}?')" class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-5 text-center text-gray-500">
                        Nenhum profissional cadastrado
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    
    <div id="professionalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-full max-w-lg rounded-lg p-6">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Novo Profissional</h3>
            <form id="professionalForm" method="POST" action="{{ route('professionals.store') }}" novalidate>
                @csrf
                
                <input type="hidden" name="id" id="professional_id">
                <div class="mb-4">
                    <label class="block mb-1">Nome</label>
                    <input type="text" name="name" id="name" class="w-full border rounded p-2" value="{{ old('name') }}" @error('name') border-red-500 @enderror required>
                     @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Email</label>
                    <input type="email" name="email" id="email" class="w-full border rounded p-2" value="{{ old('email') }}" @error('email') border-red-500 @enderror required>
                     @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Telefone</label>
                    <input type="text" name="phone" id="phone" class="w-full border rounded p-2" value="{{ old('phone') }}" @error('phone') border-red-500 @enderror>
                     @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Especialidade</label>
                    <input type="text" name="specialization" id="specialization" class="w-full border rounded p-2" value="{{ old('specialization') }}" @error('specialization') border-red-500 @enderror>
                     @error('specialization')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                     @enderror
                </div>
                @error('id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
                </div>
              
            </form>
        </div>
    </div>

    {{-- Messages Success/Error --}}
    
    <div class="message absolute top-10 right-0 mt-4 mr-4">
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
            document.getElementById('professionalModal').classList.remove('hidden');
            document.getElementById('professionalModal').classList.add('flex');
        });
    </script>
    @endif

    {{-- Open form if Edit Error  --}}

    @if (session('edit_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const id = "{{ session('edit_id') }}";
            const form = document.getElementById('professionalForm');

            form.action = `/professionals/${id}`;

            form.querySelectorAll('input[name="_method"]').forEach(e => e.remove());
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            document.getElementById('modalTitle').innerText = 'Editar Profissional';
        });
    </script>
    @endif


<script>

const openModal = () => {
    document.getElementById('professionalForm').reset();
    document.getElementById('professional_id').value = '';
    document.getElementById('modalTitle').innerText = 'Novo Profissional';
    document.getElementById('professionalModal').classList.remove('hidden');
    document.getElementById('professionalModal').classList.add('flex');
}

const closeModal = () => {
    document.getElementById('professionalModal').classList.add('hidden');
    document.getElementById('professionalModal').classList.remove('flex');
}

function editProfessional(professional) {
    const form = document.getElementById('professionalForm');
    document.getElementById('modalTitle').innerText = 'Editar Profissional';
    document.getElementById('name').value = professional.name;
    document.getElementById('email').value = professional.email;
    document.getElementById('phone').value = professional.phone;
    document.getElementById('specialization').value = professional.specialization;
    form.action = `/professionals/${professional.id}`;
    
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) {
        oldMethod.remove();
    }
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'PUT';
    form.appendChild(methodInput);

    document.getElementById('professionalModal').classList.remove('hidden');
    document.getElementById('professionalModal').classList.add('flex');
}


const inputPhone = document.getElementById('phone');
inputPhone.addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    this.value = value;
});

document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});

</script>

</x-app-layout>
