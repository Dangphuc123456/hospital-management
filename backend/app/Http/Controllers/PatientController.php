<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $patients = Patient::with('user')->paginate($perPage);

        return response()->json($patients);
    }


    public function showWithMedicalRecords($id)
    {
        $patient = Patient::with([
            'medicalRecords.prescription.medications.medication',
            'medicalRecords.doctor.user'
        ])->findOrFail($id);

        $doctor = Doctor::where('user_id', auth()->id())->with('user')->first();

        return response()->json([
            'patient' => $patient,
            'records' => $patient->medicalRecords,
            'doctor' => $doctor,
        ]);
    }
    // POST /patients
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'blood_type' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $patient = Patient::create([
                'name' => $request->name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'blood_type' => $request->blood_type,
                'phone' => $request->phone,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            DB::commit();
            return response()->json(['message' => 'Thêm thành công', 'patient' => $patient], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi khi tạo bệnh nhân', 'error' => $e->getMessage()], 500);
        }
    }

    // PUT /patients/{id}
    public function update(Request $request, $id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return response()->json(['message' => 'Không tìm thấy bệnh nhân'], 404);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật đầy đủ các trường
        $patient->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'blood_type' => $request->blood_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        return response()->json(['message' => 'Cập nhật thành công']);
    }

    // DELETE /patients/{id}
    public function destroy($id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return response()->json(['message' => 'Không tìm thấy bệnh nhân'], 404);
        }

        $patient->delete();
        return response()->json(['message' => 'Đã xóa bệnh nhân']);
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
}
