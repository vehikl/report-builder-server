<?php

namespace App\Http\Resources\Core;

use App\Models\Client\User;
use App\Models\Core\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityResource extends JsonResource
{
    private ?User $user = null;

    public function for(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $fields = $this->user === null ?
            $this->fields :
            $this->fields
                ->filter(fn (Field $field) => $field->canAccess('view', $this->user))
                ->values();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'fields' => $fields,
        ];
    }
}
