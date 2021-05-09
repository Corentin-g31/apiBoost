<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiMemberController extends AbstractController
{
    #[Route('/api/members', name: 'api_members', methods: ['GET'])]
    public function listMembers(MemberRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $listMembers = $serializer->serialize($repo->findAll(), 'json');
        return new JsonResponse($listMembers, 200, [], true);
    }

    #[Route('/api/member/{id}', name:'api_member_show', methods: ['GET'])]
    public function showMember(Member $member, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($member, 'json');
        Return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/member/{id}', name:'api_member_update', methods: ['PUT'])]
    public function updateMember(Member $member, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $data = $serializer->deserialize($data, Member::class, 'json', ['object_to_populate'=>$member]);

        $errors = $validator->validate($member);

        if(count($errors) > 0){
            $errorsJson = $serializer-> serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($member);
        $manager->flush();


        Return new JsonResponse("modification réalisée avec succès", Response::HTTP_OK,[], true);
    }

    #[Route('/api/member', name: 'api_member_create', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $member = $serializer->deserialize($data, Member::class, 'json');

        $errors = $validator->validate($member);

        if(count($errors) > 0){
            $errorsJson = $serializer-> serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($member);
        $manager->flush();

        return new JsonResponse("Création réalisée avec succès", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/member/{id}', name: 'api_member_delete', methods:['DELETE'])]
    public function deleteMember(Member $member, EntityManagerInterface $manager)
    {
        $manager->remove($member);
        $manager->flush();

        return new JsonResponse('Membre Supprimé avec succès', RESPONSE::HTTP_OK, [], true);
    }

}
