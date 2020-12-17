<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="product")
     */
    public function product(ProductRepository $productRepository)
    {
        $product = $productRepository->findAll();
        return $this->render('product.html.twig', [
            'listProduct' => $product,
        ]); 
    }

    /**
     * @Route("/admin/product/add", name="productAdd")
     */
    public function addProduct(KernelInterface $appKernel, EntityManagerInterface $em, Request $request, SluggerInterface $slugger){
        $path = $appKernel->getProjectDir() . '\public\img';

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
                ->add('img', FileType::class, [
                    'required' => false,
                    'label' => 'Image produit',
                    'attr' => [
                        'placeholder' => 'Ajouter une image',
                    ],
                    'constraints' => [
                        new File([
                            'maxSize' => '4096k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Merci de charger une jpg/png',
                            'uploadFormSizeErrorMessage' => 'Taille maximale de fichier 4 Méga'
                        ])
                    ],
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
                    #->setSlug(str_replace(' ', '-', $data['name']))
                    ->setSlug($slugger->slug($product->getName()))
                    ->setCategoryId($data['category']);

            $file = $form['img']->getData();
            if ($file) {
                // récup nom de fichier sans extension
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();
                // set nom dans la propriété Img
                $product->setImg($newFilename);

                //Déplacer le fichier dans le répertoire public + sous répertoire
                try {
                    $file->move($path, $newFilename);
                } catch (FileException $e) {
                    echo $e->getMessage();
                }
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            return $this->redirectToRoute('success');
        }

        return $this->render('productAdd.html.twig', [
            'formProdcut' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/{id}", name="detailProduct")
     */
    public function detailProduct(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, $id){

        $product = $productRepository->find($id);
        
        return $this->render('product/produdctDetail.html.twig',[
            'product' => $product,
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="deleteProduct")
     */
    public function deleteProduct(Product $product, EntityManagerInterface $em, $id){

        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Produit effacé avec succès');

        return $this->redirectToRoute('product');
    }
}