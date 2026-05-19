<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\PermissionRole
 *
 * @mixin IdeHelperPermissionRole
 */
class PermissionRole extends Pivot
{
    use HasFactory;
}
