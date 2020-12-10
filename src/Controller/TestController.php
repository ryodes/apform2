<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(): Response
    {
        return $this->render('test.html.twig', [
            'tab' => [1,2,3,5,7,8,9],
            'tab2' => ['toto','tata','titi','tete'],
        ]);
    }

    /**
     * @Route("/success", name="success")
     */
    public function success() {
        return $this->render('success.html.twig');
    }
}
