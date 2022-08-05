<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Customer;
use App\Entity\Province;
use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerFormType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var CityRepository
     */
    private CityRepository $cityRepository;

    /**
     * SellerFormType constructor.
     * @param TranslatorInterface $translator
     * @param CityRepository $cityRepository
     */
    public function __construct(TranslatorInterface $translator,CityRepository $cityRepository)
    {
        $this->translator = $translator;
        $this->cityRepository = $cityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country',EntityType::class,[
                'class'  => Country::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => $this->translator->trans('Choose your Country'),
                'mapped' => false
            ])
            ->add('province',EntityType::class,[
                'class' => Province::class,
                'choice_label' => 'name',
                'mapped'=>false,
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => $this->translator->trans('Choose your Province'),
                'choices' => []
            ])
            ->add('city',EntityType::class,[
                'class' => City::class,
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans('Choose your city'),
                'choices' => [],
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' =>true
            ])
            ->add('address',TextareaType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your Address'),
                    'class'=>'form-control'
                ]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var City $city */
                $city = $this->cityRepository->find($data['city']);
                $form = $arg->getForm();
                $form->getData()->setCity($city);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'validation_groups' => false
        ]);
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }
}
