<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchByCategoryType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;


    /**
     * SearchByCategoryType constructor.
     * @param CategoryRepository $categoryRepository
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(CategoryRepository $categoryRepository, Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans('Choose a Category')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
