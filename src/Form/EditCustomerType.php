<?php

namespace App\Form;

use App\Entity\Customer;
use App\Repository\CityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditCustomerType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * SellerFormType constructor.
     * @param TranslatorInterface $translator
     * @param CityRepository $cityRepository
     */
    public function __construct(TranslatorInterface $translator,CityRepository $cityRepository)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'attr' => [
                    'placeholder'=> $this->translator->trans('Enter Your Username'),
                    'class'=>'form-control'
                ]
            ])
            ->add('email',EmailType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your Email'),
                    'class'=>'form-control'
                ]
            ])
            ->add('phoneNumber',TextType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your Phone Number'),
                    'class'=>'form-control'
                ]
            ])
            ->add('fullName',TextType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your Full Name'),
                    'class'=>'form-control'
                ]
            ])
            ->add('address',TextareaType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your Address'),
                    'class'=>'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
