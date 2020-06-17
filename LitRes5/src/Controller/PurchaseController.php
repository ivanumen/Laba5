<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Purchase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends AbstractController
{
    public function getPurchasesByUserId ($userId) {
        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        $purchases = $user->getPurchases();
        foreach ($purchases as $purchase) {
            $purchasesResponse[] = [
                'id' => $purchase->getId(),
                'username' => $purchase->getUser()->getName(),
                'books title' => $purchase->getBook()->getTitle(),
                'createdTime' => $purchase->getCreatedTime()
            ];
        }
        return new JsonResponse($purchasesResponse);
    }

    public function getPurchase ($id) {
        /** @var Purchase $purchase */
        $purchase = $this->getDoctrine()
            ->getRepository(Purchase::class)
            ->find($id);
        if (!$purchase){
            return new Response('Purchase not found');
        }
        $purchaseResponse = [
            'id' => $purchase->getId(),
            'username' => $purchase->getUser()->getName(),
            'books title' => $purchase->getBook()->getTitle(),
            'createdTime' => $purchase->getCreatedTime()
        ];
        return new JsonResponse($purchaseResponse);
    }

    public function createPurchase (Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $purchase = new Purchase();
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($request->request->get('bookId'));
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($request->request->get('userId'));
        $purchase->setBook($book);
        $purchase->setUser($user);
        $purchase->setCreatedTime(new \DateTime('now'));
        $entityManager->persist($purchase);
        $entityManager->flush();
        return new Response('Purchase has been created id: '.$purchase->getId());
    }

    public function deletePurchase ($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $purchase = $entityManager->getRepository(Purchase::class)->find($id);
        if (!$purchase) return new Response('Purchase not found');
        $entityManager->remove($purchase);
        $entityManager->flush();
        return new Response('Purchase with id '.$id.' has been deleted');
    }
}
