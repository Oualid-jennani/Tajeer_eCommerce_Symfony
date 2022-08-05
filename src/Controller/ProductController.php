<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    //<editor-fold desc="Code Feature & Best Sellers">
    /**
     * @Route("/feature-and-bestSellers", name="feature_and_bestSellers_products")
     *
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function featureAndBestSellersProducts(ProductRepository $productRepository): Response
    {
        $featureProducts = $productRepository->findBy([],['createdAt'=>'DESC'],20);

        return $this->render('frontOffice/master/products/feature_and_bestSellers_products.html.twig', [
            'featureProducts'=>$featureProducts
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Code Feature & Best Sellers">
    /**
     * @Route("/new-arrival", name="new-arrival")
     *
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function newArrival(ProductRepository $productRepository): Response
    {
        $newArrival = $productRepository->findBy([],['createdAt'=>'DESC'],20);
        $newArrival2 = $productRepository->findBy([],['createdAt'=>'DESC'],20);

        return $this->render('frontOffice/master/products/new_arrival.html.twig', [
            'newArrival'=> $newArrival,
            'newArrival2' => $newArrival2
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Code Feature & Best Sellers">
    /**
     * @Route("/new-arrival", name="new-arrival")
     *
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function exclusiveCollection(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['isExclusive'=>true],['createdAt'=>'DESC']);

        return $this->render('frontOffice/master/products/exclusive_collection.html.twig', [
            'products' => $products
        ]);
    }
    //</editor-fold>
}
