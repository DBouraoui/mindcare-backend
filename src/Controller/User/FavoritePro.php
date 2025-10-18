<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class FavoritePro extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ){

    }

    #[Route('/api/favorite-pro', name: 'favorite_pro', methods: ['GET'])]
    public function create(
        #[CurrentUser]User $user,
        #[MapQueryParameter] string $proId): Response
    {
        try {

            $this->userService->createFavoritePro(intval($proId), $user);

            return $this->json(['success' => true]);
        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/favorite-pro', name: 'favorite_pro_delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser]User $user,
        #[MapQueryParameter] string $proId): Response
    {
        try {

            $this->userService->deleteFavoritePro(intval($proId), $user);

            return $this->json(['success' => true]);
        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/favorite-pro/{id}', name: 'favorite_pro_delete_by_id', methods: ['DELETE'])]
    public function deleteById(
        #[CurrentUser]User $user, $id): Response
    {
        try {

            $this->userService->deleteFavoriteProById($id);

            return $this->json(['success' => true]);
        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/api/favorite', name: 'favorite_pro_get_all', methods: ['GET'])]
    public function getAll(
        #[CurrentUser]User $user): Response
    {
        try {

            $favorites = $user->getFavorites();

            $array = [];
            foreach($favorites as $favorite) {
                $array[] = [
                    'id' => $favorite->getId(),
                    'idPro' => $favorite->getPro()->getId(),
                    'firstname'=> $favorite->getPro()->getUtilisateur()->getFirstname(),
                    'lastname' => $favorite->getPro()->getUtilisateur()->getLastname(),
                    'title' => $favorite->getPro()->getTitle(),
                    'city'=> $favorite->getPro()->getCity(),
                    'address' => $favorite->getPro()->getAddress(),
                    'createdAt' =>$favorite->getCreatedAt()->format(\DateTime::ATOM)
                ];
            }

            return $this->json($array);
        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
