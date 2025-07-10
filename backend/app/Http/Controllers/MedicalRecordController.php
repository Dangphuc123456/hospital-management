<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicalRecordController extends Controller
{
    public function getByPatient($id)
    {
        $records = MedicalRecord::where('patient_id', $id)->orderBy('created_at', 'desc')->get();
        return response()->json(['records' => $records]);
    }
    public function index()
    {
        $records = MedicalRecord::with(['patient', 'doctor.user','prescription.medications.medication'])->latest()->get();
        $medications = Medication::all();
        return response()->json([
            'records' => $records,
            'medications' => $medications
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'patient_id'      => 'required|exists:patients,id',
                'doctor_id'       => 'nullable|exists:doctors,id',
                'diagnosis'       => 'required|string',
                'treatment'       => 'required|string',
                'file_attachment' => 'nullable|file|max:2048',
                'medications'     => 'nullable|json',
            ]);
            $fileName = null;
            if ($request->hasFile('file_attachment')) {
                $fileName = $request->file('file_attachment')->store('uploads', 'public');
            }
            $prescription = Prescription::create([
                'appointment_id' => null,
                'doctor_id'      => $request->doctor_id,
                'notes'     => $request->notes 
            ]);
            if ($request->filled('medications')) {
                $meds = json_decode($request->medications, true);
                foreach ($meds as $m) {
                    PrescriptionMedication::create([
                        'prescription_id' => $prescription->id,
                        'medication_id'   => $m['medication_id'] ?? null,
                        'dosage'          => $m['dosage'],
                        'duration'        => $m['duration'],
                    ]);
                }
            }
            $record = MedicalRecord::create([
                'patient_id'      => $request->patient_id,
                'doctor_id'       => $request->doctor_id,
                'diagnosis'       => $request->diagnosis,
                'treatment'       => $request->treatment,
                'file_attachment' => $fileName ? basename($fileName) : null,
                'prescription_id' => $prescription->id,
            ]);

            return response()->json(['record' => $record], 201);
        } catch (\Throwable $e) {
            Log::error('Lỗi tạo hồ sơ bệnh án: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi server: ' . $e->getMessage()], 500);
        }
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
    public function update(Request $request, $id)
    {
        $record = MedicalRecord::findOrFail($id);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
        ]);

        $record->update([
            'doctor_id' => $validated['doctor_id'],
            'diagnosis' => $validated['diagnosis'],
            'treatment' => $validated['treatment'],
        ]);

        return response()->json([
            'message' => 'Hồ sơ đã được cập nhật thành công.',
            'record' => $record,
        ]);
    }
    public function getMedications($id)
    {
        $record = MedicalRecord::with('medications')->find($id);

        if (!$record) {
            return response()->json(['message' => 'Không tìm thấy hồ sơ'], 404);
        }

        return response()->json([
            'medications' => $record->medications
        ]);
    }
    public function destroy(string $id)
    {
        //
    }
}
