<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function product(ProductRepository $productRepository)
    {
        $product = $productRepository->findAll();
        return $this->render('product.html.twig', [
            'listProduct' => $product,
        ]); 
    }

    /**
     * @Route("/product/add", name="productAdd")
     */
    public function addProduct(EntityManagerInterface $em, Request $request){
        $Product = new Product;

        $builder = $this->createFormBuilder();
        $builder->add('name', TextType::class, [
                    'attr' => [
                        'placeholder' => "nom du produit"
                        ]
                    ])
                ->add('price', IntegerType::class, [
                    'attr' => [
                        'placeholder' => "prix du produit"
                        ]
                    ])
                ->add('image', TextType::class, [
                    'attr' => [
                        'placeholder' => "image du produit"
                        ]
                    ])
                
                ->add('category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'placeholder' => 'choisir une catgorie',
                    'label' => 'categorie',

                ])
                ->add('save', SubmitType::class,['label' => 'Ajouter Produit']);
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $product = new Product;
            $product->setName($data['name'])
                    ->setPrice($data['price'])
                    ->setImg($data['image'])
                    ->setSlug(str_replace(' ', '-', $data['name']))
                    ->setCategoryId($data['category']);
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('success');
        }

        return $this->render('productAdd.html.twig', [
            'formProdcut' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="detailProduct")
     */
    public function detailProduct(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, $id){

        $product = $productRepository->find($id);
        
        return $this->render('product/produdctDetail.html.twig',[
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="deleteProduct")
     */
    public function deleteProduct(Product $product, EntityManagerInterface $em, $id){

        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('product');
    }
}
