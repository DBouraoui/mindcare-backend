<?php

namespace App\Controller\Auth;

use App\DTO\auth\ProfessionelRegisterDto;
use App\DTO\auth\UserRegisterDto;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Event\RateLimiterEvent;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles user registration.
 *
 * Applies rate limiting, maps and validates incoming data,
 * and delegates user creation to the AuthService.
 *
 * @author DylanBro
 */
final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly AuthService              $authService,
        private readonly UtilitaireService        $utilService,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * User registration endpoint.
     *
     * Accepts user data, applies IP-based rate limit, validates input,
     * and creates the user account.
     */
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Trigger rate limiter to prevent abuse
            $this->dispatcher->dispatch(
                new RateLimiterEvent($request->getClientIp())
            );

            // Decode and validate the registration data
            $requestData = json_decode($request->getContent());
            $registerDto = $this->utilService->mapAndValidateRequestDto(
                $requestData,
                new UserRegisterDto()
            );

            // Create user and generate token
           $user =  $this->authService->createUser($registerDto);

            $this->logger->log(1,sprintf("%s register", $user->getEmail()));

            $this->dispatcher->dispatch(new NotificationCreateEvent(
                "Bienvenu sur mindcare !",
                "votre compte est bien activée",
                NotificationType::SIMPLE->value,
                $user
            ));

            return $this->json('success', Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * User PRO registration endpoint.
     *
     * Accepts user data, applies IP-based rate limit, validates input,
     * and creates the user pro account.
     */
    #[Route('/api/register-pro', name: 'app_register_pro', methods: ['POST'])]
    public function registerProfessionel(Request $request): JsonResponse {
        try {
            // Decode and validate the registration data
            $requestData = json_decode($request->getContent());

            $registerDto = $this->utilService->mapAndValidateRequestDto(
                $requestData,
                new UserRegisterDto()
            );

            // Create user and generate token
            $user =  $this->authService->createUser($registerDto, true);

            // Validate Profesionel data
            $proDto = $this->utilService->mapAndValidateRequestDto(
                $requestData,
                new ProfessionelRegisterDto()
            );

            // Create professionel
            $this->authService->createPro($proDto, $user);

            //create schedules
            $this->authService->createSchedules($user);

            // LOG creation of pro
            $this->logger->log(1,sprintf("%s register in pro", $user->getEmail()));

            $this->dispatcher->dispatch(new NotificationCreateEvent(
                "Bienvenu sur mindcare !",
                "votre compte professionel est bien activée",
                NotificationType::SIMPLE->value,
                $user
            ));

            return $this->json('success', Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
