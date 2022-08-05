<?php

namespace App\Form;

use App\Entity\Variant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductVariantType extends AbstractType
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
            ->add('unity',ChoiceType::class,[
                'choices'=>[
                    'Standard'=>'standard',
                    'SM'=>'sm',
                    'M'=>'m',
                    'L'=>'l',
                    'XL'=>'xl'
                ],
                'attr'=>[
                    'class'=>'single-select',
                ]
            ])
            ->add('price',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Price'),
                ]
            ])
            ->add('compareAtPrice',NumberType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> $this->translator->trans('Compare price')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Variant::class,
        ]);
    }
}
