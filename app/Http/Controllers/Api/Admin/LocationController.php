<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Location::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            // Jika lokasi baru diset default, matikan default lokasi lain
            if ($request->default) {
                Location::where('default', true)->update(['default' => false]);
            }

            $location = Location::create($request->validated());
            return response()->json(['success' => true, 'data' => $location], 201);
        });
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
    public function update(StoreLocationRequest $request, Location $location): JsonResponse
    {
        return DB::transaction(function () use ($request, $location) {
            if ($request->default) {
                Location::where('id', '!=', $location->id)->update(['default' => false]);
            }

            $location->update($request->validated());
            return response()->json(['success' => true, 'data' => $location]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): JsonResponse
    {
        if ($location->default) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus lokasi default'], 400);
        }
        $location->delete();
        return response()->json(['success' => true, 'message' => 'Lokasi dihapus']);
    }
}
