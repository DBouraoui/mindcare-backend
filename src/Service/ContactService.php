<?php

namespace App\Service;

use App\Entity\Contact;
use App\Interface\DtoInterface;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ContactService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContactRepository $contactRepository,
    ){}

    public function createContact(DtoInterface $dto): Contact {
        $contact = new Contact();
        $contact->setEmail($dto->email);
        $contact->setTitle($dto->title);
        $contact->setMessage($dto->message);
        $contact->setType($dto->type);
        $contact->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }
}
