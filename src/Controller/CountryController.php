<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryFormType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CountryController extends AbstractController
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
     * @var CountryRepository
     */
    private CountryRepository $repository;

    /**
     * CountryController constructor.
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param CountryRepository $repository
     */
    public function __construct(EntityManagerInterface $manager,TranslatorInterface $translator,CountryRepository $repository)
    {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * @Route("admin/country/",name="admin_country")
     * @param Request $request
     * @return Response
     */
    public function country(Request $request)
    {
        $country = new Country();

        $form = $this->createForm(CountryFormType::class,$country);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->persist($country);
                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('Country has been added with successfully'));
            }
        }catch (\Exception $ex) {
            $this->addFlash('danger',$this->translator->trans('Exc Err'));
        }

        return $this->render('backOffice/admin/country/country.html.twig',[
            'form' => $form->createView(),
            'countries' => $this->repository->findBy([],['name'=>'ASC'])
        ]);
    }


    /**
     * @Route("/countries", name="countries")
     */
    public function index(): Response
    {
        return $this->render('backOffice/country/country.html.twig', [
            'controller_name' => 'CountryController',
        ]);
    }
}
