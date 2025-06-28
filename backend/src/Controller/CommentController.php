<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\AccommodationRepository;
use App\Repository\ClientRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/comments')]
#[OA\Tag(name: 'Comments')]
class CommentController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CommentRepository $commentRepository;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        ValidatorInterface $validator,
    ) {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->validator = $validator;
    }

    #[Route('', name: 'app_comment_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $comments = $this->commentRepository->findAll();
        $data = [];

        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'rating' => $comment->getRating(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'client' => [
                    'id' => $comment->getClient()->getId(),
                ],
                'accommodation' => [
                    'id' => $comment->getAccommodation()->getId(),
                ],
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'rating' => $comment->getRating(),
            'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'client' => [
                'id' => $comment->getClient()->getId(),
            ],
            'accommodation' => [
                'id' => $comment->getAccommodation()->getId(),
            ],
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('', name: 'app_comment_create', methods: ['POST'])]
    public function create(
        Request $request,
        ClientRepository $clientRepository,
        AccommodationRepository $accommodationRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['content']) || !isset($data['rating']) || !isset($data['clientId']) || !isset($data['accommodationId'])) {
            return new JsonResponse(['message' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        $client = $clientRepository->find($data['clientId']);
        $accommodation = $accommodationRepository->find($data['accommodationId']);

        if (!$client || !$accommodation) {
            return new JsonResponse(['message' => 'Client ou hébergement non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setRating($data['rating']);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setClient($client);
        $comment->setAccommodation($accommodation);

        $errors = $this->validator->validate($comment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $comment->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_comment_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
    ): JsonResponse {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['content'])) {
            $comment->setContent($data['content']);
        }

        if (isset($data['rating'])) {
            $comment->setRating($data['rating']);
        }

        $errors = $this->validator->validate($comment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Commentaire mis à jour avec succès'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Commentaire supprimé avec succès'], Response::HTTP_OK);
    }

    #[Route('/accommodation/{accommodationId}', name: 'app_comments_by_accommodation', methods: ['GET'])]
    public function getCommentsByAccommodation(int $accommodationId, AccommodationRepository $accommodationRepository): JsonResponse
    {
        $accommodation = $accommodationRepository->find($accommodationId);

        if (!$accommodation) {
            return new JsonResponse(['message' => 'Hébergement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $comments = $this->commentRepository->findBy(['accommodation' => $accommodation], ['createdAt' => 'DESC']);

        $data = [];
        foreach ($comments as $comment) {
            $client = $comment->getClient();

            $data[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'rating' => $comment->getRating(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'client' => [
                    'id' => $client->getId(),
                    'firstName' => $client->getFirstName(),
                    'lastName' => $client->getLastName(),
                ],
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
