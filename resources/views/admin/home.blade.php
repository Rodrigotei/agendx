<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Início</h2>
            <p class="text-gray-600">Visão geral do sistema</p>
        </div>
    </x-slot>

      <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 m-5 container mx-auto">

            <!-- Profissionais -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">Profissionais</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalProfessionals }}</p>
            </div>

            <!-- Clientes -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">Clientes</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalClients }}</p>
            </div>

            <!-- Agendamentos Hoje -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">Hoje</h3>
                <p class="text-3xl font-bold mt-2">{{ $todayAppointments }}</p>
            </div>

            <!-- Total Agendamentos -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">Agendamentos</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalAppointments }}</p>
            </div>

        </div>

        <!-- Próximos agendamentos -->
        <div class="bg-white rounded-xl shadow p-6 container mx-auto">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Próximos Agendamentos</h3>

                <a
                    href="{{ route('appointments') }}"
                    class="text-blue-600 hover:underline"
                >
                    Ver todos
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-left">Cliente</th>
                            <th class="p-3 text-left">Profissional</th>
                            <th class="p-3 text-left">Data</th>
                            <th class="p-3 text-left">Hora</th>
                            <th class="p-3 text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($nextAppointments as $appointment)
                            <tr class="border-b hover:bg-gray-50">

                                <td class="p-3">{{ $appointment->client->name }}</td>
                                <td class="p-3">{{ $appointment->professional->name }}</td>

                                <td class="p-3">
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                                </td>

                                <td class="p-3">{{ $appointment->start_time }}</td>

                                <td class="p-3">

                                    @switch($appointment->status)
                                        @case('scheduled')
                                            <span class="text-yellow-600 font-medium">Agendado</span>
                                            @break

                                        @case('confirmed')
                                            <span class="text-blue-600 font-medium">Confirmado</span>
                                            @break

                                        @case('completed')
                                            <span class="text-green-600 font-medium">Finalizado</span>
                                            @break

                                        @case('cancelled')
                                            <span class="text-red-600 font-medium">Cancelado</span>
                                            @break
                                    @endswitch

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-5 text-center text-gray-500">
                                    Nenhum agendamento futuro
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>



</x-app-layout>