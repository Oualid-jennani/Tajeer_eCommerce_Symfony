<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/dash/login", name="security_back_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser()) return $this->redirectToRoute('dashboard');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/back_login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginCustomer(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/front_login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/dash",name="dashboard")
     */
    public function dashboard(): Response
    {
        if ($this->getUser()) {
            $role = $this->getUser()->getRoles();
            $routes = [
                'ROLE_SUPER_ADMIN' => 'dashboard_super_admin',
                'ROLE_ADMIN' => 'dashboard_admin',
                'ROLE_SELLER' => 'dashboard_seller'
            ];

            if (true === \array_key_exists($role[0], $routes)) {
                return $this->redirectToRoute($routes[$role[0]]);
            }
        }

        return $this->redirectToRoute('index');
    }
}
