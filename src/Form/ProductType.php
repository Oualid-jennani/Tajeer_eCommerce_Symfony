<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductType extends AbstractType
{
    /**
     * @var SubCategoryRepository
     */
    private $subCategoryRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Security
     */
    private $security;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;


    /**
     * ProductType constructor.
     * @param SubCategoryRepository $subCategoryRepository
     * @param CategoryRepository $categoryRepository
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(
        SubCategoryRepository $subCategoryRepository,
        CategoryRepository $categoryRepository,
        Security $security,
        TranslatorInterface $translator)
    {
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var Category $category */
        $category = $options['category'];

        if($category == null){

        }

        $builder
            ->add('name',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Product Name')
                ]
            ])
            ->add('description',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Product Descroption')
                ]
            ])
            ->add('brochures', FileType::class, [
                'attr' =>
                    [
                        'label' => 'Brochure (Image file)',
                        'accept'=>'image/*'
                    ],
                'required' => false,
                'multiple'=>true
            ])
            ->add('price',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'number',
                    'step'=>'0.01',
                    'placeholder'=> $this->translator->trans('Price')
                ]
            ])
            ->add('compareAtPrice',NumberType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'number',
                    'step'=>'0.01',
                    'placeholder'=> $this->translator->trans('Compare price')
                ]
            ])
            ->add('deliveryTime',ChoiceType::class,[
                'attr' => [
                    'class'=>'form-select'
                ],
                'required' => true,
                'choices' => ['24h'=>'24h','48h'=>'48h',
                        $this->translator->trans('3d')=>'3d',
                    $this->translator->trans('1 week')=>'1week',
                    $this->translator->trans('2 week')=>'2week',
                    $this->translator->trans('3 week')=>'3week',
                    $this->translator->trans('1 month')=>'1month',
                ],

            ])
            ->add('productType',ChoiceType::class,[
                'attr' => [
                    'class' => 'form-select'
                ],
                'choices'=>[
                    'Local'=>'Local',
                    'DropShipping'=>'DropShipping',
                ]
            ])
            ->add('expeditionCountry',ChoiceType::class,[
                'attr' => [
                    'class' => 'form-select'
                ],
                'choices'=>[
                    'Morocco'=>'Morocco',
                    'China'=>'China',
                    'USA'=>'USA',
                    'Turkey'=>'Turkey'
                ]
            ])
            ->add('tags',TextType::class,['required' => false,])
            ->add('variants', CollectionType::class, [
                'entry_type' => ProductVariantType::class,
                'entry_options' => ['label' => false,],
                'required' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
            ])
            ->add('video',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('video')
                ],
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'attr' => [
                    'class' => 'form-select',
                    'placeholder' => $this->translator->trans('Choose a Category')
                ],
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => true,
            ])

            ->add('subCategory', EntityType::class, [
                'attr' => [
                    'class' => 'form-select',
                    'placeholder' => $this->translator->trans('Choose a sub Category')
                ],
                'class' => SubCategory::class,
                'choices' => $this->subCategoryRepository->findByCategory($category),
                'choice_label' => 'name',
                'required' => false
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                if(isset($data['subCategory'])){
                    /** @var SubCategory $subCategory */
                    $subCategory = $this->subCategoryRepository->find($data['subCategory']);
                    $form = $arg->getForm();
                    $form->getData()->setSubCategory($subCategory);
                }
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => false
        ]);
        $resolver->setRequired([
            'category',
        ]);
    }
}
