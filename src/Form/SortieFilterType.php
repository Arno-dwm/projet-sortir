<?php

namespace App\Form;

use App\DTO\SortieFilterDTO;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',   // propriété affichée dans <option>
                'placeholder' => 'Choisir un site',
                'required' => false,
            ])
            ->add('inputSearch', TextType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient :',
            ])
            ->add('dateMin', DateType::class, [
                'required' => false,
                'widget' => 'single_text', // important pour input HTML5
                'label' => 'Entre :',
            ])
            ->add('dateMax', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'et :',
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'required' => false,
                'label' => "Sortie dont je suis l'organisateur.ice",
            ])
            ->add('isInscrit', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties auxquelles je suis inscrit.e",
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties auxquelles je ne suis pas inscrit.e",
            ])
            ->add('ended', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties passées",
            ])
        ;
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortieFilterDTO::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
