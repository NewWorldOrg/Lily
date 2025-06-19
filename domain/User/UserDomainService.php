<?php

declare(strict_types=1);

namespace Domain\User;

use Domain\Common\RawPassword;
use Domain\Common\Token;
use Domain\Exception\DuplicateEntryException;
use Domain\MedicationHistory\UserId;
use Domain\User\DefinitiveRegisterToken\DefinitiveRegisterTokenRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Infra\Discord\DiscordBotClient;

class UserDomainService
{
    public function __construct(
        private DefinitiveRegisterTokenRepository $definitiveRegisterTokenRepository,
        private Hasher $hasher,
        private DiscordBotClient $discordBotClient,
    ) {
    }

    public function getUserById(Id $id): User
    {
        return $this->userRepository->get($id);
    }

    public function getUserByUserId(UserId $userId): User
    {
        return $this->userRepository->getUserByUserId($userId);
    }

    public function userPasswordRegister(
        UserId $userId,
        RawPassword $password,
    ): bool {
        $user = $this->userRepository->getUserByUserId($userId);

        if (!$user->getStatus()->isUnregistered()) {
            throw new DuplicateEntryException();
        }

        $result = $this->userRepository->userRegister(
            $user->getId(),
            $password->hash($this->hasher),
        );

        $token = $this->definitiveRegisterTokenRepository->create($user->getId());
        $this->discordBotClient->sendDefinitiveRegisterUrlByDm($user->getUserId(), $token);

        return $result;
    }

    public function definitiveRegister(Token $token): bool
    {
        $definitiveRegisterToken = $this->definitiveRegisterTokenRepository->tokenPutToUse($token);
        return $this->userRepository->definitiveRegister($definitiveRegisterToken->getUserId());
    }
}
