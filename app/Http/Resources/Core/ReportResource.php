<?php

namespace App\Http\Resources\Core;

use App\Models\Client\User;
use App\Models\Core\Column;
use App\Models\Core\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ReportResource extends JsonResource
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
        $columns = $this->user && $this->fields === null ?
            $this->columns :
            $this->columns
                ->filter(fn (Column $column) => $column->canAccess('view', $this->user, $this->fields))
                ->values();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'entity_id' => $this->entity_id,
            'columns' => $columns,
        ];
    }
}
