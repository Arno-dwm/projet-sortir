<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
            ])
            ->add('site', TextType::class, [
                'label' => 'Ville organisatrice',
                'data' => $user ? $user->getSite()->getNom() : '',
                'mapped' => false,
                'disabled' => true,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et Heure de la sortie',
                'widget' => 'single_text',
            ])
            ->add('ville', ChoiceType::class, [
                'label' => 'Ville',
                'choices' => array_combine(
                    ['Quimper', 'Nantes', 'Niort', 'Rennes'],
                    ['Quimper', 'Nantes', 'Niort', 'Rennes']
                ),
                'mapped' => false,
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un lieu',
                'attr' => ['id' => 'sortie_lieu'],

            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                ]
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue',
                'mapped' => false,
                'attr' => [
                    'readonly' => true,

                ],
                'required' => false,
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée en minutes',
                'attr' => [
                    'min' => 0,
                    'step' => 5,
                ]
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'attr' => [
                    'readonly' => true,

                ],
                'required' => false,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'mapped' => false,
                'scale' => 8,
                'attr' => [
                    'readonly' => true,
                    'step' => 0.00000001,

                ],
                'required' => false,
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'mapped' => false,
                'scale' => 8,
                'attr' => [
                    'readonly' => true,
                    'step' => 0.00000001,

                ],
                'required' => false,
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', ['null', User::class]);
    }

}
