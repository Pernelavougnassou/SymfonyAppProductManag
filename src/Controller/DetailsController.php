<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class DetailsController extends AbstractController
{
    /**
     * @Route("/details", name="details")
     */
    public function index(ProductRepository $repo)
    {
        $products = $repo->findAll();
        
        return $this->render('details/index.html.twig', [
            'products' => $products , 
        ]);
    }

}
