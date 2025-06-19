<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function getByPatient($patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->where('status', 'confirmed')
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appt) {
                return [
                    'id' => $appt->id,
                    'date' => $appt->appointment_date,
                    'reason' => $appt->notes ?? 'Không ghi chú'
                ];
            });

        return response()->json($appointments);
    }
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
