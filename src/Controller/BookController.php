<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/books', name: 'books_list', methods: ['GET'])]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        return $this->json([
            'livro' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/books/{book}', name: 'books_single', methods: ['GET'])]
    public function single(int $book, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        if( !$book ){
            throw $this->createNotFoundException();
        }

        return $this->json([
            'livro' => $book,
        ]);
    }

    #[Route('/books', name: 'books_create', methods: ['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {
        $data = $request->request->all();

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $bookRepository->add($book, true);

        return $this->json([
            'message' => 'Cadastro de livro realizado c/ sucesso!',
            'livro' => $book,
        ], 201);
    }

    #[Route('/books/{book}', name: 'books_update', methods: ['PUT', 'PATCH'])]
    public function update(int $book, Request $request, ManagerRegistry $doctrine, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        if( !$book ){
            throw $this->createNotFoundException();
        }

        $data = $request->request->all();

        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Atualização de livro realizado c/ sucesso!',
            'livro' => $book,
        ], 201);
    }
}
