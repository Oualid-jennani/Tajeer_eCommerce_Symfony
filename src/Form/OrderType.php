<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Province;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\ProvinceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /** @var TranslatorInterface */
    private TranslatorInterface $translator;

    /** @var CityRepository */
    private CityRepository $cityRepository;

    /** @var ProvinceRepository */
    private ProvinceRepository $provinceRepository;

    /** @var Customer */
    private $customer;

    /**
     * SellerFormType constructor.
     * @param TranslatorInterface $translator
     * @param CityRepository $cityRepository
     * @param ProvinceRepository $provinceRepository
     * @param Security $security
     */
    public function __construct(
        TranslatorInterface $translator,
        CityRepository $cityRepository,
        ProvinceRepository $provinceRepository,
        Security $security)
    {
        $this->translator = $translator;
        $this->cityRepository = $cityRepository;
        $this->provinceRepository = $provinceRepository;
        $this->security = $security;

        if($this->security->getUser() != null){
            $this->customer = $this->security->getUser();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $provinces = [];
        $cities = [];
        $dataCountry = null;
        $dataProvince = null;
        $dataCity = null;

        if($this->security->getUser() != null){
            $provinces = $this->provinceRepository->findByCountry($this->customer->getCity()->getCountry());
            $cities = $this->cityRepository->findByProvince($this->customer->getCity()->getProvince());
            $dataCountry = $this->customer->getCity()->getCountry();
            $dataProvince = $this->customer->getCity()->getProvince();
            $dataCity =$this->customer->getCity();
            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $arg) {
                    $data = $arg->getData();
                    $data->setFullName($this->customer->getFullName());
                    $data->setPhone($this->customer->getPhoneNumber());
                    $data->setCity($this->customer->getCity());
                    $data->setAddress($this->customer->getAddress());
                });
        }

        $builder
            ->add('fullName')
            ->add('phone',TextType::class)
            ->add('country',EntityType::class,[
                'class'  => Country::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => $this->translator->trans('Choose your Country'),
                'mapped' => false,
                'data' => $dataCountry,
            ])
            ->add('province',EntityType::class,[
                'class' => Province::class,
                'choice_label' => 'name',
                'mapped'=>false,
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => $this->translator->trans('Choose your Province'),
                'choices' => $provinces,
                'data' => $dataProvince,
            ])
            ->add('city',EntityType::class,[
                'class' => City::class,
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans('Choose your city'),
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' =>true,
                'choices' => $cities,
                'data' => $dataCity,
            ])
            ->add('address',TextareaType::class)
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
            'data_class' => Order::class,
            'validation_groups' => false
        ]);
    }
}
