<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceTimeSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => AttendanceTimeSetting::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $setting = AttendanceTimeSetting::create($request->validated());
        return response()->json(['success' => true, 'data' => $setting], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceTimeSetting $attendanceSetting): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $attendanceSetting]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceTimeSetting $attendanceSetting): JsonResponse
    {
        $attendanceSetting->update($request->validated());
        return response()->json(['success' => true, 'data' => $attendanceSetting]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceTimeSetting $attendanceSetting): JsonResponse
    {
        $attendanceSetting->delete();
        return response()->json(['success' => true, 'message' => 'Setting deleted']);
    }
}
