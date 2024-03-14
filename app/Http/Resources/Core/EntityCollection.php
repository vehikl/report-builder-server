<?php

namespace App\Http\Resources\Core;

use App\Models\Client\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EntityCollection extends ResourceCollection
{
    private ?User $user = null;

    public function for(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection
                ->map(fn (EntityResource $resource) => $resource->for($this->user)->toArray($request)),
        ];
    }
}
