<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Client;
use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProfessionals = Professional::count();
        $totalClients = Client::count();

        $todayAppointments = Appointment::whereDate('date', today())->count();

        $totalAppointments = Appointment::count();

        $nextAppointments = Appointment::with(['client', 'professional'])
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('admin.home', compact(
            'totalProfessionals',
            'totalClients',
            'todayAppointments',
            'totalAppointments',
            'nextAppointments'
        ));
    }
}
