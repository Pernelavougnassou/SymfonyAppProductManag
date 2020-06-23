<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request ;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Form\ProductFormType;


class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product")
     */
    public function index()
    {
        return $this->render('product/index.html.twig', [
            'title' => 'Welcome to ProductApp'
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function create(Request $request, ManagerRegistry $managerRegistry) {
        $product = new Product() ;
            //creation du formulaire
        $form = $this->createFormBuilder($product)
                    ->add('name')
                    ->add('price')
                    ->add('quantity')
                    ->add('description')
                    ->add('add', SubmitType::class, [
                        'label' => 'Add'
                    ])
                    ->getForm();
        
        // verifie si le formulaire a été soumis et que tous les champs s'y trouvent
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('product/add.html.twig', [
            'formProduct' => $form->createView()
        ]);
    }

    /**
     * @Route("/list/edit/{id}", name="edit")
     * 
     */
    public function update(Product $product, Request $request, ManagerRegistry $managerRegistry)
    {
        $form = $this->createForm(ProductFormType::class, $product)
                    ->add('Update', SubmitType::class, [
                        'label' => 'Update'
                ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();
            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash('success', 'Product Updated!');
            return $this->redirectToRoute('list');
        }
        return $this->render('product/edit.html.twig', [
            'formProduct' => $form->createView()
        ]);
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(ProductRepository $repo)
    {
        // recupérer la liste des produits dans le repository (2ème méthode)
        // $repo = $this->getDoctrine()->getRepository(Product::class)  ;
        $products = $repo->findAll();
        
        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
        
    }



    /**
     * @Route("/list/delete/{id}",name= "delete")
     *
     */
    public function deleteAction($id){ 
        $entityManager = $this->getDoctrine()->getManager();
        //Rechercher l'Id du produit à supprimer
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
         //suprimer produit
        $entityManager->remove($product);
        $entityManager->flush();
        
        return $this->redirectToRoute('list');
    }

}
