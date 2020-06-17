<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function getUsers () {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        if (!$users){
            return new Response('Users not found');
        }
        $usersResponse = array();

        foreach($users as $user) {
            $usersResponse[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'phone' => $user->getPhone()
            );
        }

        return new JsonResponse($usersResponse);
    }

    public function getUser1 ($id) {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$user){
            return new Response('User not found');
        }
        $userResponse = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
        ];
        return new JsonResponse($userResponse);
    }

    public function createUser (Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setName($request->request->get('name'));
        $user->setPhone($request->request->get('phone'));
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response('User has been created id: '.$user->getId());
    }

    public function patchUser ($id, Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if (!$user) {
            return new Response('User not found');
        } else {
            $user->setName($request->request->get('name'));
            $user->setPhone($request->request->get('phone'));
            $entityManager->flush();
            return new Response('User has been updated id: ' . $user->getId());
        }
    }

    public function deleteUser ($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) return new Response('User not found');
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response('User with id '.$id.' has been deleted');
    }
}
