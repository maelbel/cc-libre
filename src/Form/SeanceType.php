<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => fn (Film $film) => sprintf('%s (%d)', $film->getTitre(), $film->getAnnee()),
                'label' => 'Film',
                'placeholder' => 'Choisir un film',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => fn (Salle $salle) => sprintf('%s - Salle %d', $salle->getCinema()?->getNom(), $salle->getNumero()),
                'label' => 'Salle',
                'placeholder' => 'Choisir une salle',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de la seance',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tarif', MoneyType::class, [
                'label' => 'Tarif',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('horaire', HoraireType::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Seance::class]);
    }
}
