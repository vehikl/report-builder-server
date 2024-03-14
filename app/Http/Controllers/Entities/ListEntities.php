<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Resources\Core\EntityCollection;
use App\Models\Client\User;
use App\Models\Core\Entity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListEntities extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::query()->with(['permissions', 'roles.permissions'])->first();

        $entities = Entity::query()->with('fields')->get();

        return (new EntityCollection($entities))->for($user)->toResponse($request);
    }
}
