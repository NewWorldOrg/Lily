<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\User;
use Domain\Common\RawPassword;
use Domain\Exception\NotFoundException;
use Domain\MedicationHistory\UserId;
use Domain\User\Id;
use Domain\User\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as AuthUserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class UserProvider implements AuthUserProvider
{
    public function __construct(
        private UserRepository $userRepository,
        private Hasher $hasher,
    ) {
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    public function retrieveById($identifier)
    {
        return new User($this->userRepository->get(new Id((int)$identifier)));
    }

    public function retrieveByCredentials(array $credentials): User | null
    {
        if (!isset($credentials['user_id'])) {
            return null;
        }

        try {
            $userDomain = $this->userRepository->getUserByUserId(
                new UserId($credentials['user_id'])
            );

            return new User($userDomain);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function retrieveByToken($identifier, $token)
    {
        // do noting
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // do noting
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user->checkPassword($this->hasher, new RawPassword($credentials['password']));
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // do noting
    }
}
