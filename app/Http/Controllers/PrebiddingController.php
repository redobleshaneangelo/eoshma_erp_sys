<?php

namespace App\Http\Controllers;

use App\Models\Prebidding;
use App\Models\BiddingActivity;
use App\Models\BiddingDocument;
use App\Models\MaterialPlanning;
use App\Models\EquipmentPlanning;
use App\Models\LaborPlanning;
use App\Models\SubcontractorPlanning;
use App\Models\MiscellaneousPlanning;
use App\Models\CategoryBreakdown;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PrebiddingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prebiddings = Prebidding::with(['client', 'biddingDocuments', 'branches', 'submittedBy', 'categoryBreakdowns'])
            ->whereNot('status', 'bidding_won')
            ->where('submitted_by', auth()->id())
            ->get()
            ->map(function ($prebidding) {
                $documents = $prebidding->biddingDocuments;
                
                // Format client name
                $clientName = null;
                if ($prebidding->client) {
                    $clientName = trim(
                        ($prebidding->client->last_name ?? '') . ', ' . 
                        ($prebidding->client->first_name ?? '') . ' ' . 
                        ($prebidding->client->middle_initial ?? '')
                    );
                }
                
                // Format submitted by name
                $submittedByName = null;
                if ($prebidding->submittedBy) {
                    $submittedByName = trim(
                        ($prebidding->submittedBy->last_name ?? '') . ', ' . 
                        ($prebidding->submittedBy->first_name ?? '') . ' ' . 
                        ($prebidding->submittedBy->middle_name ?? '')
                    );
                }
                
                return [
                    'id' => Hashids::encode($prebidding->id),
                    'image' => $prebidding->image ? asset('storage/' . $prebidding->image) : null,
                    'name' => $prebidding->name,
                    'code' => $prebidding->code,
                    'client_name' => $clientName,
                    'address' => $prebidding->address,
                    'status' => $prebidding->status,
                    'bid_opening' => $prebidding->bid_opening,
                    'branches' => $prebidding->branches->pluck('name')->toArray(),
                    'documents_count' => $documents->count(),
                    'uploaded_count' => $documents->where('completed', 1)->count(),
                    'submitted_by' => $submittedByName,
                    'submitted_at' => $prebidding->created_at,
                    'notes' => $prebidding->notes,
                    'total_budget' => $prebidding->total_budget,
                    'documents_uploaded' => $documents->where('completed', 1)->count(),
                    'category_breakdowns' => $prebidding->categoryBreakdowns->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                            'amount' => $category->amount,
                        ];
                    })->values()->toArray(),
                ];
            });

        return response()->json([
            'prebiddings' => $prebiddings,
        ], 200);
    }

    public function pendingApprovalsOperationsPrebiddings()
    {
        $userBranchIds = auth()->user()->branches()->pluck('branches.id')->toArray();
        
        $pendingApprovals = Prebidding::with(['client', 'biddingDocuments', 'branches', 'submittedBy', 'categoryBreakdowns'])
            ->where('status', 'operations_approval')
            ->whereHas('branches', function ($query) use ($userBranchIds) {
                $query->whereIn('branches.id', $userBranchIds);
            })
            ->get()
            ->map(function ($prebidding) {
                $documents = $prebidding->biddingDocuments;
                
                // Format client name
                $clientName = null;
                if ($prebidding->client) {
                    $clientName = trim(
                        ($prebidding->client->last_name ?? '') . ', ' . 
                        ($prebidding->client->first_name ?? '') . ' ' . 
                        ($prebidding->client->middle_initial ?? '')
                    );
                }
                
                // Format submitted by name
                $submittedByName = null;
                if ($prebidding->submittedBy) {
                    $submittedByName = trim(
                        ($prebidding->submittedBy->last_name ?? '') . ', ' . 
                        ($prebidding->submittedBy->first_name ?? '') . ' ' . 
                        ($prebidding->submittedBy->middle_name ?? '')
                    );
                }
                
                return [
                    'id' => Hashids::encode($prebidding->id),
                    'image' => $prebidding->image ? asset('storage/' . $prebidding->image) : null,
                    'name' => $prebidding->name,
                    'code' => $prebidding->code,
                    'address' => $prebidding->address,
                    'client_name' => $clientName,
                    'status' => $prebidding->status,
                    'bid_opening' => $prebidding->bid_opening,
                    'branches' => $prebidding->branches->pluck('name')->toArray(),
                    'documents_count' => $documents->count(),
                    'uploaded_count' => $documents->where('completed', 1)->count(),
                    'submitted_by' => $submittedByName,
                    'submitted_at' => $prebidding->created_at,
                    'notes' => $prebidding->notes,
                    'total_budget' => $prebidding->total_budget,
                    'documents_uploaded' => $documents->where('completed', 1)->count(),
                    'category_breakdowns' => $prebidding->categoryBreakdowns->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                            'amount' => $category->amount,
                        ];
                    })->values()->toArray(),
                ];
            });

        return response()->json([
            'pendingApprovals' => $pendingApprovals,
        ], 200);
    }

    public function pendingApprovalsFinancePrebiddings()
    {
        $userBranchIds = auth()->user()->branches()->pluck('branches.id')->toArray();
        
        $pendingApprovals = Prebidding::with(['client', 'biddingDocuments', 'branches', 'submittedBy', 'categoryBreakdowns'])
            ->where('status', 'finance_approval')
            ->whereHas('branches', function ($query) use ($userBranchIds) {
                $query->whereIn('branches.id', $userBranchIds);
            })
            ->get()
            ->map(function ($prebidding) {
                $documents = $prebidding->biddingDocuments;
                
                // Format client name
                $clientName = null;
                if ($prebidding->client) {
                    $clientName = trim(
                        ($prebidding->client->last_name ?? '') . ', ' . 
                        ($prebidding->client->first_name ?? '') . ' ' . 
                        ($prebidding->client->middle_initial ?? '')
                    );
                }
                
                // Format submitted by name
                $submittedByName = null;
                if ($prebidding->submittedBy) {
                    $submittedByName = trim(
                        ($prebidding->submittedBy->last_name ?? '') . ', ' . 
                        ($prebidding->submittedBy->first_name ?? '') . ' ' . 
                        ($prebidding->submittedBy->middle_name ?? '')
                    );
                }
                
                return [
                    'id' => Hashids::encode($prebidding->id),
                    'image' => $prebidding->image ? asset('storage/' . $prebidding->image) : null,
                    'name' => $prebidding->name,
                    'code' => $prebidding->code,
                    'address' => $prebidding->address,
                    'client_name' => $clientName,
                    'status' => $prebidding->status,
                    'bid_opening' => $prebidding->bid_opening,
                    'branches' => $prebidding->branches->pluck('name')->toArray(),
                    'documents_count' => $documents->count(),
                    'uploaded_count' => $documents->where('completed', 1)->count(),
                    'submitted_by' => $submittedByName,
                    'submitted_at' => $prebidding->created_at,
                    'notes' => $prebidding->notes,
                    'total_budget' => $prebidding->total_budget,
                    'documents_uploaded' => $documents->where('completed', 1)->count(),
                    'category_breakdowns' => $prebidding->categoryBreakdowns->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                            'amount' => $category->amount,
                        ];
                    })->values()->toArray(),
                ];
            });

        return response()->json([
            'pendingApprovals' => $pendingApprovals,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Decode JSON data from FormData
        $data = json_decode($request->input('data'), true);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('prebiddings', 'public');
        }

        // Handle uploaded document files
        $uploadedDocumentPaths = [];
        $files = $request->file('documents');
        if ($files) {
            // Handle if files is an array
            $filesArray = is_array($files) ? $files : [$files];
            foreach ($filesArray as $index => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('prebiddings/documents', 'public');
                    $uploadedDocumentPaths[$index] = $path;
                }
            }
        }

        // Update uploaded documents with file paths
        if (!empty($uploadedDocumentPaths) && isset($data['documents']['uploaded'])) {
            foreach ($uploadedDocumentPaths as $index => $path) {
                if (isset($data['documents']['uploaded'][$index])) {
                    $data['documents']['uploaded'][$index]['file_path'] = $path;
                }
            }
        }
        
        // Decode client_id from Hashids
        $clientIdEncoded = $data['projectInfo']['client_id'] ?? null;
        $decodedClientId = null;
        
        if ($clientIdEncoded) {
            // If it's an object, extract the id property
            if (is_array($clientIdEncoded)) {
                $clientIdEncoded = $clientIdEncoded['id'] ?? null;
            }
            // Decode using Hashids
            if ($clientIdEncoded) {
                $decodedClientId = Hashids::decode($clientIdEncoded);
            }
        }

        // =====================================================
        // MAIN RECORD DATA (goes to 'prebiddings' table)
        // =====================================================
        
        $mainData = [
            // Project Information
            'name' => $data['projectInfo']['name'] ?? null,
            'code' => $data['projectInfo']['code'] ?? null,
            'type' => $data['projectInfo']['type'] ?? null,
            'address' => $data['projectInfo']['address'] ?? null,
            'client_id' => $decodedClientId[0] ?? null,
            'image' => $imagePath,
            'notes' => $data['projectInfo']['notes'] ?? null,

            // Bid Details
            'bid_deadline' => $data['bidDetails']['deadline'] ?? null,
            'bid_opening' => $data['bidDetails']['opening'] ?? null,
            'bid_type' => $data['bidDetails']['type'] ?? null,
            'bond_type' => $data['bidDetails']['bondType'] ?? null,
            'security_amount' => $data['bidDetails']['amount'] ?? 0,

            // Project Setup
            'supervisor_id' => $data['projectSetup']['supervisor']['id'] ?? null,
            'foreman_id' => $data['projectSetup']['foreman']['id'] ?? null,
            'submitted_by' => auth()->id(),

            // Financial
            'total_budget' => $data['financial']['totalBudget'] ?? 0,
            'total_utilized' => $data['financial']['totalBudgetUtilized'] ?? 0,
            'contract_amount' => $data['financial']['contractAmount'] ?? 0,
            'billing_type' => $data['financial']['billingType'] ?? null,
            'retention' => $data['financial']['retention'] ?? 0,

            'status' => 'operations_approval', // Default status on creation
        ];

        // =====================================================
        // RELATED DATA (needs separate storage - JSON or tables)
        // =====================================================
        $relatedData = [
            // Bidding Activities
            'bidding_activities' => $data['biddingActivities'] ?? [],

            // Documents
            'document_checklist' => $data['documents']['checklist'] ?? [],
            'uploaded_documents' => $data['documents']['uploaded'] ?? [],

            // Planning
            'material_planning' => $data['materialPlanning'] ?? [],
            'equipment_planning' => $data['equipmentPlanning'] ?? [],

            // Cost Plans
            'material_cost_plan' => $data['costPlans']['materials'] ?? [],
            'labor_cost_plan' => $data['costPlans']['labor'] ?? [],
            'equipment_cost_plan' => $data['costPlans']['equipment'] ?? [],
            'subcontractor_cost_plan' => $data['costPlans']['subcontractors'] ?? [],
            'miscellaneous_cost_plan' => $data['costPlans']['miscellaneous'] ?? [],

            // Category Breakdown
            'category_breakdown' => $data['categoryBreakdown'] ?? [],
            'category_breakdown_custom' => $data['categoryBreakdownCustom'] ?? [],
        ];

        // =====================================================
        // VALIDATION
        // =====================================================
        // First check if data field exists and is valid JSON
        if (!$request->has('data')) {
            return response()->json(['message' => 'Data field is required'], 422);
        }

        // Validate the decoded data array
        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'projectInfo.name' => 'required|string|max:255',
            'projectInfo.code' => 'required|string|unique:prebiddings,code|max:100',
            'projectInfo.type' => 'required|string|max:100',
            'projectInfo.address' => 'required|string|max:1000',
            'projectInfo.client_id' => 'required',
            'projectInfo.notes' => 'nullable|string|max:1000',

            // Bid Details
            'bidDetails.deadline' => 'required|date|after:today',
            'bidDetails.opening' => 'required|date|after_or_equal:bidDetails.deadline',
            'bidDetails.type' => 'required|string|max:100',
            'bidDetails.bondType' => 'required|string|max:100',
            'bidDetails.amount' => 'required|numeric|min:0',

            // Project Setup
            'projectSetup.supervisor.id' => 'required|numeric',
            'projectSetup.foreman.id' => 'required|numeric',

            // Financial
            'financial.totalBudget' => 'required|numeric|min:0.01',
            'financial.totalBudgetUtilized' => 'required|numeric|min:0',
            'financial.contractAmount' => 'required|numeric|min:0',
            'financial.billingType' => 'required|string|max:100',
            'financial.retention' => 'required|numeric|min:0',

            // Bidding Activities
            'biddingActivities' => 'required|array|min:1',
            'biddingActivities.*.name' => 'required|string|max:255',
            'biddingActivities.*.date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            if (!empty($uploadedDocumentPaths)) {
                Storage::disk('public')->delete($uploadedDocumentPaths);
            }

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate files in request
        $request->validate([
            'image' => 'nullable|image|max:5120',
            'documents.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            // =====================================================
            // SAVE TO DATABASE
            // =====================================================

            // Create main prebidding record
            $prebidding = Prebidding::create($mainData);

            // Attach branches from authenticated user to prebidding
            $user = auth()->user();
            if ($user && $user->branches()->exists()) {
                $branchIds = $user->branches()->pluck('branches.id')->toArray();
                $prebidding->branches()->attach($branchIds);
            }

            // Store bidding activities
            if (!empty($relatedData['bidding_activities'])) {
                foreach ($relatedData['bidding_activities'] as $activity) {
                    BiddingActivity::create([
                        'name' => $activity['name'] ?? null,
                        'date' => $activity['date'] ?? null,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store bidding documents (merge checklist and uploaded documents)
            $checklist = $relatedData['document_checklist'] ?? [];
            $uploaded = $relatedData['uploaded_documents'] ?? [];

            // Process and merge all documents
            $allDocuments = [];

            // Add checklist items
            foreach ($checklist as $doc) {
                $allDocuments[$doc['name']] = [
                    'name' => $doc['name'] ?? null,
                    'completed' => $doc['completed'] ?? 0,
                    'file_path' => null,
                    'uploaded_at' => null,
                ];
            }

            // Add/update uploaded items (prevent duplicates by document name)
            foreach ($uploaded as $doc) {
                $uploadedAt = $doc['uploadedAt'] ?? null;

                if ($uploadedAt) {
                    $uploadedAt = trim($uploadedAt);
                    try {
                        // Parse DD/MM/YYYY, HH:MM:SS format
                        $date = Carbon::createFromFormat('d/m/Y, H:i:s', $uploadedAt);
                        $uploadedAt = $date->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $uploadedAt = null;
                        \Log::warning("Invalid uploaded_at date: {$doc['uploadedAt']}");
                    }
                }

                // Update existing checklist item or create new one
                $allDocuments[$doc['name']] = [
                    'name' => $doc['name'] ?? null,
                    'completed' => 1,
                    'file_path' => $doc['file_path'] ?? null,
                    'uploaded_at' => $uploadedAt,
                ];
            }

            // Store all documents
            foreach ($allDocuments as $document) {
                BiddingDocument::create([
                    'prebidding_id' => $prebidding->id,
                    'name' => $document['name'],
                    'completed' => $document['completed'],
                    'file_path' => $document['file_path'],
                    'uploaded_at' => $document['uploaded_at'],
                ]);
            }

            // Store material planning
            if (!empty($relatedData['material_planning'])) {
                foreach ($relatedData['material_planning'] as $material) {
                    MaterialPlanning::create([
                        'name' => $material['name'] ?? null,
                        'brand' => $material['brand'] ?? null,
                        'unit' => $material['unit'] ?? null,
                        'quantity' => $material['quantity'] ?? 0,
                        'available' => $material['available'] ?? 0,
                        'source' => $material['source'] ?? null,
                        'unit_cost' => $material['cost'] ?? 0,
                        'cost_code' => $material['code'] ?? null,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store equipment planning
            if (!empty($relatedData['equipment_planning'])) {
                foreach ($relatedData['equipment_planning'] as $equipment) {
                    EquipmentPlanning::create([
                        'name' => $equipment['name'] ?? null,
                        'brand' => $equipment['brand'] ?? null,
                        'conditions' => $equipment['condition'] ?? null,
                        'type' => $equipment['type'] ?? null,
                        'quantity' => $equipment['quantity'] ?? 0,
                        'available' => $equipment['available'] ?? 0,
                        'unit_cost' => $equipment['cost'] ?? 0,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store labor planning
            if (!empty($relatedData['labor_cost_plan'])) {
                foreach ($relatedData['labor_cost_plan'] as $labor) {
                    LaborPlanning::create([
                        'name' => $labor['name'] ?? null,
                        'role' => $labor['role'] ?? null,
                        'headcount' => $labor['quantity'] ?? 1,
                        'total_cost' => $labor['total'] ?? 0,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store subcontractor planning
            if (!empty($relatedData['subcontractor_cost_plan'])) {
                foreach ($relatedData['subcontractor_cost_plan'] as $subcontractor) {
                    SubcontractorPlanning::create([
                        'name' => $subcontractor['name'] ?? null,
                        'scope' => $subcontractor['scope'] ?? null,
                        'planned_cost' => $subcontractor['cost'] ?? 0,
                        'cost_code' => $subcontractor['code'] ?? null,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store miscellaneous planning
            if (!empty($relatedData['miscellaneous_cost_plan'])) {
                foreach ($relatedData['miscellaneous_cost_plan'] as $miscellaneous) {
                    MiscellaneousPlanning::create([
                        'name' => $miscellaneous['name'] ?? null,
                        'planned_cost' => $miscellaneous['cost'] ?? 0,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            // Store category breakdown (merge auto-generated and custom)
            $allCategoryBreakdowns = array_merge(
                $relatedData['category_breakdown'] ?? [],
                $relatedData['category_breakdown_custom'] ?? []
            );

            if (!empty($allCategoryBreakdowns)) {
                foreach ($allCategoryBreakdowns as $category) {
                    CategoryBreakdown::create([
                        'name' => $category['name'] ?? null,
                        'description' => $category['description'] ?? null,
                        'amount' => $category['amount'] ?? 0,
                        'prebidding_id' => $prebidding->id,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Prebidding created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating prebidding',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed prebidding information for pending approval view.
     */
    public function getPendingApprovalsOperationsPrebiddings($encodedId)
    {
        try {
            // Decode the ID from Hashids
            $decodedId = Hashids::decode($encodedId);
            if (empty($decodedId)) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 404);
            }

            $prebiddingId = $decodedId[0];
            
            // Fetch prebidding with all relationships
            $prebidding = Prebidding::with([
                'client',
                'submittedBy',
                'materialPlannings',
                'equipmentPlannings',
                'laborPlannings',
                'subcontractorPlannings',
                'miscellaneousPlannings',
                'biddingActivities',
                'biddingDocuments',
                'categoryBreakdowns',
                'branches'
            ])->find($prebiddingId);

            if (!$prebidding) {
                return response()->json([
                    'message' => 'Prebidding not found',
                ], 404);
            }

            // Format client name
            $clientName = null;
            if ($prebidding->client) {
                $clientName = trim(
                    ($prebidding->client->last_name ?? '') . ', ' . 
                    ($prebidding->client->first_name ?? '') . ' ' . 
                    ($prebidding->client->middle_initial ?? '')
                );
            }

            // Format submitted by name
            $submittedByName = null;
            if ($prebidding->submittedBy) {
                $submittedByName = trim(
                    ($prebidding->submittedBy->last_name ?? '') . ', ' . 
                    ($prebidding->submittedBy->first_name ?? '') . ' ' . 
                    ($prebidding->submittedBy->middle_name ?? '')
                );
            }

            // Calculate cost breakdown from category breakdowns
            $materials_cost = 0;
            $equipment_cost = 0;
            $labor_cost = 0;
            $subcontractor_cost = 0;
            $miscellaneous_cost = 0;

            foreach ($prebidding->categoryBreakdowns as $category) {
                $categoryName = strtolower($category->name ?? '');
                if (strpos($categoryName, 'material') !== false) {
                    $materials_cost += $category->amount;
                } elseif (strpos($categoryName, 'equipment') !== false) {
                    $equipment_cost += $category->amount;
                } elseif (strpos($categoryName, 'labor') !== false) {
                    $labor_cost += $category->amount;
                } elseif (strpos($categoryName, 'subcontractor') !== false) {
                    $subcontractor_cost += $category->amount;
                } else {
                    $miscellaneous_cost += $category->amount;
                }
            }

            return response()->json([
                'prebidding' => [
                    'id' => $encodedId,
                    'project_name' => $prebidding->name,
                    'project_code' => $prebidding->code,
                    'project_type' => $prebidding->type,
                    'address' => $prebidding->address,
                    'client_name' => $clientName,
                    'submitted_by' => $submittedByName,
                    'deadline' => $prebidding->bid_opening,
                    'total_budget' => $prebidding->total_budget,
                    'contract_amount' => $prebidding->contract_amount,
                    'billing_type' => $prebidding->billing_type,
                    'retention' => $prebidding->retention,
                    'materials_cost' => $materials_cost,
                    'equipment_cost' => $equipment_cost,
                    'labor_cost' => $labor_cost,
                    'subcontractor_cost' => $subcontractor_cost,
                    'miscellaneous_cost' => $miscellaneous_cost,
                    'status' => $prebidding->status,
                    'description' => $prebidding->notes,
                    'submitted_at' => $prebidding->created_at,
                    'document_count' => $prebidding->biddingDocuments->count(),
                    'branches' => $prebidding->branches->pluck('name')->toArray(),
                    'image' => $prebidding->image ? asset('storage/' . $prebidding->image) : null,
                ],
                'materials' => $prebidding->materialPlannings->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'brand' => $material->brand,
                        'unit' => $material->unit,
                        'quantity' => $material->quantity,
                        'unit_cost' => $material->unit_cost,
                        'source' => $material->source,
                    ];
                })->values()->toArray(),
                'equipment' => $prebidding->equipmentPlannings->map(function ($equipment) {
                    return [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'brand' => $equipment->brand,
                        'condition' => $equipment->conditions,
                        'type' => $equipment->type,
                        'quantity' => $equipment->quantity,
                        'unit_cost' => $equipment->unit_cost,
                    ];
                })->values()->toArray(),
                'labor' => $prebidding->laborPlannings->map(function ($labor) {
                    return [
                        'id' => $labor->id,
                        'role' => $labor->role,
                        'personnel' => $labor->name,
                        'cost' => $labor->total_cost,
                    ];
                })->values()->toArray(),
                'subcontractors' => $prebidding->subcontractorPlannings->map(function ($subcontractor) {
                    return [
                        'id' => $subcontractor->id,
                        'name' => $subcontractor->name,
                        'scope' => $subcontractor->scope,
                        'cost' => $subcontractor->planned_cost,
                    ];
                })->values()->toArray(),
                'documents' => [
                    'checklist' => $prebidding->biddingDocuments->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'completed' => $doc->completed,
                            'type' => $doc->type,
                        ];
                    })->values()->toArray(),
                    'uploaded' => $prebidding->biddingDocuments->filter(function ($doc) {
                        return $doc->completed;
                    })->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'type' => $doc->type,
                            'uploaded_at' => $doc->uploaded_at,
                            'file_path' => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                        ];
                    })->values()->toArray(),
                ],
                'bid_activities' => $prebidding->biddingActivities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'description' => $activity->description,
                        'date' => $activity->date,
                        'time' => $activity->time,
                    ];
                })->values()->toArray(),
                'bid_details' => [
                    'opening_date' => $prebidding->bid_opening,
                    'bid_type' => $prebidding->bid_type,
                    'bond_type' => $prebidding->bond_type,
                    'security_amount' => $prebidding->security_amount,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching prebidding details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPendingApprovalsFinancePrebiddings($encodedId)
    {
        try {
            // Decode the ID from Hashids
            $decodedId = Hashids::decode($encodedId);
            if (empty($decodedId)) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 404);
            }

            $prebiddingId = $decodedId[0];
            
            // Fetch prebidding with all relationships
            $prebidding = Prebidding::with([
                'client',
                'submittedBy',
                'materialPlannings',
                'equipmentPlannings',
                'laborPlannings',
                'subcontractorPlannings',
                'miscellaneousPlannings',
                'biddingActivities',
                'biddingDocuments',
                'categoryBreakdowns',
                'branches'
            ])->find($prebiddingId);

            if (!$prebidding) {
                return response()->json([
                    'message' => 'Prebidding not found',
                ], 404);
            }

            // Format client name
            $clientName = null;
            if ($prebidding->client) {
                $clientName = trim(
                    ($prebidding->client->last_name ?? '') . ', ' . 
                    ($prebidding->client->first_name ?? '') . ' ' . 
                    ($prebidding->client->middle_initial ?? '')
                );
            }

            // Format submitted by name
            $submittedByName = null;
            if ($prebidding->submittedBy) {
                $submittedByName = trim(
                    ($prebidding->submittedBy->last_name ?? '') . ', ' . 
                    ($prebidding->submittedBy->first_name ?? '') . ' ' . 
                    ($prebidding->submittedBy->middle_name ?? '')
                );
            }

            // Calculate cost breakdown from category breakdowns
            $materials_cost = 0;
            $equipment_cost = 0;
            $labor_cost = 0;
            $subcontractor_cost = 0;
            $miscellaneous_cost = 0;

            foreach ($prebidding->categoryBreakdowns as $category) {
                $categoryName = strtolower($category->name ?? '');
                if (strpos($categoryName, 'material') !== false) {
                    $materials_cost += $category->amount;
                } elseif (strpos($categoryName, 'equipment') !== false) {
                    $equipment_cost += $category->amount;
                } elseif (strpos($categoryName, 'labor') !== false) {
                    $labor_cost += $category->amount;
                } elseif (strpos($categoryName, 'subcontractor') !== false) {
                    $subcontractor_cost += $category->amount;
                } else {
                    $miscellaneous_cost += $category->amount;
                }
            }

            return response()->json([
                'prebidding' => [
                    'id' => $encodedId,
                    'project_name' => $prebidding->name,
                    'project_code' => $prebidding->code,
                    'project_type' => $prebidding->type,
                    'address' => $prebidding->address,
                    'client_name' => $clientName,
                    'submitted_by' => $submittedByName,
                    'deadline' => $prebidding->bid_opening,
                    'total_budget' => $prebidding->total_budget,
                    'contract_amount' => $prebidding->contract_amount,
                    'billing_type' => $prebidding->billing_type,
                    'retention' => $prebidding->retention,
                    'materials_cost' => $materials_cost,
                    'equipment_cost' => $equipment_cost,
                    'labor_cost' => $labor_cost,
                    'subcontractor_cost' => $subcontractor_cost,
                    'miscellaneous_cost' => $miscellaneous_cost,
                    'status' => $prebidding->status,
                    'description' => $prebidding->notes,
                    'submitted_at' => $prebidding->created_at,
                    'document_count' => $prebidding->biddingDocuments->count(),
                    'branches' => $prebidding->branches->pluck('name')->toArray(),
                    'image' => $prebidding->image ? asset('storage/' . $prebidding->image) : null,
                ],
                'materials' => $prebidding->materialPlannings->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'brand' => $material->brand,
                        'unit' => $material->unit,
                        'quantity' => $material->quantity,
                        'unit_cost' => $material->unit_cost,
                        'source' => $material->source,
                    ];
                })->values()->toArray(),
                'equipment' => $prebidding->equipmentPlannings->map(function ($equipment) {
                    return [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'brand' => $equipment->brand,
                        'condition' => $equipment->conditions,
                        'type' => $equipment->type,
                        'quantity' => $equipment->quantity,
                        'unit_cost' => $equipment->unit_cost,
                    ];
                })->values()->toArray(),
                'labor' => $prebidding->laborPlannings->map(function ($labor) {
                    return [
                        'id' => $labor->id,
                        'role' => $labor->role,
                        'personnel' => $labor->name,
                        'cost' => $labor->total_cost,
                    ];
                })->values()->toArray(),
                'subcontractors' => $prebidding->subcontractorPlannings->map(function ($subcontractor) {
                    return [
                        'id' => $subcontractor->id,
                        'name' => $subcontractor->name,
                        'scope' => $subcontractor->scope,
                        'cost' => $subcontractor->planned_cost,
                    ];
                })->values()->toArray(),
                'documents' => [
                    'checklist' => $prebidding->biddingDocuments->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'completed' => $doc->completed,
                            'type' => $doc->type,
                        ];
                    })->values()->toArray(),
                    'uploaded' => $prebidding->biddingDocuments->filter(function ($doc) {
                        return $doc->completed;
                    })->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'type' => $doc->type,
                            'uploaded_at' => $doc->uploaded_at,
                            'file_path' => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                        ];
                    })->values()->toArray(),
                ],
                'bid_activities' => $prebidding->biddingActivities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'description' => $activity->description,
                        'date' => $activity->date,
                        'time' => $activity->time,
                    ];
                })->values()->toArray(),
                'bid_details' => [
                    'opening_date' => $prebidding->bid_opening,
                    'bid_type' => $prebidding->bid_type,
                    'bond_type' => $prebidding->bond_type,
                    'security_amount' => $prebidding->security_amount,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching prebidding details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed prebidding information for pending approval view.
     */
    public function approvePendingApprovalsOperationsPrebiddings($encodedId)
    {
        try {
            // Decode the ID from Hashids
            $decodedId = Hashids::decode($encodedId);
            if (empty($decodedId)) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 404);
            }

            $prebiddingId = $decodedId[0];

            // Update the prebidding status to finance approval
            $prebidding = Prebidding::findOrFail($prebiddingId);
            $prebidding->update(['status' => 'finance_approval']);

            return response()->json([ 
                'message' => "Prebidding approved successfully",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching prebidding details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approvePendingApprovalsFinancePrebiddings($encodedId)
    {
        try {
            // Decode the ID from Hashids
            $decodedId = Hashids::decode($encodedId);
            if (empty($decodedId)) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 404);
            }

            $prebiddingId = $decodedId[0];

            // Update the prebidding status to finance approval
            $prebidding = Prebidding::findOrFail($prebiddingId);
            $prebidding->update(['status' => 'active']);

            return response()->json([ 
                'message' => "Prebidding approved successfully",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching prebidding details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update prebidding documents (checklist and uploaded).
     */
    public function updateDocuments(Request $request, $id)
    {
        try {
            // Validate files in request
            $request->validate([
                'documents.*' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            // Decode the ID from Hashids
            $decodedId = Hashids::decode($id);
            $prebiddingId = $decodedId[0] ?? null;

            if (!$prebiddingId) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 422);
            }

            $prebidding = Prebidding::findOrFail($prebiddingId);

            // Validate that the user has permission to update this prebidding
            if ($prebidding->submitted_by !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            $checklist = $request->input('checklist', []);
            $uploaded = $request->input('uploaded', []);

            if (is_string($checklist)) {
                $decodedChecklist = json_decode($checklist, true);
                $checklist = is_array($decodedChecklist) ? $decodedChecklist : [];
            }

            if (is_string($uploaded)) {
                $decodedUploaded = json_decode($uploaded, true);
                $uploaded = is_array($decodedUploaded) ? $decodedUploaded : [];
            }

            // Handle uploaded document files
            $uploadedDocumentPaths = [];
            $files = $request->file('documents');
            if ($files) {
                $filesArray = is_array($files) ? $files : [$files];
                foreach ($filesArray as $index => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('prebiddings/documents', 'public');
                        $uploadedDocumentPaths[$index] = $path;
                    }
                }
            }

            // Update uploaded documents with file paths
            if (!empty($uploadedDocumentPaths)) {
                foreach ($uploadedDocumentPaths as $index => $path) {
                    if (isset($uploaded[$index])) {
                        $uploaded[$index]['file_path'] = $path;
                    }
                }
            }

            // Clear existing bidding documents
            BiddingDocument::where('prebidding_id', $prebiddingId)->delete();

            // Process and merge all documents
            $allDocuments = [];

            // Add checklist items
            foreach ($checklist as $item) {
                $allDocuments[$item['name']] = [
                    'name' => $item['name'] ?? null,
                    'completed' => !empty($item['completed']) ? 1 : 0,
                    'file_path' => null,
                    'uploaded_at' => null,
                ];
            }

            // Add/update uploaded items (prevent duplicates by document name)
            foreach ($uploaded as $item) {
                $uploadedAt = $item['uploadedAt'] ?? null;
                if ($uploadedAt) {
                    try {
                        $uploadedAt = Carbon::parse($uploadedAt)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $uploadedAt = null;
                    }
                }

                $allDocuments[$item['name']] = [
                    'name' => $item['name'] ?? null,
                    'completed' => 1,
                    'file_path' => $item['filePath'] ?? $item['file_path'] ?? null,
                    'uploaded_at' => $uploadedAt,
                ];
            }



            // Store all documents
            foreach ($allDocuments as $document) {
                BiddingDocument::create([
                    'prebidding_id' => $prebiddingId,
                    'name' => $document['name'],
                    'completed' => $document['completed'],
                    'file_path' => $document['file_path'],
                    'uploaded_at' => $document['uploaded_at'],
                ]);
            }

            return response()->json([
                'message' => 'Documents updated successfully',
                'documents' => array_values($allDocuments),
            ], 200);

        } catch (\Exception $e) {
            if (!empty($uploadedDocumentPaths)) {
                Storage::disk('public')->delete($uploadedDocumentPaths);
            }
            return response()->json([
                'message' => 'Error updating documents',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send prebidding to operations (convert to construction project)
     */
    public function sendToOperations(Request $request, $id)
    {
        try {
            // Validate the notice of award file
            $request->validate([
                'notice_of_award' => 'required|file|mimes:pdf|max:10240',
            ]);

            // Decode the ID from Hashids
            $decodedId = Hashids::decode($id);
            $prebiddingId = $decodedId[0] ?? null;

            if (!$prebiddingId) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 422);
            }

            $prebidding = Prebidding::findOrFail($prebiddingId);

            // Validate that the user has permission to update this prebidding
            if ($prebidding->submitted_by !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Store the notice of award file
            $file = $request->file('notice_of_award');
            $filePath = null;
            
            if ($file && $file->isValid()) {
                $filePath = $file->store('prebiddings/notice_of_award', 'public');
            }

            // Create document name with current timestamp format: notice_of_award_(timestamp)
            $currentTimestamp = now()->format('YmdHis');
            $documentName = "noa_{$currentTimestamp}";

            // Insert into bidding_documents table
            BiddingDocument::create([
                'prebidding_id' => $prebiddingId,
                'name' => $documentName,
                'file_path' => $filePath,
                'completed' => 1,
                'uploaded_at' => now(),
            ]);

            // Update prebidding status to "bidding_won"
            $prebidding->status = 'bidding_won';
            $prebidding->save();

            return response()->json([
                'message' => 'Pre-bidding successfully sent to Operations',
                'prebidding' => [
                    'hashed_id' => $prebidding->hashed_id,
                    'status' => $prebidding->status,
                ],
            ], 200);

        } catch (\Exception $e) {
            // Clean up uploaded file if there was an error
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            return response()->json([
                'message' => 'Error sending to Operations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** 
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Decode the ID from Hashids
            $decodedId = Hashids::decode($id);
            $prebiddingId = $decodedId[0] ?? null;

            if (!$prebiddingId) {
                return response()->json([
                    'message' => 'Invalid prebidding ID',
                ], 422);
            }

            $prebidding = Prebidding::with([
                'client',
                'biddingDocuments',
                'biddingActivities',
                'materialPlannings',
                'equipmentPlannings',
                'laborPlannings',
                'subcontractorPlannings',
                'categoryBreakdowns',
                'branches'
            ])->findOrFail($prebiddingId);

            // Format client name
            $clientName = null;
            if ($prebidding->client) {
                $clientName = trim(
                    ($prebidding->client->last_name ?? '') . ', ' . 
                    ($prebidding->client->first_name ?? '') . ' ' . 
                    ($prebidding->client->middle_initial ?? '')
                );
            }

            // Get supervisor and foreman from labor_planning table
            $supervisor = ['name' => 'N/A', 'cost' => 0];
            $foreman = ['name' => 'N/A', 'cost' => 0];
            
            $laborPlannings = $prebidding->laborPlannings;
            foreach ($laborPlannings as $labor) {
                if (strtolower($labor->role) === 'project supervisor') {
                    $supervisor = ['name' => $labor->name, 'cost' => (float) $labor->total_cost];
                } elseif (strtolower($labor->role) === 'project foreman') {
                    $foreman = ['name' => $labor->name, 'cost' => (float) $labor->total_cost];
                }
            }

            // Calculate cost breakdown from category breakdowns
            $materials_cost = 0;
            $equipment_cost = 0;
            $labor_cost = 0;
            $subcontractor_cost = 0;
            $miscellaneous_cost = 0;

            foreach ($prebidding->categoryBreakdowns as $category) {
                $categoryName = strtolower($category->name ?? '');
                if (strpos($categoryName, 'material') !== false) {
                    $materials_cost += $category->amount;
                } elseif (strpos($categoryName, 'equipment') !== false) {
                    $equipment_cost += $category->amount;
                } elseif (strpos($categoryName, 'labor') !== false) {
                    $labor_cost += $category->amount;
                } elseif (strpos($categoryName, 'subcontractor') !== false) {
                    $subcontractor_cost += $category->amount;
                } else {
                    $miscellaneous_cost += $category->amount;
                }
            }

            // Separate documents into checklist and uploaded
            $documentChecklist = [];
            $documentUploaded = [];
            
            foreach ($prebidding->biddingDocuments as $doc) {
                if ($doc->file_path) {
                    // This is an uploaded document
                    $documentUploaded[] = [
                        'name' => $doc->name,
                        'uploadedAt' => $doc->uploaded_at ? date('M d, Y', strtotime($doc->uploaded_at)) : now()->format('M d, Y'),
                        'fileUrl' => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                        'filePath' => $doc->file_path,
                    ];
                } else {
                    // This is a checklist item
                    $documentChecklist[] = [
                        'name' => $doc->name,
                        'completed' => $doc->completed ?? false,
                    ];
                }
            }

            // Format bidding activities
            $biddingActivities = $prebidding->biddingActivities->map(function ($activity) {
                return [
                    'name' => $activity->name,
                    'date' => $activity->date,
                ];
            })->toArray();

            // Format materials
            $materials = $prebidding->materialPlannings->map(function ($material) {
                return [
                    'name' => $material->name,
                    'brand' => $material->brand,
                    'quantity' => $material->quantity,
                    'unit' => $material->unit,
                    'source' => $material->source,
                ];
            })->toArray();

            // Format equipment
            $equipment = $prebidding->equipmentPlannings->map(function ($equip) {
                return [
                    'name' => $equip->name,
                    'brand' => $equip->brand,
                    'condition' => $equip->conditions,
                    'quantity' => $equip->quantity,
                    'type' => $equip->type,
                ];
            })->toArray();

            // Format subcontractors
            $subcontractors = $prebidding->subcontractorPlannings->map(function ($sub) {
                return [
                    'name' => $sub->name,
                    'scope' => $sub->scope,
                    'cost' => $sub->planned_cost,
                ];
            })->toArray();

            return response()->json([
                'prebidding' => [
                    'id' => Hashids::encode($prebidding->id),
                    'project_name' => $prebidding->name,
                    'project_code' => $prebidding->code,
                    'project_type' => $prebidding->type,
                    'client_name' => $clientName,
                    'address' => $prebidding->address,
                    'notes' => $prebidding->notes,
                    'status' => $prebidding->status,
                    'bid_details' => [
                        'deadline' => $prebidding->bid_deadline,
                        'opening' => $prebidding->bid_opening,
                        'type' => $prebidding->bid_type,
                        'bondType' => $prebidding->bond_type,
                        'amount' => $prebidding->security_amount,
                    ],
                    'bidding_activities' => $biddingActivities,
                    'materials' => $materials,
                    'equipment' => $equipment,
                    'subcontractors' => $subcontractors,
                    'team' => [
                        'supervisor' => $supervisor,
                        'foreman' => $foreman,
                    ],
                    'financial' => [
                        'total_budget' => (float) $prebidding->total_budget,
                        'contract_amount' => (float) $prebidding->contract_amount,
                        'billing_type' => $prebidding->billing_type,
                        'retention' => (float) $prebidding->retention,
                        'materials_cost' => $materials_cost,
                        'equipment_cost' => $equipment_cost,
                        'labor_cost' => $labor_cost,
                        'subcontractor_cost' => $subcontractor_cost,
                        'miscellaneous_cost' => $miscellaneous_cost,
                        'total_budget_set' => (float) $prebidding->total_budget,
                        'total_budget_utilized' => (float) $prebidding->total_utilized,
                    ],
                ],
                'documents' => [
                    'checklist' => $documentChecklist,
                    'uploaded' => $documentUploaded,
                ],
                'scope_changes' => [], // Placeholder as per requirement to ignore scope changes for now
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching prebidding details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all prebiddings with status "bidding_won"
     */
    public function getBiddingsWon()
    {
        try {
            $userBranchIds = auth()->user()->branches()->pluck('branches.id')->toArray();
            
            $biddingsWon = Prebidding::with(['client', 'biddingDocuments', 'branches'])
                ->where('status', 'bidding_won')
                ->whereHas('branches', function ($query) use ($userBranchIds) {
                    $query->whereIn('branches.id', $userBranchIds);
                })
                ->get()
                ->map(function ($prebidding) {
                    // Format client name
                    $clientName = null;
                    if ($prebidding->client) {
                        $clientName = trim(
                            ($prebidding->client->last_name ?? '') . ', ' . 
                            ($prebidding->client->first_name ?? '') . ' ' . 
                            ($prebidding->client->middle_initial ?? '')
                        );
                    }

                    return [
                        'id' => Hashids::encode($prebidding->id),
                        'project_name' => $prebidding->name,
                        'project_code' => $prebidding->code,
                        'client_name' => $clientName,
                        'total_budget' => $prebidding->total_budget,
                        'status' => $prebidding->status,
                        'created_at' => $prebidding->created_at,
                        'updated_at' => $prebidding->updated_at,
                    ];
                });

            return response()->json([
                'biddings_won' => $biddingsWon,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching biddings won',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prebidding $prebidding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prebidding $prebidding)
    {
        //
    }
}
