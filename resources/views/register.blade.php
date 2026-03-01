<x-guest-layout>
    <div class="bg-gray-100 py-10">
        <div class="max-w-5xl mx-auto px-4">

            <h1 class="text-3xl font-bold text-gray-800 mb-6">Registro de Usuário</h1>

            <!-- ================= NOVO AGENDAMENTO ================= -->
            <div id="contentNovo" class="bg-white rounded-2xl shadow p-6">
                <form method="POST" action="{{ route('cadastrar.store') }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Nome*</label>
                        <input type="text" name="name" id="name" required class="w-full mt-1 rounded-xl border-gray-300" value="{{ old('name') }}"/>
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium">Documento (CPF)*</label>
                        <input type="text" name="document" class="w-full mt-1 rounded-xl border-gray-300" oninput="validateDocument(this)" required value="{{ old('document') }}"/>
                         @error('document')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium">E-mail*</label>
                        <input type="email" name="email" class="w-full mt-1 rounded-xl border-gray-300" required value="{{ old('email') }}"/>
                         @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium">Telefone</label>
                        <input type="text" name="phone" class="w-full mt-1 rounded-xl border-gray-300" oninput="validatePhone(this)" value="{{ old('phone') }}"/>
                         @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 flex justify-end mt-4">
                        <button type="submit"class="px-6 py-2 bg-green-600 text-white rounded-2xl shadow hover:bg-green-700">Confirmar Agendamento</button>
                    </div>
                </form>
                 <div class="mt-5">
                    <p class="mt-5">Já tem cadastro?</p>
                    <a href="{{ route('public.appointments.index') }}" class="text-blue-600 hover:underline">Clique aqui para agendar</a>
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
function validateDocument(input){
    let value = input.value.replace(/\D/g, '');
    if(value.length > 11) value = value.slice(0,11);
    input.value = value;
}
function validatePhone(input){
    let value = input.value.replace(/\D/g, '');
    if(value.length > 11) value = value.slice(0,11);
    input.value = value;
}
document.querySelectorAll('.message').forEach(el => {
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
});
</script>
</x-guest-layout>