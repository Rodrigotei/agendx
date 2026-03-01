<x-app-layout>
    <x-slot name="header">
         <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profissionais</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Novo Agendamento</button>
        </div>
    </x-slot>

    <div class="bg-white p-4 rounded shadow mb-1 grid grid-cols-3 gap-3 container mx-auto my-5">
        <form method="GET" class="col-span-3 grid grid-cols-3 gap-3">
            <select name="professional" class="border rounded p-2">
                <option value="">Todos Profissionais</option>
                @foreach($professionals as $professional)
                    <option value="{{ $professional->id }}" @selected(request('professional') == $professional->id)>{{ $professional->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="border rounded p-2" >
            <button type="submit" class="bg-blue-600 text-white rounded">Filtrar</button>
        </form>
    </div>

    {{-- Appointments Table --}}

    <div class="bg-white shadow overflow-x-auto container mx-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">Cliente</th>
                    <th class="p-3 text-left">Documento</th>
                    <th class="p-3 text-left">Profissional</th>
                    <th class="p-3 text-left">Data</th>
                    <th class="p-3 text-left">Horário</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $appointment->client->name }}</td>
                        <td class="p-3">{{ $appointment->client->document }}</td>
                        <td class="p-3">{{ $appointment->professional->name }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                        <td class="p-3 text-center">
                            @switch($appointment->status)
                                @case('scheduled')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">Agendado</span>
                                    @break
                                @case('completed')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Finalizado</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <button onclick='editAppointment(@json($appointment))' class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Editar</button>
                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Deseja cancelar este agendamento?')" class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Cancelar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-5 text-center text-gray-500">
                            Nenhum agendamento encontrado
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    
    <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white w-full max-w-lg rounded-lg p-6">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Novo Agendamento</h3>
            <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">Cliente (Documento)</label>
                    <input type="text" id="document" name="document" id="document" class="w-full border rounded p-2" placeholder="CPF ou Documento" oninput="validateDocument(this)" value="{{ old('document') }}" required>
                    @error('document')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                    <input type="date" name="date" id="date" class="w-full border rounded p-2" required>
                    @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Horário</label>
                    <select name="slot" id="slot" class="w-full border rounded p-2" required>
                        <option value="">Selecione a data primeiro</option>
                    </select>
                    @error('slot')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
                @error('start_time')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('end_time')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="mb-4">
                    <label class="block mb-1">Status</label>
                    <select name="status" id="status" class="w-full border rounded p-2">
                        <option value="scheduled">Agendado</option>
                        <option value="completed">Finalizado</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Observações</label>
                    <textarea name="notes" id="notes" class="w-full border rounded p-2" rows="3">{{ old('notes') }}</textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded"> Cancelar</button>
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
            document.getElementById('appointmentModal').classList.remove('hidden');
            document.getElementById('appointmentModal').classList.add('flex');
        });
    </script>
    @endif

    {{-- Open form if Edit Error  --}}

    @if (session('edit_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const id = "{{ session('edit_id') }}";
            const form = document.getElementById('appointmentForm');

            form.action = `/appointments/${id}`;

            form.querySelectorAll('input[name="_method"]').forEach(e => e.remove());
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            document.getElementById('modalTitle').innerText = 'Editar Agendamento';
        });
    </script>
    @endif

<script>
function openModal() {
    document.getElementById('appointmentForm').reset();
    document.getElementById('modalTitle').innerText = 'Novo Agendamento';
    document.getElementById('appointmentForm').action = '{{ route("appointments.store") }}';
    document.getElementById('appointmentModal').classList.remove('hidden');
    document.getElementById('appointmentModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
    document.getElementById('appointmentModal').classList.remove('flex');
}
function validateDocument(input){
    let value = input.value.replace(/\D/g, '');
    if(value.length > 11) value = value.slice(0, 11);
    input.value = value;
}
document.getElementById('date').addEventListener('change', loadSlots);
document.getElementById('professional_id').addEventListener('change', loadSlots);
function loadSlots() {
    const date = document.getElementById('date').value;
    const professional = document.getElementById('professional_id').value;
    const slotSelect = document.getElementById('slot');
    if (!date || !professional) {
        return;
    }
    slotSelect.innerHTML = '<option>Carregando...</option>';
    fetch(`/appointments/available-slots?date=${date}&professional_id=${professional}`)
        .then(response => response.json())
        .then(data => {
            slotSelect.innerHTML = '';
            if (data.length === 0) {
                slotSelect.innerHTML = '<option>Sem horários disponíveis</option>';
                return;
            }
            slotSelect.innerHTML = '<option value="">Selecione</option>';
            data.forEach(slot => {
                const option = document.createElement('option');
                option.value = `${slot.start}|${slot.end}`;
                option.text  = `${slot.start} - ${slot.end}`;
                slotSelect.appendChild(option);
            });

            const currentStart = document.getElementById('start_time').value;
            const currentEnd   = document.getElementById('end_time').value;

            if(currentStart && currentEnd){
                const option = document.createElement('option');
                option.value = `${currentStart}|${currentEnd}`;
                option.text  = `${currentStart} - ${currentEnd} (atual)`;
                option.selected = true;
                slotSelect.appendChild(option);
            }
        });
}
document.getElementById('slot').addEventListener('change', function () {
    if (!this.value) return;
    const parts = this.value.split('|');
    document.getElementById('start_time').value = parts[0];
    document.getElementById('end_time').value   = parts[1];
});
function editAppointment(appointment) {
    const form = document.getElementById('appointmentForm');
    document.getElementById('modalTitle').innerText = 'Editar Agendamento';
    document.getElementById('document').value = appointment.client.document;
    document.getElementById('professional_id').value = appointment.professional_id;
    document.getElementById('date').value = appointment.date;
    document.getElementById('start_time').value = appointment.start_time;
    document.getElementById('end_time').value = appointment.end_time;
    document.getElementById('status').value = appointment.status;
    document.getElementById('notes').value = appointment.notes;
    loadSlots();
    form.action = `/appointments/${appointment.id}`;
    
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) {
        oldMethod.remove();
    }
    const methodInput = document.createElement('input');

    methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    document.getElementById('appointmentModal').classList.remove('hidden');
    document.getElementById('appointmentModal').classList.add('flex');
}
document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});
</script>
</x-app-layout>