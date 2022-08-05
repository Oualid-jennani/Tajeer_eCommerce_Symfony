<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Province;
use App\Repository\ProvinceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityFormType extends AbstractType
{
    /**
     * @var ProvinceRepository
     */
    private ProvinceRepository $provinceRepository;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * CityFormType constructor.
     * @param ProvinceRepository $provinceRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(ProvinceRepository $provinceRepository,TranslatorInterface $translator)
    {
        $this->provinceRepository = $provinceRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var City $city */
        $city = $options['data'];

        $builder
            ->add('name',TextType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('country',EntityType::class,[
                'class' => Country::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => $this->translator->trans('Choose a Country')
            ])
            ->add('province',EntityType::class,[
                'class' => Province::class,
                'choices' => [],
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-select'
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var Province $province */
                $province = $this->provinceRepository->find($data['province']);
                $form = $arg->getForm();
                $form->getData()->setProvince($province);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            'validation_groups' => false,
        ]);
    }
}
