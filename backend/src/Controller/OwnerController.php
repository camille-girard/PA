<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Repository\OwnerRepository;
use App\Service\ValidationErrorFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/owners', name: 'api_owners_')]
#[OA\Tag(name: 'Owners')]
final class OwnerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OwnerRepository $ownerRepository,
        private ValidatorInterface $validator,
        private ValidationErrorFormatterService $errorFormatter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $owners = $this->ownerRepository->findBy(['isDeleted' => false]);

        $json = $serializer->serialize($owners, 'json', ['groups' => ['owner:read', 'accommodation:read', 'booking:read']]);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, SerializerInterface $serializer): JsonResponse
    {
        $owner = $this->ownerRepository->find($id);

        if (!$owner) {
            return $this->json(['message' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($owner, 'json', [
            'groups' => ['owner:read', 'accommodation:read', 'booking:read'],
        ]);

        return JsonResponse::fromJsonString($json, Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'email', 'phone'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->json([
                'message' => 'Missing required fields',
                'missingFields' => $missingFields,
            ], Response::HTTP_BAD_REQUEST);
        }

        $owner = new Owner();
        $owner->setFirstName($data['firstName']);
        $owner->setLastName($data['lastName']);
        $owner->setEmail($data['email']);
        $owner->setPhone($data['phone']);

        $errors = $this->validator->validate($owner);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->persist($owner);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Owner successfully created',
            'owner' => $owner,
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $owner = $this->ownerRepository->find($id);

        if (!$owner) {
            return $this->json(['message' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['firstName'])) {
            $owner->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $owner->setLastName($data['lastName']);
        }

        if (isset($data['email'])) {
            $owner->setEmail($data['email']);
        }

        if (isset($data['phone'])) {
            $owner->setPhone($data['phone']);
        }

        if (isset($data['address'])) {
            $owner->setAddress($data['address']);
        }

        if (isset($data['bio'])) {
            $owner->setBio($data['bio']);
        }

        if (isset($data['isVerified'])) {
            $owner->setIsVerified($data['isVerified']);
        }

        $errors = $this->validator->validate($owner);
        if (count($errors) > 0) {
            return $this->errorFormatter->createValidationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Owner successfully updated',
            'owner' => $owner,
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $owner = $this->ownerRepository->find($id);

        if (!$owner) {
            return $this->json(['message' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $owner->setIsDeleted(true);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Owner successfully archived',
        ], Response::HTTP_OK);
    }
}
