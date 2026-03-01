<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disponibilidades</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Nova Disponibilidade</button>
        </div>
    </x-slot>

    {{-- Availabilities Table --}}

    <div class="bg-white shadow overflow-x-auto container mx-auto p-4 my-5">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">Profissional</th>
                    <th class="p-3 text-left">Data</th>
                    <th class="p-3 text-left">Início</th>
                    <th class="p-3 text-left">Fim</th>
                    <th class="p-3 text-left">Duração</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($availabilities as $availability)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $availability->professional->name }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($availability->date)->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $availability->start_time }}</td>
                        <td class="p-3">{{ $availability->end_time }}</td>
                        <td class="p-3">{{ $availability->duration_minutes }} min</td>
                        <td class="p-3 text-center">
                            @if($availability->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Ativo</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-sm">Inativo</span>
                            @endif
                        </td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <button onclick='editAvailability(@json($availability))' class="bg-yellow-500 text-white px-3 py-1 rounded text-sm" >Editar</button>
                            <form action="{{ route('availabilities.destroy', $availability->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Deseja excluir disponibilidade de {{ $availability->professional->name }}?')" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-5 text-center text-gray-500">
                            Nenhuma disponibilidade cadastrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    
    </div>

    {{-- Modal Form --}}
    
    <div id="availabilityModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-full max-w-lg rounded-lg p-6">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Nova Disponibilidade</h3>
            <form id="availabilityForm" method="POST" action="{{ route('availabilities.store') }}" novalidate>
                @csrf
                <input type="hidden" id="availability_id">
                <div class="mb-4">
                    <label class="block mb-1">Profissional</label>
                    <select name="professional_id" id="professional_id" class="w-full border rounded p-2" required>
                        <option value="">Selecione</option>
                        @foreach($professionals as $professional)
                            <option value="{{ $professional->id }}" {{ old('professional_id') == $professional->id ? 'selected' : '' }}>{{ $professional->name }}</option>
                        @endforeach
                    </select>
                    @error('professional_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Data</label>
                    <input type="date" name="date" id="date" class="w-full border rounded p-2" value="{{ old('date') }}" required>
                    @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Hora Inicial</label>
                        <input type="time" name="start_time" id="start_time" class="w-full border rounded p-2" value="{{ old('start_time') }}" required>
                    </div>
                    <div>
                        <label class="block mb-1">Hora Final</label>
                        <input type="time" name="end_time" id="end_time" class="w-full border rounded p-2" value="{{ old('end_time') }}" required>
                    </div>
                    @error('start_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('end_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4 mt-2">
                    <label class="block mb-1">Duração (minutos)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" class="w-full border rounded p-2" min="5" step="5" value="{{ old('duration_minutes') }}" required>
                    @error('duration_minutes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4 flex items-center space-x-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label>Ativo</label>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
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

     @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('availabilityModal').classList.remove('hidden');
            document.getElementById('availabilityModal').classList.add('flex');
        });
    </script>
    @endif

    {{-- Open form if Edit Error  --}}

    @if (session('edit_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const id = "{{ session('edit_id') }}";
            const form = document.getElementById('availabilityForm');

            form.action = `/availabilities/${id}`;

            form.querySelectorAll('input[name="_method"]').forEach(e => e.remove());
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            document.getElementById('modalTitle').innerText = 'Editar Disponibilidade';
        });
    </script>
    @endif


</x-app-layout>


<script>
function openModal() {
    document.getElementById('availabilityForm').reset();
    document.getElementById('modalTitle').innerText = 'Nova Disponibilidade';
    document.getElementById('availabilityForm').action = '{{ route("availabilities.store") }}';
    document.getElementById('availabilityModal').classList.remove('hidden');
    document.getElementById('availabilityModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('availabilityModal').classList.add('hidden');
    document.getElementById('availabilityModal').classList.remove('flex');
}
function editAvailability(availability) {
    let form = document.getElementById('availabilityForm');

    document.getElementById('modalTitle').innerText = 'Editar Disponibilidade';
    document.getElementById('professional_id').value = availability.professional_id;
    document.getElementById('date').value = availability.date;
    document.getElementById('start_time').value = availability.start_time;
    document.getElementById('end_time').value = availability.end_time;
    document.getElementById('duration_minutes').value = availability.duration_minutes;
    document.getElementById('is_active').checked = availability.is_active;
    
    form.action = `/availabilities/${availability.id}`;
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) {
        oldMethod.remove();
    }
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    document.getElementById('availabilityModal').classList.remove('hidden');
    document.getElementById('availabilityModal').classList.add('flex');
}
document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});
    
</script>