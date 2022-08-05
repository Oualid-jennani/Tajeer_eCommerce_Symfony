<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminFormType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SuperAdminController
 * @package App\Controller
 * @Route("/super/admin")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * SuperAdminController constructor.
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="super_admin")
     */
    public function index(): Response
    {
        return $this->render('backOffice/super_admin/index.html.twig');
    }

    /**
     * @Route("/add-admin",name="super_admin_add_admin")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordHasher
     * @return Response
     */
    public function addAdmin(Request $request,UserPasswordEncoderInterface  $passwordHasher)
    {
        $admin = new Admin();

        $form = $this->createForm(AdminFormType::class,$admin);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $admin->setCreatedAt(new \DateTime('now'));
                $admin->setPassword(
                    $passwordHasher->encodePassword(
                        $admin,
                        $form->get('password')->getData()
                    )
                );
                $this->manager->persist($admin);
                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('Admin has been added with successfully'));
            }
        }catch(\Exception $ex) {
            $this->addFlash('danger',$ex->getMessage());
        }

        return $this->render('backOffice/super_admin/admin/add_admin.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admins",name="super_admin_admins")
     * @param AdminRepository $adminRepository
     * @return Response
     */
    public function admins(AdminRepository $adminRepository)
    {
        $admins = $adminRepository->findAll();

        return $this->render('backOffice/super_admin/admin/admins.html.twig',[
            'admins' => $admins
        ]);
    }
}
