<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Province;
use App\Repository\ProvinceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditCityType extends AbstractType
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
     * EditCityType constructor.
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
        $city = $builder->getData();
        $builder
            ->add('province', EntityType::class, [
                'class' => Province::class,
                'placeholder' => $this->translator->trans('Choose Province'),
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'choices'=> $this->provinceRepository->findByCountry($city->getCountry())
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var Province $province */
                $province = $this->provinceRepository->find($data['province']);
                $form = $arg->getForm();
                $form->getData()->setProvince($province);
            })
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }

    public function getParent()
    {
        return CityFormType::class; // TODO: Change the autogenerated stub
    }
}
