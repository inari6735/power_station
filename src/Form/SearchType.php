<?php

namespace App\Form;

use App\Entity\Generator;
use App\Repository\GeneratorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('generator', EntityType::class, [
                'class' => Generator::class,
                'query_builder' => function (GeneratorRepository $gr) {
                    return $gr->createQueryBuilder('gr')
                        ->orderBy('gr.id', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('from', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime'
            ])
            ->add('to', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
