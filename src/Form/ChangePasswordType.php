<?php

namespace App\Form;

use App\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;


class ChangePasswordType extends AbstractType
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
            ->add('oldPassword', PasswordType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('Current password'),
                    'class'=>'form-control'
                ]
            ])
            ->add('newPassword', PasswordType::class,[
                'attr' => [
                    'placeholder'=>$this->translator->trans('New password'),
                    'class'=>'form-control'
                ]
            ])
            ->add('confirmNewPassword', PasswordType::class,[
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
            'data_class' => ChangePassword::class,
        ]);
    }


}
