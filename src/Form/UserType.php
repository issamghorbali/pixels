<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('picture', FileType::class, [
                'label' => false,
                'mapped' => false, // Tell that there is no Entity to link
                'required' => true,
                'constraints' => [ ],
            ])
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'invalid_message' => 'Password fields must be the same',
                'mapped' => false,
                'required'   => false,
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                // 'placeholder'=>"Select a role",
                'choices' => [

                    'ROLE_Expert' => 'ROLE_Expert',
                    'ROLE_Correcteur' => 'ROLE_Correcteur',
                ],

            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Password fields must be the same',
                'mapped' => false,
                'required'   => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'groups'=>["Signup","Signemp"]
                    ]),
                    new NotBlank([
                        'message' => 'Enter your password',
                        'groups' => 'password_update',
                        'groups'=>["Signup","Signemp"]
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
