<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\{Cart, CartLine, Category, Customer, Order, Product, Seller, Variant};
use App\Form\{AddToCartType, CartHeaderType, CartType, OrderType};
use App\Repository\{AdminRepository,
    CategoryRepository,
    ProductRepository,
    SellerRepository,
    SliderRepository,
    UserRepository,
    VariantRepository};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response, Session\SessionInterface};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Composer\Autoload\includeFile;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
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
     * StoreController constructor.
     *
     * @param Security $security
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Security $security,
        SessionInterface $session,
        EntityManagerInterface $manager,
        TranslatorInterface $translator
    ) {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->translator = $translator;
    }

    //<editor-fold desc="Code index">

    /**
     * @Route("/", name="index")
     *
     * @param SliderRepository $sliderRepository
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function index(SliderRepository $sliderRepository,ProductRepository $productRepository): Response
    {
        $sliders = $sliderRepository->findAll();
        $newProducts = $productRepository->findBy([],['createdAt'=>'DESC'],5);
        return $this->render('frontOffice/default/index.html.twig', [
            'sliders'=>$sliders,
            'newProducts'=>$newProducts
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Render Categories & sub Categories">
    /**
     * @Route("/categories", name="default_categories")
     *
     * @param CategoryRepository $categoryRepository
     * @return Response the response
     */
    public function categories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('frontOffice/master/categories.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/categories-for-search", name="search_categories")
     *
     * @param CategoryRepository $categoryRepository
     * @return Response the response
     */
    public function searchCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('frontOffice/master/search_for_categories.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/categories", name="default_categories")
     *
     * @param CategoryRepository $categoryRepository
     * @return Response the response
     */
    public function categoriesSubCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('frontOffice/master/mega_menu.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    //</editor-fold>


    //<editor-fold desc="Code Product">
    /**
     * @Route("/product/{slug}", name="product")
     * @param Product $product
     * @param Request $request
     * @param SessionInterface $session
     * @param VariantRepository $variantRepository
     * @return Response the response
     */
    public function product(Product $product, Request $request, SessionInterface $session, VariantRepository $variantRepository): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        } else {
            $cart = new Cart();
            $session->set('CART', $cart);
        }

        $cartLine = new CartLine();
        $form = $this->createForm(AddToCartType::class, $cartLine);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $cartLine->setProduct($product);
            $data = $request->request->get("variant");
            /** @var Variant|null $variant */
            $variant = $variantRepository->findOneBy(['track' => $data]);
            if (null !== $variant) {
                $cartLine->setVariant($variant);
            }

            $cart->addCartLine($cartLine);
            $session->set('CART', $cart);

            $this->addFlash('success', $this->translator->trans('Product is Added to Cart'));
        }
        return $this->render('frontOffice/default/product.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Code Cart">
    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response the response
     */
    public function cart(Request $request, SessionInterface $session): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        }
        if (null === $cart) {
            return $this->render('frontOffice/default/cart.html.twig');
        }

        if ($cart->getCartLines()->count() == 0) {
            return $this->render('frontOffice/default/cart.html.twig');
        }

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $session->set('CART', $cart);
            if ($cart->getCartLines()->count() == 0) {
                return $this->redirectToRoute('index');
            }
        }

        return $this->render('frontOffice/default/cart.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/header-cart", name="headerCart")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response the response
     */
    public function headerCart(Request $request, SessionInterface $session): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        } else {
            $cart = new Cart();
            $session->set('CART', $cart);
        }

        $form = $this->createForm(CartHeaderType::class, $cart);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $session->set('CART', $cart);
            $ref = $request->headers->get('referer');
            if (!is_string($ref) || $ref) {
                return $this->redirect($ref);
            }
        }

        return $this->render('frontOffice/master/cart.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }
    //</editor-fold>


    //<editor-fold desc="Code checkout">
    /**
     * @Route("/checkout", name="checkout")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SellerRepository $sellerRepository
     * @param VariantRepository $variantRepository
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function checkout(
        Request $request,
        UserRepository $userRepository,
        SellerRepository $sellerRepository,
        VariantRepository $variantRepository,
        ProductRepository $productRepository
    ): Response {
        /** @var Cart|null $cart */
        if ($this->session->has('CART') === true) {
            $cart = $this->session->get('CART');
        }

        if (null === $cart || $cart->getCartLines()->count() == 0) {
            return $this->render('frontOffice/default/cart.html.twig', []);
        }

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $sellers = [];
            $sellersCartLines = [];
            $lines = $cart->getCartLines();
            foreach ($lines as $line) {
                /** @var Product|null $product */
                $product = $line->getProduct();
                /** @var Seller|null $seller */
                $seller = $line->getProduct()->getSeller();

                /** @var Variant|null $variant */
                $variant = $line->getVariant();

                $sellers[] = $seller->getId();
                $sellersCartLines[] = [
                    'seller' => $seller->getId(),
                    'product' => $product->getId(),
                    'quantity' => $line->getQuantity(),
                ];

                if ($variant!== null) $sellersCartLines[] = ['variant' => $variant->getId()];
            }

            if ($this->security->getUser() != null) {
                /** @var Customer|null $customer */
                $customer = $userRepository->find($this->security->getUser());
                $order->setCustomer($customer);
                $order->setFullName($customer->getFullName());
                $order->setPhone($customer->getPhoneNumber());
            }
            $order->setStatus(Order::STATUS_INITIATED);
            $trackNumber = strtoupper(md5(uniqid()));
            $order->setOrderDate(new \DateTime());
            $order->setTrackNumber($trackNumber);
            $this->manager->persist($order);
            $this->manager->flush();
            $this->session->remove('CART');

            foreach (array_unique($sellers) as $seller) {
                $newCart = new Cart();
                $newCart->setOrderCart($order);
                $newCart->setSeller($sellerRepository->find($seller));
                $this->manager->persist($newCart);
                $this->manager->flush();
                $existVariant = array_key_exists('variant',$sellersCartLines);
                foreach ($sellersCartLines as $row) {
                    if ($seller == $row['seller']) {
                        $product = $productRepository->find($row['product']);
                        if ($existVariant) {
                            $variant = $variantRepository->find($row['variant']);
                        }
                        $line = new CartLine();
                        $line->setCart($newCart)->setProduct($product)->setQuantity($row['quantity']);
                        if ($existVariant){
                            $line->setVariant($variant);
                        }
                        $this->manager->persist($line);
                        $this->manager->flush();
                    }
                }
            }
            $this->manager->flush();

            return $this->redirectToRoute('checkoutComplete');
        }

        return $this->render('frontOffice/default/checkout.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/checkout_complete", name="checkoutComplete")
     *
     * @return Response the response
     */
    public function checkoutComplete(): Response
    {
        return $this->render('frontOffice/default/checkoutComplete.html.twig', []);
    }
    //</editor-fold>

    //<editor-fold desc="Code Shop">
    /**
     * @Route("/shop/{slug}", name="shop" , methods={"GET","POST"}, defaults={"slug" = null})
     *
     * @param Request $request
     * @param Category $category
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function shop(Request $request,CategoryRepository $categoryRepository,ProductRepository $productRepository,Category $category = null): Response
    {

        $productName = $request->query->get('productName');
        $categorySlug = $request->query->get('category');
        $products =[];
        if($category != null) {
            $products = $productRepository->findBy(['category' => $category], ['createdAt' => 'DESC']);
            $categorySlug = $category->getSlug();
        }elseif (isset($categorySlug)) {
            if ($categorySlug == "all" && isset($productName)) {
                $products = $productRepository->findByLikeName($productName);
            }elseif ($categorySlug != "all") {
                $category = $categoryRepository->findOneBy(['slug'=>$categorySlug]);
                if(isset($productName)){
                    $products = $productRepository->findProductsByCategory($productName,$category);
                }
                else{
                    $products = $productRepository->findBy(['category' => $category], ['createdAt' => 'DESC']);
                }
            }else {
                $this->addFlash('danger',$this->translator->trans('Please enter your product Name and choose a Category !'));
            }
        }
        else{
            $products = $productRepository->findAll();
        }

        $pricesRange = [];
        if ($request->query->get('pricesRange')) {
            /** @var array $pricesRange */
            $pricesRange = $request->query->get('pricesRange');
            if(!empty($pricesRange)) {
                $products = $this->getProductsByPricesRange($pricesRange,$products)->toArray();
            }
        }

        return $this->render('frontOffice/default/shop_page.html.twig',[
            'category'=>$category,
            'products' => $products,
            'categorySlug'=>$categorySlug,
            'productName'=>$productName,
            'pricesRange' => $pricesRange,
        ]);
    }

    //</editor-fold>

    public function getProductsByPricesRange($pricesRange,$products)
    {
        $firstPrice = explode(',',$pricesRange[0]);
        $lastPrice = explode(',',$pricesRange[count($pricesRange)-1]);
        $products = new ArrayCollection($products);
        return $products->filter(function (Product $product)  use ($lastPrice,$firstPrice) {
            if ($product->getPrice() >= $firstPrice[0] && $product->getPrice() <= $lastPrice[1])  return $product;
        });
    }

    /**
     * @Route("/store-seller/{storeName}", name="seller")
     *
     * @param Request $request
     * @param Seller $seller
     * @param CategoryRepository $categoryRepository
     * @return Response the response
     */
    public function Seller(Request $request ,Seller $seller,CategoryRepository $categoryRepository): Response
    {
        $products = [];
        $pricesRange = [];
        $categories = [];
        if ($request->query) {
            /** @var array $categories */
            $categories = $request->query->get('categories');
            /** @var array $pricesRange */
            $pricesRange = $request->query->get('pricesRange');
            if (!empty($categories) && !empty($pricesRange) ) {
                /** @var Category $category */
                foreach ($categories as $slugCategory) {
                    $category = $categoryRepository->findOneBy(['slug'=>$slugCategory]);
                    $products = array_merge($products,$seller->getProductsByCategoriesAndPricesRange($pricesRange,$category)->toArray());
                }
            }elseif( empty($categories) && !empty($pricesRange)) {
                $products = array_merge($products,$seller->getProductsByPricesRange($pricesRange)->toArray());
            }elseif ( !empty($categories) && empty($pricesRange)) {
                /** @var Category $category */
                foreach ($categories as $slugCategory) {
                    $category = $categoryRepository->findOneBy(['slug'=>$slugCategory]);
                    $products = array_merge($products,$seller->getProductsByCategory($category)->toArray());
                }
            }else{
                $products = $seller->getProducts();
            }
        }else {
            $products = $seller->getProducts();
        }

        return $this->render('frontOffice/default/seller.html.twig', [
            'seller'=>$seller,
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'pricesRange' => $pricesRange,
            'categoriesRange' => $categories
        ]);
    }

    //<editor-fold desc="Translation">
    /**
     * @Route("change-locale/{locale}" , name="change_locale")
     * @param $locale
     * @param Request $request
     * @return RedirectResponse
     */
    public function changeLocale($locale,Request $request) {

        $request->getSession()->set('_locale',$locale);
        $request->setLocale($locale);
        return $this->redirect($request->headers->get('referer'));
    }
    //</editor-fold>

}