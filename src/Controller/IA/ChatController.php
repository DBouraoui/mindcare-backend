<?php

namespace App\Controller\IA;

use App\Entity\Booking;
use App\Entity\Notification;
use App\Entity\User;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Yaml\Yaml;

class ChatController extends AbstractController
{
    public function __construct(
        private readonly AgentInterface $agent,
    ) {}

    #[Route('/api/chat-ai', name: 'app_ia_chat_invoke', methods: ['POST'])]
    public function __invoke(#[CurrentUser] User $user, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (!$userMessage) {
            return $this->json(['error' => 'Aucun message reçu'], 400);
        }

        // Détecte si la question concerne les données utilisateur
        $keywords = ['booking', 'notification', 'compte', 'pro', 'favoris'];
        $includeData = false;
        foreach ($keywords as $kw) {
            if (str_contains(strtolower($userMessage), $kw)) {
                $includeData = true;
                break;
            }
        }

        $dynamicContext = '';
        if ($includeData) {
            // Bookings
            $bookings = $user->getBookings()->map(fn(Booking $b) => [
                'note' => $b->getNote(),
                'status' => $b->getStatus(),
                'rdv_date'=> $b->getStartAt()->format('Y-m-d h:m:i')." ". $b->getEndAt()->format('Y-m-d h:m:i'),
                'user_lastname'=> $b->getUtilisateur()->getLastname(),
                'pro_lastname'=> $b->getPro()->getUtilisateur()->getLastname(),
            ]);
            $bookingText = implode("\n", $bookings->map(fn($b) => sprintf(
                "Booking #%d: note=%s, status=%s, user=%s, pro=%s",
                $b['id'], $b['note'], $b['status'], $b['user_lastname'], $b['pro_lastname']
            ))->toArray());

            // Notifications
            $notifications = $user->getNotifications()->map(fn(Notification $n) => [
                'title'=> $n->getTitle(),
                'description'=> $n->getDescription(),
                'created_at'=> $n->getCreatedAt()?->format('Y-m-d H:i:s'),
                'read_at'=> $n->getReadAt()?->format('Y-m-d H:i:s'),
            ]);
            $notificationText = implode("\n", $notifications->map(fn($n) => sprintf(
                "Notification #%d: title=%s, desc=%s, created_at=%s, read_at=%s",
                $n['id'], $n['title'], $n['description'], $n['created_at'], $n['read_at']
            ))->toArray());

            // User info
            $userText = sprintf(
                "User #%d: %s %s, email=%s, phone=%s, city=%s",
                $user->getId(),
                $user->getFirstname(),
                $user->getLastname(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getCity()
            );

            $dynamicContext = implode("\n", array_filter([
                $bookingText,
                $notificationText,
                $userText
            ]));
        }

        // Contexte statique YAML
        $config = Yaml::parseFile($this->getParameter('kernel.project_dir').'/config/ai/context-ai.yaml');
        $staticContext = implode("\n",$config['context']['role'] ?? []);
        $rulesContext = implode("\n", $config['context']['rules'] ?? []);

        $fullContext = $staticContext . "\n" . $rulesContext;
        if ($dynamicContext) {
            $fullContext .= "\n\nDonnées utilisateur pertinentes :\n" . $dynamicContext;
        }

        $messages = new MessageBag(
            Message::forSystem($fullContext),
            Message::ofUser($userMessage)
        );

        $result = $this->agent->call($messages);

        return $this->json([
            'result' => $result->getContent(),
        ]);
    }
}
