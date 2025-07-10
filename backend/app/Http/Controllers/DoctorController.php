<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return response()->json(['doctors' => $doctors]);
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
    public function show($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        return response()->json(['doctor' => $doctor]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);

        $doctor->update([
            'specialization' => $request->input('specialization'),
            'phone' => $request->input('phone'),
            'degree' => $request->input('degree'),
            'years_of_experience' => $request->input('years_of_experience'),
            'license_number' => $request->input('license_number'),
            'bio' => $request->input('bio'),
        ]);

        if ($request->has('user')) {
            $doctor->user->update([
                'name' => $request->input('user.name'),
                'email' => $request->input('user.email'),
            ]);
        }

        return response()->json([
            'message' => 'Cập nhật thông tin bác sĩ thành công.',
            'doctor' => $doctor->load('user')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
