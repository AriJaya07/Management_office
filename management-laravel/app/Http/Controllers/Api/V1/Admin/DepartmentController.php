<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepartmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $departments = Department::query()
            ->with(['manager', 'positions'])
            ->withCount('users')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->string('search')}%");
            })
            ->orderBy('name')
            ->paginate($request->integer('per_page', 10));

        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = Department::create($request->validated());

        return response()->json([
            'message' => 'Department created successfully',
            'data' => new DepartmentResource($department->load(['manager', 'positions'])->loadCount('users')),
        ], 201);
    }

    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($department->load(['manager', 'positions'])->loadCount('users'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $department->update($request->validated());

        return response()->json([
            'message' => 'Department updated successfully',
            'data' => new DepartmentResource($department->load(['manager', 'positions'])->loadCount('users')),
        ]);
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
