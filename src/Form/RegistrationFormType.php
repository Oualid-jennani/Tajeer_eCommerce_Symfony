<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
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
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'attr' => [
                    'placeholder'=>$this->translator->trans('Enter Your password'),
                    'class'=>'form-control',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirm_password',PasswordType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Confirm password'),
                    'class'=>'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
