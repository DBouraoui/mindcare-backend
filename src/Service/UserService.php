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

        if ($proInformation->getPhone() !== $dto->phone) {
            $proInformation->setPhone($dto->phone);
        }

        if ($proInformation->getAddress() !== $dto->address) {
            $proInformation->setAddress($dto->address);
        }

        if ($proInformation->getCity() !== $dto->city) {
            $proInformation->setCity($dto->city);
        }

        if ($proInformation->getSiren() !== $dto->siren) {
            $proInformation->setSiren($dto->siren);
        }

        if ($proInformation->getSiret() !== $dto->siret) {
            $proInformation->setSiret($dto->siret);
        }

        if ($proInformation->getTitle() !== $dto->title) {
            $proInformation->setTitle($dto->title);
        }

        if ($proInformation->getEmail() !== $dto->email) {
            $proInformation->setEmail($dto->email);
        }

        $proInformation->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): User {
        $user->setDeletedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $user;
    }
}
