<?php

namespace App\Service;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly  class NewsletterService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NewsletterRepository $newsletterRepository,
    ){}

    public function createNewsletter(string $email) {
        $newsletter = $this->newsletterRepository->findOneBy(['email' => $email]);

        if ($newsletter) {
            throw new \Exception("Newsletter already exists");
        }

        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        $newsletter->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($newsletter);
        $this->entityManager->flush();

        return $newsletter;
    }

    public function isNewsletterExist(string $email): bool
    {
        return (bool)$this->newsletterRepository->findOneBy(['email' => $email]);
    }

    public function updateNewsletter(User $user): void
    {
        // Récupère la première newsletter associée (si elle existe)
        $newsletter = $user->getNewsletters()->first() ?: null;

        if ($newsletter) {
            // L'utilisateur est déjà abonné → on le désabonne
            $user->removeNewsletter($newsletter);
            $this->entityManager->remove($newsletter);
        } else {
            // L'utilisateur n'est pas abonné → on l'abonne
            $newsletter = new Newsletter();
            $newsletter->setEmail($user->getEmail());
            $newsletter->setCreatedAt(new \DateTimeImmutable());
            $newsletter->setUtilisateur($user);

            $user->addNewsletter($newsletter);
            $this->entityManager->persist($newsletter);
        }

        $this->entityManager->flush();
    }


}
