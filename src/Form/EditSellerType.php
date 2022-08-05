<?php

namespace App\Form;

use App\Entity\Seller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditSellerType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * RegistrationFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
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
            ->add('cin',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Enter Your Cin/Id')
                ]
            ])
            ->add('storeName',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Enter your stroe name')
                ]
            ])
            ->add('storePhone',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Enter store phone')
                ]
            ])
            ->add('address',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Enter address')
                ]
            ])
            ->add('localisation',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Localisation')
                ]
            ])
            ->add('about',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('About')
                ]
            ])
            ->add('description',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Description')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
