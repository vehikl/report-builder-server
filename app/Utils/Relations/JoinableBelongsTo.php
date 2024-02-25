<?php

namespace App\Utils\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinableBelongsTo extends BelongsTo implements Joinable
{
    use IsJoinable;
}
