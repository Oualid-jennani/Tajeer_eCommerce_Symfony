<?php

namespace App\Form;

use App\Entity\CategorySlider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategorySliderType extends AbstractType
{

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * SubCategoryType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Title')
                ]
            ])
            ->add('url',TextType::class,[
                'required'=>false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Url Shop'),
                ]
            ])
            ->add('description',TextareaType::class,[
                'required'=>false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Description')
                ]
            ])
            ->add('brochure', FileType::class, [
                'label' => $this->translator->trans('Choose image'),
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategorySlider::class,
        ]);
    }
}
