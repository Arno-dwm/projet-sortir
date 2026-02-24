<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class,[
                'label' => 'Pseudo'
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('nom', TextType::class,[
                'label' => 'Nom'
            ])
            ->add('telephone', TextType::class,[
                'label' => 'Téléphone'
            ])
            ->add('mail', TextType::class,[
                'label' => 'Email'
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
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
                        /* TODO décommenter pour difficulter de mot de passe
                         * new PasswordStrength(
                        # il existe 4 niveau voir la doc Symfony
                            minScore: PasswordStrength::STRENGTH_MEDIUM,
                        ),*/
                       /* TODO décommenter pour vérifier si compromis
                       new NotCompromisedPassword(),
                        # vérifier si le password est compromis
                       */
                    ],
                    'label' => 'Votre mot de passe',
                ],
                'second_options' => [
                    'label' => 'confirmez votre mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',

            ])
            ->add('Ville de rattachement', EntityType::class, [
                'label' => 'Site',
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
            ->add('lienImgFile', FileType::class, [
                'mapped' => false, // car le champs 'lienImgFile n'est pas dans Wish c'est lienImg
                'label' => 'Imagede profil',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Votre fichier est trop lourd. Max: 1Mo',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'format acceptés : jpeg, jpg, png'
                    ])
                ]
            ])

            ->add('Accepter les conditions d\'utilisations', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary mx-auto d-block',
                ]
            ])
        ;
          /*  ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
