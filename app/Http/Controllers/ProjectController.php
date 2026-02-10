<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Prebidding;
use App\Models\ProjectHasWorkforce;
use App\Models\ProjectHasMaterial;
use App\Models\ProjectHasEquipment;
use App\Models\ProjectHasMilestone;
use App\Models\ProjectHasDocument;
use App\Models\PrebiddingsHasProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'project_data' => 'required|json',
            'documents.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = json_decode($request->input('project_data'), true);
        
        // Handle uploaded document files - preserve document indices
        $uploadedDocumentPaths = [];
        
        // Get all uploaded files as an array
        $filesArray = $request->file('documents') ?? [];
        
        // Map files to their document indices
        foreach ($data['documents'] as $index => $document) {
            if (isset($filesArray[$index]) && $filesArray[$index] instanceof \Illuminate\Http\UploadedFile && $filesArray[$index]->isValid()) {
                $path = $filesArray[$index]->store('projects/documents', 'public');
                $uploadedDocumentPaths[$index] = $path;
            }
        }
        
        // Validate the decoded data structure
        $validated = validator($data, [
            // Project validation
            'project.project_name' => 'required|string|max:255',
            'project.project_code' => 'required|string|max:100',
            'project.project_address' => 'nullable|string',
            'project.project_start_date' => 'required|date',
            'project.project_end_date' => 'required|date|after:project.project_start_date',
            'project.contract_number' => 'required|string|max:100',
            'project.contract_date' => 'required|date',
            'project.contract_amount' => 'required|numeric|min:0',
            'project.contract_period' => 'required|integer|min:1',
            'project.total_budget' => 'required|numeric|min:0',
            'project.total_utilized' => 'required|numeric|min:0',
            'project.retention' => 'nullable|numeric|min:0',

            // Workforces validation
            'workforces' => 'required|array|min:1',
            'workforces.*.position' => 'required|string|max:255',
            'workforces.*.name' => 'nullable|string|max:255',
            'workforces.*.headcount' => 'required|integer|min:1',
            'workforces.*.status' => 'required|string|max:50',
            'workforces.*.cost' => 'required|numeric|min:0',
            
            // Materials validation
            'materials' => 'required|array|min:1',
            'materials.*.name' => 'required|string|max:255',
            'materials.*.brand' => 'nullable|string|max:255',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.unit' => 'required|string|max:50',
            'materials.*.cost_per_unit' => 'required|numeric|min:0',
            'materials.*.available' => 'nullable|integer|min:0',
            'materials.*.minimum_stock' => 'nullable|integer|min:0',
            
            // Equipment validation
            'equipment' => 'required|array|min:1',
            'equipment.*.name' => 'required|string|max:255',
            'equipment.*.brand' => 'nullable|string|max:255',
            'equipment.*.type' => 'required|string|max:50',
            'equipment.*.quantity' => 'required|integer|min:1',
            'equipment.*.cost_per_unit' => 'required|numeric|min:0',
            'equipment.*.condition' => 'nullable|string|max:50',
            'equipment.*.available' => 'nullable|integer|min:0',
            
            // Milestones validation
            'milestones' => 'required|array|min:1',
            'milestones.*.name' => 'required|string|max:255',
            'milestones.*.start_date' => 'required|date',
            'milestones.*.end_date' => 'required|date|after:milestones.*.start_date',
            'milestones.*.status' => 'required|string|max:50',
            'milestones.*.completion_percentage' => 'required|integer|min:0|max:100',
            
            // Documents validation
            'documents' => 'required|array|min:1',
            'documents.*.document_name' => 'required|string|max:255',
            'documents.*.status' => 'required|string|max:50',
            'documents.*.uploaded_at' => 'required|date',
            
            // Financial validation
            'financial.total_budget' => 'required|numeric|min:0',
            'financial.materials_cost' => 'required|numeric|min:0',
            'financial.equipment_cost' => 'required|numeric|min:0',
            'financial.labor_cost' => 'required|numeric|min:0',
            'financial.subcontractor_cost' => 'required|numeric|min:0',
            'financial.miscellaneous_cost' => 'required|numeric|min:0',
        ])->validate();

        DB::beginTransaction();
        
        try {
            $encodedPrebiddingId = $request->route('id');
            
            // Decode the prebidding ID from Hashids
            $decodedId = Hashids::decode($encodedPrebiddingId);
            if (empty($decodedId)) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 404);
            }
            
            $prebiddingId = $decodedId[0];
            
            // Fetch the prebidding to get client_id
            $prebidding = Prebidding::findOrFail($prebiddingId);
            
            // Add client_id from prebidding to project data
            $data['project']['client_id'] = $prebidding->client_id;
            
            // Add submitted_by from authenticated user
            $data['project']['submitted_by'] = auth()->id();

            // Set initial project status to 'admin_approval'
            $data['project']['status'] = 'admin_approval';
            
            // Create Project
            $project = Project::create($data['project']);
            
            // Insert Workforces with headcount iteration
            if (isset($data['workforces']) && is_array($data['workforces'])) {
                foreach ($data['workforces'] as $workforce) {
                    // Iterate based on headcount to create multiple records
                    for ($i = 0; $i < $workforce['headcount']; $i++) {
                        ProjectHasWorkforce::create([
                            'project_id' => $project->id,
                            'role' => $workforce['position'],
                            'name' => $workforce['name'] ?? '', // Empty string if no name
                            'status' => $workforce['status'] ?? 'Assigned',
                            'cost' => $workforce['cost'] ?? 0
                        ]);
                    }
                }
            }
            
            // Insert Materials
            if (isset($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as $material) {
                    $material['project_id'] = $project->id;
                    ProjectHasMaterial::create($material);
                }
            }
            
            // Insert Equipment
            if (isset($data['equipment']) && is_array($data['equipment'])) {
                foreach ($data['equipment'] as $equipment) {
                    $equipment['project_id'] = $project->id;
                    ProjectHasEquipment::create($equipment);
                }
            }
            
            // Insert Milestones
            if (isset($data['milestones']) && is_array($data['milestones'])) {
                foreach ($data['milestones'] as $milestone) {
                    $milestone['project_id'] = $project->id;
                    ProjectHasMilestone::create($milestone);
                }
            }
            
            // Insert Documents with file_path
            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $index => $document) {
                    $storedPath = null;

                    // 1) Prefer freshly uploaded file for this document index
                    if (isset($uploadedDocumentPaths[$index])) {
                        $storedPath = $uploadedDocumentPaths[$index];
                    }
                    // 2) Otherwise, if we have an existing file path/URL (e.g. NOA from prebidding),
                    //    copy it into the projects/documents folder and use that new path
                    elseif (!empty($document['file_path'] ?? null)) {
                        $existing = $document['file_path'];

                        // Extract path component if a full URL was provided
                        $existingPath = parse_url($existing, PHP_URL_PATH) ?? $existing;
                        $existingPath = ltrim($existingPath, '/');

                        // If it's a typical Storage::url path like storage/prebiddings/..., strip the storage/ prefix
                        if (strpos($existingPath, 'storage/') === 0) {
                            $existingPath = substr($existingPath, strlen('storage/'));
                        }

                        if (Storage::disk('public')->exists($existingPath)) {
                            $filename = basename($existingPath);
                            $newPath = 'projects/documents/' . $filename;

                            // Ensure we don't overwrite an existing file with the same name
                            if (Storage::disk('public')->exists($newPath)) {
                                $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                $uniqueName = $nameWithoutExt . '_' . uniqid();
                                if ($ext) {
                                    $uniqueName .= '.' . $ext;
                                }
                                $newPath = 'projects/documents/' . $uniqueName;
                            }

                            Storage::disk('public')->copy($existingPath, $newPath);
                            $storedPath = $newPath;
                        } else {
                            $storedPath = '';
                        }
                    } else {
                        $storedPath = '';
                    }

                    ProjectHasDocument::create([
                        'project_id' => $project->id,
                        'name' => $document['document_name'],
                        'status' => $document['status'],
                        'uploaded_at' => $document['uploaded_at'],
                        'file_path' => $storedPath,
                    ]);
                }
            }
            
            // Financial data breakdown is calculated from related materials, equipment, and workforces
            // Total budget is already stored in the project record
            
            // Insert to PrebiddingsHasProject
            PrebiddingsHasProject::create([
                'prebidding_id' => $prebiddingId,
                'project_id' => $project->id
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Project created successfully',
                'project_id' => $project->id
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if there was an error
            if (!empty($uploadedDocumentPaths)) {
                Storage::disk('public')->delete($uploadedDocumentPaths);
            }
            
            return response()->json([
                'message' => 'Error creating project',
                'error' => $e->getMessage(),
                'data' => $data
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
