<?php

namespace App\Controller;

use App\Entity\Province;
use App\Form\ProvinceFormType;
use App\Repository\ProvinceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProvinceController extends AbstractController
{
    /**
     * @var ProvinceRepository
     */
    private ProvinceRepository $provinceRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * ProvinceController constructor.
     * @param ProvinceRepository $provinceRepository
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProvinceRepository $provinceRepository ,
        EntityManagerInterface $manager,
        TranslatorInterface $translator)
    {
        $this->provinceRepository = $provinceRepository;
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
     * @Route("admin/province/" , name="admin_province")
     * @param Request $request
     * @return Response
     */
    public function addProvince(Request $request): Response
    {
        $province = new Province();
        $form = $this->createForm(ProvinceFormType::class,$province);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->persist($province);
                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('Province added with successfully'));
            }
        }catch (\Exception $exception){
            $this->addFlash('danger','Error Excp');
        }
        $provinces = $this->provinceRepository->findAll();
        return $this->render('backOffice/admin/province/province.html.twig', [
            'form' => $form->createView(),
            'provinces' => $provinces
        ]);
    }

    /**
     * @Route("admin/province/edit/{id}" , name="admin_province_edit")
     * @param Request $request
     * @param Province $province
     * @return Response
     */
    public function editProvince(Request $request,Province $province): Response
    {
        $form = $this->createForm(ProvinceFormType::class,$province);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                $this->addFlash('success',$this->translator->trans('Province Edited with successfully'));
            }
        }catch (\Exception $exception){
            $this->addFlash('danger','Error Excp');
        }
        $provinces = $this->provinceRepository->findAll();
        return $this->render('backOffice/admin/province/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/provinces", name="retrieve_provinces", options={"expose"=true})
     *
     * @param Request        $request    The request instance
     * @param ProvinceRepository $repository The province's repository instance
     *
     * @throws NotFoundHttpException When calling this route without ajax
     *
     * @return JsonResponse A response
     */
    public function retrieveProvinces(
        Request $request,
        ProvinceRepository $repository
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('post')) {
                $country = $request->request->get('country');
                $provinces = $repository->findByCountry(['country'=>$country],['name'=>'ASC']);

                return new JsonResponse($this->convertToArray($provinces));
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
