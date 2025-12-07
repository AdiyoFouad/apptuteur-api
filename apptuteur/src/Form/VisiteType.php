<?php

namespace App\Form;

use App\Entity\Visite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la visite',
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Prévue' => 'prévue',  // Label => Value
                    'Réalisée' => 'réalisée',
                    'Annulée' => 'annulée',
                ],
                'data' => 'prévue', // Valeur par défaut
                'label' => 'Statut',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]); 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visite::class,
        ]);
    }
}
