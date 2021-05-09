<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['GET'])]
    public function listContact(ContactRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $listContact = $serializer->serialize($repo->findAll(), 'json');
        return new JsonResponse($listContact, 200, [], true);
    }

    
    #[Route('/api/contact/{id}', name:'api_contact_show', methods: ['GET'])]
    public function showContact(Contact $contact, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($contact, 'json');
        Return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/contact', name: 'api_contact_create', methods: ['POST'])]
    public function addContact(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $contact = $serializer->deserialize($data, Contact::class, 'json');

        $errors = $validator->validate($contact);

        if(count($errors) > 0){
            $errorsJson = $serializer-> serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($contact);
        $manager->flush();

        return new JsonResponse("Création réalisée avec succès", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/contact/{id}', name: 'api_contact_delete', methods:['DELETE'])]
    public function deleteMember(Contact $contact, EntityManagerInterface $manager)
    {
        $manager->remove($contact);
        $manager->flush();

        return new JsonResponse('Contact Supprimé avec succès', RESPONSE::HTTP_OK, [], true);
    }
}
