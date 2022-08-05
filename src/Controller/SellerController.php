<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\Slider;
use App\Entity\SliderSeller;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditCustomerType;
use App\Form\EditProfileImageType;
use App\Form\EditSellerType;
use App\Form\Model\ChangePassword;
use App\Form\ProductType;
use App\Form\SliderSellerType;
use App\Form\SliderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SliderRepository;
use App\Repository\SliderSellerRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class SellerController
 * @package App\Controller
 * @Route("/seller")
 */
class SellerController extends AbstractController
{
    private $security;
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var ImageManager
     */
    private ImageManager $imageManager;


    /**
     * SellerController constructor.
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
    )
    {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->translator = $translator;
        $this->imageManager = $imageManager;
    }

    /**
     * @Route("/", name="dashboard_seller")
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findOrderBySeller($this->getUser());
        return $this->render('backOffice/seller/index.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/account",name="seller_account")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function account(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        /**
         * @var Seller $seller
         */
        $seller = $this->getUser();
        $formProfile = $this->createForm(EditProfileImageType::class,$seller);
        $formProfile->handleRequest($request);

        $user = $this->getUser();
        $form = $this->createForm(EditSellerType::class,$seller);
        $form->handleRequest($request);
        $changePasswordModel = new ChangePassword();
        $formPassword = $this->createForm(ChangePasswordType::class,$changePasswordModel);
        $formPassword->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $profileImage = $formProfile->get('brochureProfile')->getData();
            $this->imageManager->updateProfile($profileImage);
            return $this->redirectToRoute('seller_account');
        }
        else if ($form->isSubmitted() && $form->isValid()) {

            try {
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', $this->translator->trans('Information EditedPassword Changed'));

            } catch (\Exception $exception) {
                $this->addFlash('danger', $this->translator->trans('Error'));
            }

        } elseif ($formPassword->isSubmitted() && $formPassword->isValid()) {

            try {
                $hash = $encoder->encodePassword($user,$changePasswordModel->getNewPassword());
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', $this->translator->trans('Password Changed'));

            } catch (\Exception $exception) {
                $this->addFlash('danger', $this->translator->trans('Error'));
            }

        }

        return $this->render('backOffice/seller/account/account-info.twig',[
            'formProfile'=>$formProfile->createView(),
            'form'=>$form->createView(),
            'formPassword'=>$formPassword->createView(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/settings",name="seller_settings")
     * @param Request $request
     * @return Response
     */
    public function settings(Request $request): Response
    {

        return $this->render('backOffice/seller/account/settings.html.twig',[

        ]);
    }


    //<editor-fold desc="Code Slider">

    /**
     * @Route("/sliders/list", name="seller-sliders-list")
     * @param SliderSellerRepository $sliderSellerRepository
     * @return Response
     */
    public function listSliders(SliderSellerRepository $sliderSellerRepository): Response
    {
        $sliders = $sliderSellerRepository->findAll();
        return $this->render('backOffice/seller/sliders/list.html.twig', [
            'sliders' => $sliders,
        ]);
    }

    /**
     * @Route("/sliders/new", name="seller-sliders-new")
     * @param Request $request
     * @return Response
     */
    public function newSlider(Request $request): Response
    {
        $sliderSeller = new SliderSeller();
        $form = $this->createForm(SliderSellerType::class,$sliderSeller);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->addSliderSellerImage($brochureFile,$sliderSeller);
            /** @var Seller $seller */
            $seller = $this->getUser();
            $sliderSeller->setSeller($seller);
            $this->manager->persist($sliderSeller);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Slider has been added'));
            return $this->redirectToRoute('seller-sliders-list');
        }

        return $this->render('backOffice/seller/sliders/new.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/sliders/edit/{slug}" , name="seller-sliders-edit")
     * @param SliderSeller $sliderSeller
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public  function editSlider(SliderSeller $sliderSeller, Request $request){
        $form = $this->createForm(SliderSellerType::class,$sliderSeller);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->updateSliderSellerImage($brochureFile,$sliderSeller);
            $this->manager->persist($sliderSeller);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Slider has been Edited'));

            return $this->redirectToRoute('seller-sliders-list');
        }

        return $this->render('backOffice/seller/sliders/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/sliders/delete/{slug}" , name="seller-sliders-delete")
     * @param SliderSeller $sliderSeller
     * @return RedirectResponse
     */
    public function deleteSlider(SliderSeller $sliderSeller)
    {
        $this->imageManager->deleteSliderSellerImage($sliderSeller);

        $this->manager->remove($sliderSeller);
        $this->manager->flush();
        $this->addFlash('success', $this->translator->trans('Slider has been Deleted'));

        return $this->redirectToRoute("seller-sliders-list");
    }
    //</editor-fold>

    //<editor-fold desc="Products Part">

    /**
     * @Route("/products/list", name="seller_products_list")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function productList(ProductRepository $productRepository): Response
    {
        /** @var Seller $seller */
        $seller = $this->getUser();
        return $this->render('backOffice/seller/product/list.html.twig', [
            'products' => $seller->getProducts(),
        ]);
    }

    /**
     * @Route("/products/new", name="seller_products_new")
     *
     * @param Request $request
     * @return Response
     */
    public function newProduct(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, ['category' => null]);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->imageManager->saveProductImages($product);
                foreach ($product->getVariants() as $variant) {
                    $variant->setProduct($product);
                    $this->manager->persist($variant);
                }
                $product->setCreatedAt(new \DateTime());
                /** @var Seller $seller */
                $seller = $this->getUser();
                $product->setSeller($seller);
                $this->manager->persist($product);
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Product has been added'));
            }
        } catch (\Exception $ex) {
            $this->addFlash('danger', 'Err Exc');
        }

        return $this->render('backOffice/seller/product/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/products/edit/{slug}", name="seller_products_edit")
     *
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function editProduct(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product, ['category' => $product->getCategory()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageManager->updateProductImages($product, $request);
            foreach ($product->getVariants() as $variant) {
                $variant->setProduct($product);
                $this->manager->persist($variant);
            }
            $this->manager->persist($product);
            $this->manager->flush();
            $this->addFlash('success', 'Product has been Edited');

            return $this->redirectToRoute('seller_products_edit', ['slug' => $product->getSlug()]);
        }

        return $this->render('backOffice/seller/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/products/delete/{slug}" , name="seller_products_delete")
     *
     * @param Product $product
     *
     * @return RedirectResponse
     */
    public function deleteProduct(Product $product)
    {
        $this->imageManager->deleteProductImages($product);
        $this->manager->remove($product);
        $this->manager->flush();
        $this->addFlash('success', $this->translator->trans('Product Has been Deleted'));

        return $this->redirectToRoute("seller_products_list");
    }

    /**
     * @Route("/products/detail/{slug}" , name="seller_products_detail")
     *
     * @param Product $product
     *
     * @return RedirectResponse
     */
    public function detailProduct(Product $product): Response
    {
        return $this->render('backOffice/seller/product/detail.html.twig', [
            'product' => $product,
        ]);
    }

    //</editor-fold>


    //<editor-fold desc="Orders Part">
    /**
     * @Route("/all-orders",name="seller_orders_all")
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function allOrders(OrderRepository $orderRepository)
    {
        $seller = $this->getUser();
        $orders = $orderRepository->findOrderBySeller($seller);

        return $this->render('backOffice/seller/orders/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order/{trackNumber}",name="seller_order_detail")
     * @param Order $order
     * @return Response
     */
    public function detailOrder(Order $order)
    {
        /** @var Cart $cart */
        foreach ($order->getCarts() as $item) {
            if ($item->getSeller() == $this->getUser()) {
                $cart = $item;
                break;
            }
        }
        return $this->render('backOffice/seller/orders/detail.html.twig', [
            'order' => $order,
            'cart' => $cart
        ]);
    }


    //</editor-fold>

}
