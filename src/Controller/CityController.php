<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\City;
use App\Form\CityFormType;
use App\Form\EditCityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityController extends AbstractController
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
     * @var CityRepository
     */
    private CityRepository $repository;

    /**
     * CityController constructor.
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param CityRepository $repository
     */
    public function __construct(EntityManagerInterface $manager, TranslatorInterface $translator,CityRepository $repository)
    {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * @Route("admin/city/" , name="admin_city")
     * @param Request $request
     * @return Response
     */
    public function addCity(Request $request)
    {
        $city = new City();

        $form = $this->createForm(CityFormType::class,$city);
        $form->handleRequest($request);

        $city->setProvince($city->getProvince());
        try {
            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->persist($city);
                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('City has been Added with successfully'));
            }
        }catch (\Exception $ex) {
            $this->addFlash('danger','Exc err');
        }

        return $this->render('backOffice/admin/city/city.html.twig',[
            'form'=>$form->createView(),
            'cities'=>$this->repository->findAll()
        ]);
    }

    /**
     * @Route("admin/city/edit/{id}" , name="admin_city_edit")
     * @param Request $request
     * @param City $city
     * @return Response
     */
    public function editCity(Request $request,City $city)
    {
        $form = $this->createForm(EditCityType::class,$city);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('City has been Edited with successfully'));
            }
        }catch (\Exception $ex) {
            $this->addFlash('danger','Exc err');
        }

        return $this->render('backOffice/admin/city/edit.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/cities", name="retrieve_cities", options={"expose"=true})
     *
     * @param Request        $request    The request instance
     * @param CityRepository $repository The province's repository instance
     *
     * @throws NotFoundHttpException When calling this route without ajax
     *
     * @return JsonResponse A response
     */
    public function retrieveCitiesByCountry(
        Request $request,
        CityRepository $repository
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('post')) {
                $country = $request->request->get('country');
                $cities = $repository->findByCountry(['country'=>$country],['name'=>'ASC']);

                return new JsonResponse($this->convertToArray($cities));
            }
        }

        // Simply throw a 404 error.
        throw $this->createNotFoundException('Page not found');
    }

    /**
     * @Route("/province-cities", name="retrieve_province_cities", options={"expose"=true})
     *
     * @param Request        $request    The request instance
     * @param CityRepository $repository The province's repository instance
     *
     * @throws NotFoundHttpException When calling this route without ajax
     *
     * @return JsonResponse A response
     */
    public function retrieveCitiesProvince(
        Request $request,
        CityRepository $repository
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('post')) {
                $province = $request->request->get('province');
                $cities = $repository->findByProvince(['province'=>$province],['name'=>'ASC']);

                return new JsonResponse($this->convertToArray($cities));
            }
        }

        // Simply throw a 404 error.
        throw $this->createNotFoundException('Page not found');
    }

    /**
     * @param array $data A list Provinces objects
     *
     * @return array The list of cities converted to a simple array
     */
    private function convertToArray(array $data)
    {
        $output = [];
        foreach ($data as $item) {
            $output[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
            ];
        }

        return $output;
    }
}
