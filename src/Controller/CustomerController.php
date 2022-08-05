<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditCustomerType;
use App\Form\EditProfileImageType;
use App\Form\EditSellerType;
use App\Form\Model\ChangePassword;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CustomerController
 * @package App\Controller
 * @Route("/customer")
 */
class CustomerController extends AbstractController
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

    /**
     * @Route("/account-info", name="customer-account-info")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function profile(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(EditCustomerType::class,$user);
        $form->handleRequest($request);

        $changePasswordModel = new ChangePassword();
        $formPassword = $this->createForm(ChangePasswordType::class,$changePasswordModel);
        $formPassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Information EditedPassword Changed'));

            } catch (\Exception $exception) {
                $this->addFlash('danger', $this->translator->trans('Error'));
            }
        } elseif ($formPassword->isSubmitted() && $formPassword->isValid()) {

            try {
                $hash = $encoder->encodePassword($user,$changePasswordModel->getNewPassword());
                $user->setPassword($hash);
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Password Changed'));

            } catch (\Exception $exception) {
                $this->addFlash('danger', $this->translator->trans('Error'));
            }

        }

        return $this->render('frontOffice/customer/account-info.html.twig',[
            'form'=>$form->createView(),
            'formPassword'=>$formPassword->createView(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/orders-history", name="customer-orders-history")
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function ordersHistory(OrderRepository $orderRepository): Response
    {
        $customer = $this->getUser();
        $orders = $orderRepository->findByCustomer($customer);
        return $this->render('frontOffice/customer/order_history.html.twig',[
            'orders' => $orders,
        ]);

    }

    /**
     * @Route("/orders-detail/{trackNumber}", name="customer-orders-detail")
     * @return Response
     */
    public function ordersDetail(Order $order): Response
    {
        return $this->render('frontOffice/customer/order_detail.html.twig',[
            'order' => $order,
        ]);

    }

    /**
     * @Route("/new-order/{trackNumber}", name="customer_order_again")
     * @param Order $order
     * @param SessionInterface $session
     * @return Response
     */
    public function NewOrder(Order $order): Response
    {
        /** @var Cart|null $cart */
        $cart = new Cart();

        foreach ($order->getCarts() as $orderCart){
            foreach ($orderCart->getCartLines() as $cartLine){
                $cart->addCartLine($cartLine);
                dump($cartLine->getVariant());
            }
        }
        $this->session->set('CART', $cart);

        return $this->redirectToRoute('checkout');
    }

}
