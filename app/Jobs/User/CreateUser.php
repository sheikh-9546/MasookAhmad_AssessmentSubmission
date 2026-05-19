<?php

namespace App\Jobs\User;

use App\Enums\RoleType;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUser extends AbstractBaseUser
{
    public function __construct(
        protected string $email,
        protected string $firstName,
        protected string $lastName,
        protected ?string $phone,
        protected string $country,
        protected RoleType $roleType,
    ) {}

    public static function fromRequest(CreateUserRequest $request, RoleType $roleType): static
    {
        return new static(
            $request->getEmail(),
            $request->getFirstName(),
            $request->getLastName(),
            $request->getPhone(),
            $request->getCountry(),
            $roleType,
        );
    }

    private function make(): static
    {
        $this->user = new User;

        return $this;
    }

    // private function setPasswordToken(): static
    // {
    //     $createdUser                              = $this->user;
    //     $token                                    = Password::getRepository()->create($createdUser);
    //     $this->user->onboard_password_reset_token = base64_encode($token);
    //     $this->user->update();

    //     return $this;
    // }

    // protected function notify(): static
    // {
    //     $token = $this->user->onboard_password_reset_token;
    //     $this->user->notify(new SetPasswordEmailNotification($token, $this->user));

    //     return $this;
    // }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        return DB::transaction(function () {
            return $this->make()
                ->getRole($this->roleType)
                ->setAttribute('email', $this->email)
                ->setAttribute('first_name', $this->firstName)
                ->setAttribute('last_name', $this->lastName)
                ->setAttribute('phone', $this->phone)
                ->setAttribute('country_code', $this->country)
                ->tempPassword()
                ->createUser()
                ->attachRoles()
                ->get();
        });
    }
}
