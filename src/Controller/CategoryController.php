<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category",name="category")
     *  */
    public function category(CategoryRepository $categoryRepository)
    {
        //entity manager
        //$em = $this->getDoctrine()->getManager();
        //$category = $em->getRepository(Category::class)->findAll();

        $category = $categoryRepository->findAll();

        return $this->render('category.html.twig',[
            'listCategory' => $category,
        ]);
    }

    /**
     * @Route("/category/add", name="ajoutCategory")
     */
    public function addCategory() {
        $em = $this->getDoctrine()->getManager();
        $category = new Category;
        $category->setName('xiaomi')
                 ->setDescription('un autre fabricant')
                 ->setSlug('xiaomi');
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('success');
    }

}
