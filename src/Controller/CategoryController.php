<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function addCategory(Request $request, EntityManagerInterface $em)
    {
        $category = new Category;
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(str_replace(' ', '-', $category->getName()));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('success');
        }


        return $this->render('category/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/category/edit/{id}",name="editCategory")
     */
    public function editCategory(Request $request, EntityManagerInterface $em, $id)
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category){
            return $this->redirectToRoute('category');
        }
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('success');
        }


        return $this->render('category/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/category/delete/{id}",name="deleteCategory")
     */
    public function deleteCategory(Request $request, EntityManagerInterface $em, $id){
        $category = $em->getRepository(Category::class)->find($id);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('category');
    }

    /**
     * @Route("/category/{id}/products",name="productsCategory")
     */
    public function productsCategory(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, $id){
        $repository = $productRepository->findBy(['categoryId' => $id]);

        return $this->render('category/productsCategory.html.twig', [
            'listProductByCategory' => $repository,
            ]);
    }

}
