<?php

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmail implements Rule
{
    public function __construct(private $ignoreUserId) {}

    public function passes($attribute, $value)
    {
        return ! User::where('email', $value)
            ->whereNull('deleted_at')
            ->where('id', '<>', $this->ignoreUserId)
            ->exists();
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
