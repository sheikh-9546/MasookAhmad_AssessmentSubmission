<?php

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccessTokenCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public User $user)
    {
        //
    }
}
