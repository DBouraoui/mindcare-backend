<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\DtoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ){}
    public function updateInformation(DtoInterface $dto, User $user): User {

        if ($user->getCity() !== $dto->city) {
            $user->setCity($dto->city);
        }

        if ($user->getPhone() !== $dto->phone) {
            $user->setPhone($dto->phone);
        }

        if ($user->getFirstname() !== $dto->firstname) {
            $user->setFirstname($dto->firstname);
        }

        if ($user->getLastname() !== $dto->lastname) {
            $user->setLastname($dto->lastname);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function updateUserPassword(DtoInterface $dto, User $user): User
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
