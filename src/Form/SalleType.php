<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Salle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,
                'choice_label' => 'nom',
                'label' => 'Cinema',
                'placeholder' => 'Choisir un cinema',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('numero', IntegerType::class, [
                'label' => 'Numero de salle',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacite (nombre de places)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateConstruction', DateType::class, [
                'label' => 'Date de construction',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Salle::class]);
    }
}
