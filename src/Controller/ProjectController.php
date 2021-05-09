<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectController extends AbstractController
{
    #[Route('/api/project', name: 'api_project', methods: ['GET'])]
    public function listContact(ProjectRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $listProject= $serializer->serialize($repo->findAll(), 'json');
        return new JsonResponse($listProject, 200, [], true);
    }

    #[Route('/api/project/{id}', name:'api_project_show', methods: ['GET'])]
    public function showProject(Project $project, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($project, 'json');
        Return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/project', name: 'api_project_create', methods: ['POST'])]
    public function addProject(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $project = $serializer->deserialize($data, Project::class, 'json');

        $errors = $validator->validate($project);

        if(count($errors) > 0){
            $errorsJson = $serializer-> serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($project);
        $manager->flush();

        return new JsonResponse("Création réalisée avec succès", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/project/{id}', name:'api_project_update', methods: ['PUT'])]
    public function updateProject(Project $project, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $data = $serializer->deserialize($data,Project::class, 'json', ['object_to_populate'=>$project]);

        $errors = $validator->validate($project);

        if(count($errors) > 0){
            $errorsJson = $serializer-> serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($project);
        $manager->flush();


        Return new JsonResponse("modification réalisée avec succès", Response::HTTP_OK,[], true);
    }

    #[Route('/api/project/{id}', name: 'api_member_delete', methods:['DELETE'])]
    public function deleteProject(Project $project, EntityManagerInterface $manager)
    {
        $manager->remove($project);
        $manager->flush();

        return new JsonResponse('Membre Supprimé avec succès', RESPONSE::HTTP_OK, [], true);
    }





}
