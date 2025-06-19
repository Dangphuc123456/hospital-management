<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function getByPatient($id)
    {
        $records = MedicalRecord::where('patient_id', $id)->orderBy('created_at', 'desc')->get();
        return response()->json(['records' => $records]);
    }
    public function index()
    {
        $records = MedicalRecord::with(['patient', 'doctor'])->latest()->get();

        return response()->json([
            'records' => $records
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'file_attachment' => 'nullable|file|max:2048',
        ]);

        $fileName = null;
        if ($request->hasFile('file_attachment')) {
            $fileName = $request->file('file_attachment')->store('uploads', 'public');
        }

        $record = MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'file_attachment' => $fileName ? basename($fileName) : null,
        ]);

        return response()->json(['record' => $record], 201);
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
