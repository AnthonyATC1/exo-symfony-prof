<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class HelloController extends AbstractController
{
    protected $logger;
    private $Title = "Hello ";

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/test/{name?world}', name: 'test', methods: ['GET', 'POST'])]
    public function test(string $name = 'world'): Response
    {
        $this->Title .= $name;

        return $this->render('View/item/hello.html.twig', [
            'title' => $this->Title
        ]);
    }
}
