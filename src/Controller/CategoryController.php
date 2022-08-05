<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategorySlider;
use App\Entity\SubCategory;
use App\Form\CategorySliderType;
use App\Form\CategoryType;
use App\Form\SearchByCategoryType;
use App\Form\SubCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\CategorySliderRepository;
use App\Repository\SliderRepository;
use App\Repository\SubCategoryRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/admin")
 */
class CategoryController extends AbstractController
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
    )
    {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->translator = $translator;
        $this->imageManager = $imageManager;
    }

    //<editor-fold desc="Code Category">

    /**
     * @Route("/categories/list", name="admin_categories_list")
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function categoriesList(CategoryRepository $categoryRepository, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Category has been added'));
        }

        $categories = $categoryRepository->findAll();
        return $this->render('backOffice/admin/categories/list.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/categories/edit/{slug}" , name="admin_categories_edit")
     * @param Category $category
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editCategory(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Category has been edited'));
            }
        }

        return $this->render('backOffice/admin/categories/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categories/delete/{slug}" , name="admin_categories_delete")
     * @param Category $category
     * @return RedirectResponse
     */
    public function deleteCategory(Category $category)
    {
        $this->manager->remove($category);
        $this->manager->flush();
        $this->addFlash('success', $this->translator->trans('Category has been deleted'));

        return $this->redirectToRoute("admin_categories_list");
    }
    //</editor-fold>

    //<editor-fold desc="Code Sub Category">
    /**
     * @Route("/category-{slug}/sub-categories/list", name="admin_subCategory_list")
     * @param Category $category
     * @param SubCategoryRepository $subCategoryRepository
     * @param Request $request
     * @return Response
     */
    public function listSubCategory(Category $category, SubCategoryRepository $subCategoryRepository, Request $request): Response
    {
        //---- filter By Category
        $data = new SubCategory();
        $data->setCategory($category);
        $formFilter = $this->createForm(SearchByCategoryType::class, $data);
        $formFilter->handleRequest($request);
        if ($formFilter->isSubmitted() && $formFilter->isValid()) {
            try {
                if (null != $data->getCategory()) {
                    return $this->redirectToRoute('admin_subCategory_list', ['slug' => $data->getCategory()->getSlug()]);
                }
            } catch (\Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        //---- Add Sub Category in this category

        $subCategory = new SubCategory();
        $form = $this->createForm(SubCategoryType::class, $subCategory);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $subCategory->setCategory($category);
                $this->manager->persist($subCategory);
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Sub Category has been added'));
            }
        } catch (\Exception $ex) {
            $this->addFlash('danger', 'Exc Error');
        }

        $subCategories = $subCategoryRepository->findByCategory($category);
        return $this->render('backOffice/admin/categories/sub_categories/list.html.twig', [
            'category' => $category,
            'subCategories' => $subCategories,
            'formFilter' => $formFilter->createView(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category-{slugCategory}/sub-categories/edit/{slug}" , name="admin_subCategory_edit")
     * @param String $slugCategory
     * @param CategoryRepository $categoryRepository
     * @param SubCategory $subCategory
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editSubCategory(
        string $slugCategory,
        CategoryRepository $categoryRepository,
        SubCategory $subCategory,
        Request $request
    )
    {
        $category = $categoryRepository->findOneBy(['slug' => $slugCategory]);
        $form = $this->createForm(SubCategoryType::class, $subCategory);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->persist($subCategory);
                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Sub Category has been Edited'));

                return $this->redirectToRoute('admin_subCategory_list', ['category' => $category->getSlug()]);
            }
        } catch (\Exception $ex) {
            $this->addFlash('danger', 'Exc Err');
        }

        return $this->render('backOffice/admin/categories/sub_categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category-{slugCategory}/sub-categories/delete/{slug}" , name="admin_subCategory_delete")
     * @param String $slugCategory
     * @param CategoryRepository $categoryRepository
     * @param SubCategory $subCategory
     * @return RedirectResponse
     */
    public function deleteSubCategory(string $slugCategory, CategoryRepository $categoryRepository, SubCategory $subCategory)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slugCategory]);
        $this->manager->remove($subCategory);
        $this->manager->flush();

        return $this->redirectToRoute('admin_subCategory_list', ['slug' => $category->getSlug()]);
    }
    //</editor-fold>





    //<editor-fold desc="Code Slider category">
    /**
     * @Route("/category-{slug}/sliders/list", name="admin_categorySliders_list")
     * @param Category $category
     * @param CategorySliderRepository $categorySliderRepository
     * @return Response
     */
    public function listCategorySliders(Category $category, CategorySliderRepository $categorySliderRepository): Response
    {
        $sliders = $categorySliderRepository->findByCategory($category);
        return $this->render('backOffice/admin/categories/sliders/list.html.twig', [
            'sliders' => $sliders,
            'category'=> $category
        ]);
    }

    /**
     * @Route("/category-{slug}/sliders/new", name="admin_categorySliders_new")
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function newCategorySlider(Category $category, Request $request): Response
    {
        $categorySlider = new CategorySlider();
        $form = $this->createForm(CategorySliderType::class, $categorySlider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->addCategorySliderImage($brochureFile, $categorySlider);
            $categorySlider->setCategory($category);
            $categorySlider->setTrack(md5(uniqid()));
            $this->manager->persist($categorySlider);
            $this->manager->flush();
            dump($categorySlider);
            $this->addFlash('success', $this->translator->trans('Slider has been added'));
            return $this->redirectToRoute('admin_categorySliders_list', ['slug' => $category->getSlug()]);
        }

        return $this->render('backOffice/admin/categories/sliders/new.html.twig', [
            'form' => $form->createView(),
            'category'=> $category
        ]);
    }

    /**
     * @Route("/category-{slugCategory}/sliders/edit/{track}" , name="admin_categorySliders_edit")
     * @param string $slugCategory
     * @param CategorySlider $categorySlider
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editSlider(string $slugCategory, CategorySlider $categorySlider, CategoryRepository $categoryRepository, Request $request)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slugCategory]);
        $form = $this->createForm(CategorySliderType::class, $categorySlider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $this->imageManager->updateCategorySliderImage($brochureFile, $categorySlider);
            $this->manager->persist($categorySlider);
            $this->manager->flush();
            $this->addFlash('success', $this->translator->trans('Slider has been Edited'));

            return $this->redirectToRoute('admin_categorySliders_list', ['slug' => $category->getSlug()]);
        }

        return $this->render('backOffice/admin/categories/sliders/edit.html.twig', [
            'form' => $form->createView(),
            'category'=> $category
        ]);
    }

    /**
     * @Route("/category-{slugCategory}/slider/delete/{track}" , name="admin_categorySliders_delete")
     * @param string $slugCategory
     * @param CategoryRepository $categoryRepository
     * @param CategorySlider $categorySlider
     * @return RedirectResponse
     */
    public function deleteSlider(string $slugCategory, CategoryRepository $categoryRepository, CategorySlider $categorySlider)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slugCategory]);
        $this->imageManager->deleteCategorySliderImage($categorySlider);
        $this->manager->remove($categorySlider);
        $this->manager->flush();
        return $this->redirectToRoute('admin_categorySliders_list', ['slug' => $category->getSlug()]);
    }
    //</editor-fold>


    //<editor-fold desc="Code return Sub Category by category">
    /**
     * @param array $data A cities object
     *
     * @return array The list of cities converted to a simple array
     */
    private function convertToArray_(array $data)
    {
        $output = [];
        /** @var SubCategory $item */
        foreach ($data as $item) {
            $output[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
            ];
        }

        return $output;
    }

    /**
     * @Route("/subCategory", name="subCategory", options={"expose"=true})
     *
     * @param Request $request The request instance
     * @param SubCategoryRepository $repository The city's repository instance
     *
     * @return JsonResponse A response
     * @throws NotFoundHttpException When calling this route without ajax
     *
     */
    public function retrieveSubCategory(
        Request $request,
        SubCategoryRepository $repository
    ): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('post')) {
                $category = $request->request->get('category');
                $subCategories = $repository->findByCategory($category);

                return new JsonResponse($this->convertToArray_($subCategories));
            }
        }

        // Simply throw a 404 error.
        throw $this->createNotFoundException('Page not found');
    }
    //</editor-fold>
}
