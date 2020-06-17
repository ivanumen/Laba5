<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractController
{
    public function getBooks () {
        $books = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();
        if (!$books){
            return new Response('Books not found');
        }
        $booksResponse = array();

        foreach($books as $book) {
            $booksResponse[] = array(
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'author' => $book->getAuthor()
            );
        }

        return new JsonResponse($booksResponse);
    }

    public function getBook ($id) {
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);
        if (!$book){
            return new Response('Book not found');
        }
        $bookResponse = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'author' => $book->getAuthor()
        ];
        return new JsonResponse($bookResponse);
    }

    public function createBook (Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $book = new Book();
        $book->setTitle($request->request->get('title'));
        $book->setDescription($request->request->get('description'));
        $book->setAuthor($request->request->get('author'));
        $entityManager->persist($book);
        $entityManager->flush();
        return new Response('Book has been created id: '.$book->getId());
    }

    public function patchBook ($id, Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);
        if (!$book) {
            return new Response('Book not found');
        } else {
            $book->setTitle($request->request->get('title'));
            $book->setDescription($request->request->get('description'));
            $book->setAuthor($request->request->get('author'));
            $entityManager->flush();
            return new Response('Book has been updated id: ' . $book->getId());
        }
    }

    public function deleteBook ($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) return new Response('Book not found');
        $entityManager->remove($book);
        $entityManager->flush();
        return new Response('Book with id '.$id.' has been deleted');
    }
}
