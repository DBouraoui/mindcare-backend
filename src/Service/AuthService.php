<?php

namespace App\Service;

use App\Entity\Pro;
use App\Entity\SchedulesPro;
use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\TokenType;
use App\Interface\DtoInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPStan\Parallel\Schedule;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class AuthService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UtilitaireService           $utilitaireService,
    ) {}

    /**
     * Crée un utilisateur avec un token de validation par e-mail.
     */
    public function createUser(DtoInterface $dto, bool $isPro = false): User
    {
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email])) {
            throw new \Exception("User already exists");
        }

        $now = new \DateTimeImmutable();

        $user = (new User());
        $user->setEmail($dto->email)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password))
            ->setFirstname($dto->firstname)
            ->setLastname($dto->lastname)
            ->setPhone($dto->phone)
            ->setCity($dto->city)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setRoles( $isPro ? ['ROLE_PRO', 'ROLE_USER'] :['ROLE_USER'])
            ->setIsActive(false);

        $token = (new UserToken())
            ->setType(TokenType::REGISTER)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+2 hours'))
            ->setToken(Uuid::v4())
            ->setRelatedUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $this->utilitaireService->sendEmail(
            "Welcome in my app !",
            $user->getEmail(),
            "Auth/Welcome",
            [
                "token_expiration" => $token->getExpiredAt(),
                "user" => $user,
                "validate_link" => $_ENV['FRONT_URL'] . "/validate-email/" . $token->getToken(),
            ]
        );

        return $user;
    }

    public function createPro(DtoInterface $dto, User $user): User {
        $now = new \DateTimeImmutable();
        $pro =  (new Pro())
            ->setPrice($dto->price_pro)
            ->setCountry($dto->country_pro)
            ->setDescription($dto->description_pro)
            ->setDiplome($dto->diplome_pro)
            ->setTitle($dto->title_pro)
            ->setSiren($dto->siren_pro)
            ->setSiret($dto->siret_pro)
            ->setAddress($dto->address_pro)
            ->setCity($dto->city_pro)
            ->setEmail($dto->email_pro)
            ->setPhone($dto->phone_pro)
            ->setUpdatedAt($now)
            ->setUtilisateur($user)
            ->setCreatedAt($now);

        $this->entityManager->persist($pro);
        $this->entityManager->flush();

        return $user;
    }

    public function createSchedules(User $user): void
    {
        $this->entityManager->refresh($user);

        if (!$user->getPro()) {
            throw new \RuntimeException('Le user n’a pas de profil Pro associé.');
        }

        $days = [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi',
            'Dimanche',
        ];

        foreach ($days as $day) {
            $schedule = new SchedulesPro();
            $schedule->setPro($user->getPro());
            $schedule->setDay($day);
            $schedule->setMorningStart(null);
            $schedule->setMorningEnd( null);
            $schedule->setAfternoonStart( null);
            $schedule->setAfternoonEnd(null);
            $schedule->setClosed("true");
            $schedule->setUpdatedAt(new \DateTimeImmutable());

            $user->getPro()->addSchedulesPro($schedule);

            $this->entityManager->persist($schedule);
        }

        $this->entityManager->flush();
    }


    /**
     * Confirme l'adresse e-mail de l'utilisateur avec un token.
     */
    public function confirmEmail(string $token, string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (empty($user) ||
            $user->getUserToken()->getToken()->toRfc4122() !== $token ||
            $user->getUserToken()->getType() !== TokenType::REGISTER) {
            throw new \Exception("User not found");
        }

        $this->entityManager->remove($user->getUserToken());
        $user->setIsActive(true);
        $this->entityManager->flush();

        $this->utilitaireService->sendEmail(
            "Your email is confirmed !",
            $user->getEmail(),
            "Auth/ConfirmEmail",
            [
                "user" => $user,
            ]
        );

        return $user;
    }

    /**
     * Gère la demande de mot de passe oublié.
     */
    public function forgetPassword(DtoInterface $dto): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);

        if (!$user) {
            throw new \Exception("User not found");
        }

        $now = new \DateTimeImmutable();
        $token = $user->getUserToken();

        if ($token) {
            if ($token->getType() === TokenType::FORGET_PASSWORD && $token->getExpiredAt() < $now) {
                $token = $this->updateTokenUser($token, $now);
                $this->sendForgetPasswordEmail($user, $token, "Auth/TokenRefresh");
                return;
            }

            if ($token->getType() !== TokenType::FORGET_PASSWORD) {
                throw new \Exception("A different token is already active for this user.");
            }

            $this->entityManager->remove($token);
            $this->entityManager->flush();
        }

        $token = $this->createTokenUser($user, $now);
        $this->sendForgetPasswordEmail($user, $token, "Auth/ForgetPassword");
    }

    /**
     * Met à jour le mot de passe d'un utilisateur via un token valide.
     */
    public function updatePassword(DtoInterface $dto): User
    {
        $userToken = $this->entityManager->getRepository(UserToken::class)->findOneBy(['token' => $dto->token]);

        if (empty($userToken)) {
            throw new \Exception("User not found");
        }

        $user = $userToken->getRelatedUser();
        $updatedUser = $this->updatePasswordUser($user, $dto);

        $this->deleteTokenUser($userToken);

        $this->utilitaireService->sendEmail(
            "Your email was changed",
            $updatedUser->getEmail(),
            "Auth/UpdatePassword",
            [
                "user" => $updatedUser,
            ]
        );

        return $updatedUser;
    }

    /**
     * Crée un nouveau token de type FORGET_PASSWORD pour un utilisateur.
     */
    private function createTokenUser(User $user, \DateTimeImmutable $now): UserToken
    {
        $userToken = (new UserToken())
            ->setToken(Uuid::v4())
            ->setType(TokenType::FORGET_PASSWORD)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+2 hours'))
            ->setRelatedUser($user);

        $this->entityManager->persist($userToken);
        $this->entityManager->flush();

        return $userToken;
    }

    /**
     * Met à jour la date d’expiration d’un token existant.
     */
    private function updateTokenUser(UserToken $userToken, \DateTimeImmutable $now): UserToken
    {
        $userToken->setExpiredAt($now->modify('+2 hours'));
        $this->entityManager->persist($userToken);
        $this->entityManager->flush();

        return $userToken;
    }

    /**
     * Supprime un token utilisateur.
     */
    private function deleteTokenUser(UserToken $userToken): void
    {
        $this->entityManager->remove($userToken);
        $this->entityManager->flush();
    }

    /**
     * Envoie l’e-mail de réinitialisation de mot de passe.
     */
    private function sendForgetPasswordEmail(User $user, UserToken $token, string $template): void
    {
        $this->utilitaireService->sendEmail(
            "Change your password",
            $user->getEmail(),
            $template,
            [
                'user' => $user,
                'validate_link' => $_ENV['FRONT_URL'] . "/forget-password/" . $token->getToken(),
                'token_expiration' => $token->getExpiredAt(),
            ]
        );
    }

    /**
     * Met à jour le mot de passe hashé d’un utilisateur.
     */
    private function updatePasswordUser(User $user, DtoInterface $dto): User
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
