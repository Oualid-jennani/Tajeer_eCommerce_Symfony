<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\{Order, Product, Seller, Slider};
use App\Form\SliderType;
use App\Repository\{OrderRepository, ProductRepository, SellerRepository, SliderRepository};
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile, RedirectResponse, Request, Response, Session\SessionInterface};
use Symfony\Component\{Routing\Annotation\Route, Security\Core\Security};
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var ImageManager
     */
    private ImageManager $imageManager;

    /**
     * AdminController constructor.
     * @param Security $security
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param ImageManager $imageManager
     */
    public function __construct(
        Security $security,
        SessionInterface $session,
        EntityManagerInterface $manager,
        TranslatorInterface $translator,
        ImageManager $imageManager
    ) {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->translator = $translator;
        $this->imageManager = $imageManager;
    }

    //<editor-fold desc="Code dashboard_admin">

    /**
     * @Route("/", name="dashboard_admin")
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('backOffice/admin/index.html.twig',['orders'=>$orders]);
    }
    //</editor-fold>


    //<editor-fold desc="Code Slider">
    /**
     * @Route("/sliders/list", name="admin-sliders-list")
     *
     * @param SliderRepository $sliderRepository
     *
     * @return Response
     */
    public function listSliders(SliderRepository $sliderRepository): Response
    {
        $sliders = $sliderRepository->findAll();

        return $this->render('backOffice/admin/sliders/list.html.twig', [
            'sliders' => $sliders,
        ]);
    }

    /**
     * @Route("/sliders/new", name="admin-sliders-new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newSlider(Request $request): Response
    {
        $slider = new Slider();
        $form = $this->createForm(SliderType::class,$slider);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->addSliderImage($brochureFile,$slider);
            $this->manager->persist($slider);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Slider has been added'));
            return $this->redirectToRoute('admin-sliders-list');
        }

        return $this->render('backOffice/admin/sliders/new.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/sliders/edit/{slug}" , name="admin-sliders-edit")
     *
     * @param Slider  $slider
     * @param Request $request
     *
     * @return Response
     */
    public  function editSlider(Slider $slider, Request $request): Response
    {
        $form = $this->createForm(SliderType::class,$slider);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->updateSliderImage($brochureFile,$slider);
            $this->manager->persist($slider);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Slider has been Edited'));

            return $this->redirectToRoute('admin-sliders-list');
        }

        return $this->render('backOffice/admin/sliders/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/sliders/delete/{slug}" , name="admin-sliders-delete")
     *
     * @param Slider $slider
     *
     * @return RedirectResponse
     */
    public function deleteSlider(Slider $slider): RedirectResponse
    {
        $this->imageManager->deleteSliderImage($slider);

        $this->manager->remove($slider);
        $this->manager->flush();
        $this->addFlash('success', $this->translator->trans('Slider has been Deleted'));

        return $this->redirectToRoute("admin-sliders-list");
    }
    //</editor-fold>


    //<editor-fold desc="Code Sellers">
    /**
     * @Route("/sellers", name="admin_sellers")
     *
     * @param SellerRepository $sellerRepository
     *
     * @return Response
     */
    public function sellers(SellerRepository $sellerRepository): Response
    {
        $sellers = $sellerRepository->findBy([], ['createdAt'=>'ASC']);

        return $this->render('backOffice/admin/seller/sellers.html.twig',[
            'sellers' => $sellers
        ]);
    }

    /**
     * @Route("/sellers/delete/{id}", name="admin_sellers_delete")
     *
     * @param Seller           $seller
     * @param SellerRepository $sellerRepository
     *
     * @return Response
     */
    public function deleteSeller(Seller $seller , SellerRepository $sellerRepository): Response
    {
        $this->manager->remove($seller);
        $this->manager->flush();
        return $this->redirectToRoute('admin_sellers');
    }
    //</editor-fold>


    //<editor-fold desc="Orders Part">
    /**
     * @Route("/orders/{status}",name="admin_orders")
     *
     * @param OrderRepository $orderRepository
     * @param string          $status
     *
     * @return Response
     */
    public function allOrders(OrderRepository $orderRepository, string $status = "all"): Response
    {
        $criteria = 'all' === $status ? [] : $status;
        $called = 'find'.('all' === $status ? 'All' : 'ByStatus');
        $orders = $orderRepository->$called($criteria);

        return $this->render('backOffice/admin/orders/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route(
     *     "/orders/{status}/change-status/{trackNumber}",
     *     name="admin_orders_change_status"
     * )
     *
     * @ParamConverter("order", options={"mapping": {"trackNumber": "trackNumber"}})
     *
     * @param Order  $order
     * @param string $status
     *
     * @return Response
     */
    public function changeStatus(Order $order, string $status = 'all'): Response
    {
        if (\in_array($status, [Order::STATUS_VALIDATED, Order::STATUS_CANCELED]) === true) {
            $order->setStatus($status);
            $this->manager->flush();
            $this->addFlash('success',$this->translator->trans('Order Has been changed to '.$status));
        } else {
            $this->addFlash('danger',$this->translator->trans('Choose a right Status and try again'));
        }

        return  $this->redirectToRoute('admin_orders', ['status'=>Order::STATUS_INITIATED]);
    }

    /**
     * @Route("/order/{trackNumber}",name="admin_order_detail")
     *
     * @param Order $order
     *
     * @return Response
     */
    public function detailOrder(Order $order): Response
    {
        return $this->render('backOffice/admin/orders/detail.html.twig', [
            'order' => $order,
        ]);
    }


    //</editor-fold>

    //<editor-fold desc="Orders Part">

    /**
     * @Route("/exclusive-collection",name="admin_exclusive_col_list")
     *
     * @param ProductRepository $productRepository
     *
     * @return Response
     */
    public function exclusiveCollection(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['isExclusive'=>true], ['createdAt'=>'DESC']);

        return $this->render('backOffice/admin/exclusive-collection/list.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/exclusive-collection/add",name="admin_exclusive_col_add")
     *
     * @param ProductRepository $productRepository
     * @param Request           $request
     *
     * @return Response
     */
    public function addExclusiveCollection(ProductRepository $productRepository , Request $request): Response
    {
        $query = $request->query->get('products');
        if ($query !== null) {
            foreach ($query as $slug ) {
                /** @var Product $product */
                $product = $productRepository->findOneBy(['slug' => $slug]);
                $product->setIsExclusive(true);
            }
            $this->manager->flush();
            $this->addFlash(
                'success',
                $this->translator->trans('Products has been added to exclusive collection')
            );
        }
        $products = $productRepository->findBy(['isExclusive' => false], ['createdAt'=>'DESC']);

        return $this->render('backOffice/admin/exclusive-collection/add.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/exclusive-collection/delete/{slug}",name="admin_exclusive_col_delete")
     *
     * @param Product $product
     *
     * @return Response
     */
    public function exclusiveCollectionDelete(Product $product): Response
    {
        try {
            $product->setIsExclusive(false);
            $this->manager->flush();
            $this->addFlash('success',$this->translator->trans('Product Deleted from exclusive collection'));
        } catch (\Exception $exception) {
            $this->addFlash('danger','Error Excep');
        }

        return $this->redirectToRoute('admin_exclusive_col_list');
    }

    //</editor-fold>
}
