<?php

namespace App\Form;

use App\Entity\Condidat;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CorrecteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('correcteur', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.roles', 'ASC')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"'.'ROLE_Correcteur'.'"%');

                },
                'choice_label' => 'first_name',
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          //  'data_class' => Condidat::class,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
