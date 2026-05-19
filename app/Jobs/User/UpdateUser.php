<?php

namespace App\Jobs\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUser extends AbstractBaseUser
{
    public function __construct(
        protected string $firstName,
        protected string $lastName,
        protected ?string $phone,
        protected string $country,
        protected User $user,
    ) {}

    public static function fromRequest(UpdateUserRequest $request, User $user): static
    {
        return new static(
            $request->getFirstName(),
            $request->getLastName(),
            $request->getPhone(),
            $request->getCountry(),
            $user,
        );
    }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        return DB::transaction(function () {
            return $this->setAttribute('first_name', $this->firstName)
                ->setAttribute('last_name', $this->lastName)
                ->setAttribute('phone', $this->phone)
                ->setAttribute('country_code', $this->country)
                ->createUser()
                ->get();
        });
    }
}
