<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function test(ProductRepository $productRepository)
    {
        $count = $productRepository->count(['price' => 200]);
        $product = $productRepository->findAll();
        return $this->render('test.html.twig', [
            'tab' => [1,2,3,5,7,8,9],
            'tab2' => ['toto','tata','titi','tete'],
            'count' => $count,
            'product' => $product,
        ]);
    }

    /**
     * @Route("/category",name="category")
     *  */
    public function category(CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find(1);

        return $this->render(
            'category.html.twig',
            ['products' => $category]
        );
    }

}
