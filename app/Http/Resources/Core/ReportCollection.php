<?php

namespace App\Http\Resources\Core;

use App\Models\Client\User;
use App\Models\Core\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ReportCollection extends ResourceCollection
{
    private ?User $user = null;

    /** @var Collection<int, Field>|null */
    private ?Collection $fields = null;

    public function for(?User $user, ?Collection $fields): static
    {
        $this->user = $user;
        $this->fields = $fields;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection
                ->map(fn (ReportResource $resource) => $resource->for($this->user, $this->fields)->toArray($request)),
        ];
    }
}
