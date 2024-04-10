<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/books', name: 'books_list', methods: ['GET'])]
    public function index(BookRepository $bookRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtém uma instância do QueryBuilder
        $queryBuilder = $entityManager->createQueryBuilder();

        // Define a query para selecionar todos os livros ordenados pelo ID ASC
        $queryBuilder->select('b')->from('App\Entity\Book', 'b')->orderBy('b.id', 'ASC');

        // Executa a query
        $books = $queryBuilder->getQuery()->getResult();

        return $this->json([
            'book' => $books,
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
            'book' => $book,
        ]);
    }

    #[Route('/books', name: 'books_create', methods: ['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {
        // Configuração p/ receber JSON
        if( $request->headers->get('Content-Type') == 'application/json' ){

            $data = $request->toArray();

        }else{  

            $data = $request->request->all();

        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $bookRepository->add($book, true);

        return $this->json([
            'message' => 'Cadastro de livro realizado c/ sucesso!',
            'book' => $book,
        ], 201);
    }

    #[Route('/books/{book}', name: 'books_update', methods: ['PUT', 'PATCH'])]
    public function update(int $book, Request $request, ManagerRegistry $doctrine, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        if( !$book ){
            throw $this->createNotFoundException();
        }

        // Configuração p/ receber JSON
        if( $request->headers->get('Content-Type') == 'application/json' ){

            $data = $request->toArray();

        }else{  

            $data = $request->request->all();

        }

        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Atualização de livro realizado c/ sucesso!',
            'book' => $book,
        ], 201);
    }

    #[Route('/books/{book}', name: 'books_delete', methods: ['DELETE'])]
    public function delete(int $book, Request $request, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        // $bookName = $book->getTitle();

        $bookRepository->remove($book, true);

        return $this->json([
            'message' => 'Livro deletado c/ sucesso!',
            'book_deleted' => $book
        ]);
    }

}
