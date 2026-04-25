<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleAttendanceRequest;
use App\Models\RoleAttendanceTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = RoleAttendanceTime::with(['role', 'setting'])->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleAttendanceRequest $request): JsonResponse
    {
        // Opsional: Cek jika role sudah punya settingan agar tidak double
        $exists = RoleAttendanceTime::where('role_id', $request->role_id)->first();
        if ($exists) {
            $exists->update(['attendance_time_settings_id' => $request->attendance_time_settings_id]);
            return response()->json(['success' => true, 'message' => 'Setting updated for this role', 'data' => $exists]);
        }

        $assignment = RoleAttendanceTime::create($request->validated());
        return response()->json(['success' => true, 'data' => $assignment], 201);
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
    public function destroy($id): JsonResponse
    {
        RoleAttendanceTime::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Assignment deleted']);
    }
}
