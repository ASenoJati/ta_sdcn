<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.locations.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $locations = Location::query();

            return DataTables::of($locations)
                ->addIndexColumn()
                ->addColumn('coordinates', function ($row) {
                    return $row->latitude . ', ' . $row->longitude;
                })
                ->addColumn('radius_km_formatted', function ($row) {
                    return $row->radius_km . ' km';
                })
                ->addColumn('default_badge', function ($row) {
                    if ($row->default) {
                        return '<span class="badge bg-success"><i class="bi bi-star-fill"></i> Default</span>';
                    }
                    return '<span class="badge bg-secondary">Tidak Default</span>';
                })
                ->addColumn('address_short', function ($row) {
                    return $row->address ? \Str::limit($row->address, 50) : '-';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    $defaultBtn = '';
                    if (!$row->default) {
                        $defaultBtn = '<button type="button" class="btn btn-info btn-sm me-1" onclick="setDefault(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                            <i class="bi bi-star"></i> Set Default
                        </button>';
                    }

                    return '
                        <div class="btn-group" role="group">
                            ' . $defaultBtn . '
                            <button type="button" class="btn btn-warning btn-sm me-1" onclick="editLocation(' . $row->id . ')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['default_badge', 'aksi'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Store location request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:locations,name',
                'longitude' => 'required|numeric|between:-180,180',
                'latitude' => 'required|numeric|between:-90,90',
                'radius_km' => 'required|integer|min:1|max:1000',
                'default' => 'sometimes|boolean',
                'address' => 'nullable|string|max:500',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Set default jika tidak ada di request
            $data = $request->all();
            if (!isset($data['default'])) {
                $data['default'] = false;
            }

            $location = Location::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data lokasi berhasil ditambahkan!',
                'data' => $location
            ]);
        } catch (Exception $e) {
            Log::error('Error storing location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update location request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $location = Location::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:locations,name,' . $id,
                'longitude' => 'required|numeric|between:-180,180',
                'latitude' => 'required|numeric|between:-90,90',
                'radius_km' => 'required|integer|min:1|max:1000',
                'default' => 'sometimes|boolean',
                'address' => 'nullable|string|max:500',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            if (!isset($data['default'])) {
                $data['default'] = false;
            }

            $location->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data lokasi berhasil diupdate!',
                'data' => $location
            ]);
        } catch (Exception $e) {
            Log::error('Error updating location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);

            // Cek jika lokasi adalah default
            if ($location->default) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi default tidak dapat dihapus! Set lokasi lain sebagai default terlebih dahulu.'
                ], 400);
            }

            $location->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data lokasi berhasil dihapus!'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set location as default
     */
    public function setDefault($id)
    {
        try {
            Log::info('Set default location', ['id' => $id]);

            // Reset semua lokasi yang default
            Location::where('default', true)->update(['default' => false]);

            // Set lokasi yang dipilih menjadi default
            $location = Location::findOrFail($id);
            $location->default = true;
            $location->save();

            return response()->json([
                'success' => true,
                'message' => 'Lokasi "' . $location->name . '" berhasil dijadikan default!'
            ]);
        } catch (Exception $e) {
            Log::error('Error setting default location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get default location
     */
    public function getDefault()
    {
        $location = Location::where('default', true)->first();
        return response()->json($location);
    }
}
