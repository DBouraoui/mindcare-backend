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

    public function updateEmail(DtoInterface $dto, User $user): User
    {
        if ($user->getEmail() !== $dto->email) {
            $user->setEmail($dto->email);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function updateProInformation(DtoInterface $dto, User $user): User
    {
        $proInformation = $user->getPro();

        if ($proInformation->getCountry() !== $dto->country) {
            $proInformation->setCountry($dto->country);
        }

        if ($proInformation->getDiplome() !== $dto->diplome) {
            $proInformation->setDiplome($dto->diplome);
        }

        if ($proInformation->getDescription() !== $dto->description) {
            $proInformation->setDescription($dto->description);
        }

        if ($proInformation->getPrice() !== $dto->price) {
            $proInformation->setPrice($dto->price);
        }

        $this->entityManager->flush();

        return $user;
    }
}
