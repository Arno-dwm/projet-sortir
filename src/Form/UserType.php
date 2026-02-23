<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class,[
                'label' => 'Pseudo'
            ])
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('mail')
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'mapped' => false,
                'first_options' => [
                    'constraints' => [
                        new NotBlank(
                            message: 'Please enter a password',
                        ),
                        new PasswordStrength(
                        # il existe 4 niveau voir la doc Symfony
                            minScore: PasswordStrength::STRENGTH_MEDIUM,
                        ),
                        new NotCompromisedPassword(),
                        # vérifier si le password est compromis
                    ],
                    'label' => 'Votre mot de passe',
                ],
                'second_options' => [
                    'label' => 'confirmez votre mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',

            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
