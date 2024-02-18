<?php

namespace App\Models\Data;

use App\Utils\Sql\HasExtendedRelationships;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;

class DataModel extends Model
{
    use HasExtendedRelationships;
}
