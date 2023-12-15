<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListEntities extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $entities = Entity::query()->with('attributes')->get();

        return JsonResource::collection($entities)->toResponse($request);
    }
}