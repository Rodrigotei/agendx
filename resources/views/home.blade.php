<x-guest-layout>
    <div class="bg-gray-100 py-10">
        <div class="max-w-5xl mx-auto px-4">

            <h1 class="text-3xl font-bold text-gray-800 mb-6">Agendamento Online</h1>

            <!-- Tabs -->
            <div class="mb-6 flex gap-2">
                <button onclick="showTab('novo')" id="tabNovo"
                    class="px-4 py-2 rounded-xl bg-blue-600 text-white">Novo Agendamento</button>
                <button onclick="showTab('consultar')" id="tabConsultar"
                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-700">Consultar Agendamentos</button>
            </div>

            <!-- ================= NOVO AGENDAMENTO ================= -->
            <div id="contentNovo" class="bg-white rounded-2xl shadow p-6">
                <form method="POST" action="{{ route('public.appointments.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Documento (CPF)</label>
                        <input type="text" name="document" class="w-full mt-1 rounded-xl border-gray-300" oninput="validateDocument(this)" required value="{{ old('document') }}"/>
                    </div>
                    @error('document')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div>
                        <label class="block text-sm font-medium">Profissional</label>
                        <select name="professional_id" id="professional_id" required class="w-full mt-1 rounded-xl border-gray-300">
                            <option value="">Selecione</option>
                            @foreach($professionals as $professional)
                                <option value="{{ $professional->id }}" {{ old('professional_id') == $professional->id ? 'selected' : '' }}>{{ $professional->name }} - {{ $professional->specialization }}</option>
                            @endforeach
                        </select>
                        @error('professional_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Data</label>
                        <input type="date" name="date" id="date" required class="w-full mt-1 rounded-xl border-gray-300"/>
                        @error('date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4 md:col-span-2">
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
                    <div class="md:col-span-2 flex justify-end mt-4">
                        <button type="submit"class="px-6 py-2 bg-green-600 text-white rounded-2xl shadow hover:bg-green-700">Confirmar Agendamento</button>
                    </div>
                </form>
                <div class="mt-5">
                    <p class="mt-5">Ainda não tem cadastro?</p>
                    <a href="{{ route('cadastrar') }}" class="text-blue-600 hover:underline">Clique aqui para se cadastrar</a>
                </div>
            </div>

            <!-- ================= CONSULTAR ================= -->
            <div id="contentConsultar" class="hidden bg-white rounded-2xl shadow p-6">
                <form id="consultForm" class="flex gap-2 mb-4">
                    <input type="text" id="consultDocument" placeholder="Digite seu CPF" class="flex-1 rounded-xl border-gray-300" oninput="validateDocument(this)" required>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Buscar</button>
                </form>
                <div id="consultResults"></div>
            </div>
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

<script>
function showTab(tab) {
    const novoBtn = document.getElementById('tabNovo');
    const consBtn = document.getElementById('tabConsultar');
    const novo = document.getElementById('contentNovo');
    const cons = document.getElementById('contentConsultar');
    if (tab === 'novo') {
        novo.classList.remove('hidden');
        cons.classList.add('hidden');
        novoBtn.classList.replace('bg-gray-200', 'bg-blue-600');
        novoBtn.classList.replace('text-gray-700', 'text-white');
        consBtn.classList.replace('bg-blue-600', 'bg-gray-200');
        consBtn.classList.replace('text-white', 'text-gray-700');
    } else {
        cons.classList.remove('hidden');
        novo.classList.add('hidden');
        consBtn.classList.replace('bg-gray-200', 'bg-blue-600');
        consBtn.classList.replace('text-gray-700', 'text-white');
        novoBtn.classList.replace('bg-blue-600', 'bg-gray-200');
        novoBtn.classList.replace('text-white', 'text-gray-700');
    }
}
// ===== carregar horários =====
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
    fetch(`/agendamento/available-slots?date=${date}&professional_id=${professional}`)
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
        });
}
document.getElementById('slot').addEventListener('change', function () {
    if (!this.value) return;
    const parts = this.value.split('|');
    document.getElementById('start_time').value = parts[0];
    document.getElementById('end_time').value   = parts[1];
});
function validateDocument(input){
    let value = input.value.replace(/\D/g, '');
    if(value.length > 11) value = value.slice(0,11);
    input.value = value;
}
document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});


document.getElementById('consultForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const container = document.getElementById('consultResults');
    try {
        const doc = document.getElementById('consultDocument').value;
        const res = await fetch(`agendamento/by-document?document=${doc}`);
        const data = await res.json();
        
        if (!data.length) {
            container.innerHTML = '<p class="text-gray-500">Nenhum agendamento encontrado.</p>';
            return;
        }

        let html = '<div class="overflow-auto"><table class="w-full text-sm">';
        html += '<thead><tr class="border-b"><th class="text-left p-2">Profissional</th><th class="text-left p-2">Data</th><th class="text-left p-2">Horário</th></tr></thead><tbody>';

        data.forEach(item => {
            html += `<tr class="border-b">
                <td class="p-2">${item.professional}</td>
                <td class="p-2">${item.date}</td>
                <td class="p-2">${item.start_time}</td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    } catch (error) {
        console.log('erro')
        container.innerHTML = '<p class="text-gray-500">Nenhum agendamento encontrado.</p>';
        return;
    }
});
</script>
</x-guest-layout>
