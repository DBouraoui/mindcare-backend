<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\User;
use App\Enum\BookingStatusType;
use App\Interface\DtoInterface;
use App\Repository\BookingRepository;
use App\Repository\ProRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ProRepository $proRepository,
        private readonly BookingRepository $bookingRepository,
        private readonly UserRepository $userRepository,
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

        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $user;
    }

    public function updateUserPassword(DtoInterface $dto, User $user): User
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $user;
    }

    public function updateEmail(DtoInterface $dto, User $user): User
    {
        $userDB = $this->userRepository->findOneBy(['email' => $dto->email]);

        if ($userDB) {
            Throw new \Exception("Email already in use");
        }

        if ($user->getEmail() !== $dto->email) {
            $user->setEmail($dto->email);
        }

        $user->setUpdatedAt(new \DateTimeImmutable());

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

    public function createBooking(DtoInterface $dto, User $user): Booking
    {
        $pro = $this->proRepository->find($dto->proId);

        if (!$pro) {
            Throw new \Exception('Pro not found');
        }

        $startAt = new \DateTimeImmutable($dto->startAt);
        $endAt = new \DateTimeImmutable($dto->endAt);

        if ($startAt > $endAt) {
            Throw new \Exception('Start date must be after end date');
        }

        $exists = $this->bookingRepository->createQueryBuilder('b')
            ->andWhere('b.pro = :pro')
            ->andWhere('b.startAt < :end')
            ->andWhere('b.endAt > :start')
            ->setParameter('pro', $pro)
            ->setParameter('start', $startAt)
            ->setParameter('end', $endAt)
            ->getQuery()
            ->getResult();

        if ($exists) {
            Throw new \Exception('Booking already exists');
        }

        $booking = new Booking();
        $booking->setPro($pro);
        $booking->setUtilisateur($user);
        $booking->setStartAt($startAt);
        $booking->setEndAt($endAt);
        $booking->setStatus(BookingStatusType::PENDING->value);
        $booking->setNote($dto->note);
        $booking->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    public function deleteBooking(string $id): Booking
    {
       $booking =  $this->bookingRepository->find($id);
       $now = new \DateTimeImmutable();
       $twoDaysAgo = new \DateTimeImmutable('+2 days');

       if ($booking->getStartAt() < $now) {
           Throw new \Exception('The booking is already passed');
       }

       if ($booking->getStartAt() < $twoDaysAgo) {
           Throw new \Exception('The booking must be scheduled at least 2 days in advance.');
       }

       $this->entityManager->remove($booking);
       $this->entityManager->flush();

       return $booking;
    }
}
