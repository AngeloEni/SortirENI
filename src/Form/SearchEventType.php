<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Form\Model\EventFilterModel;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('campus', EntityType::class, [
            'class'=>Campus::class,
               'choice_value' => function (?Campus $entity) {
                   return $entity ? $entity->getId() : '';
               },
            'choice_label'=>'name',
               'required' =>false,])

            ->add('name', TextType::class, [
                'required' =>false,])

            ->add('earliestDate',DateType::class, [
                'widget' => 'single_text',
                'format'=>'yyyy-MM-dd',
                'required' =>false,])

            ->add('latestDate',DateType::class, [
                'widget' => 'single_text',
                'format'=>'yyyy-MM-dd',
                'required' =>false,])

            ->add('myOrganisedEvents', CheckboxType::class, ['label'=> "Sorties dont je suis l'organitatrice/eur",
                'required' => false,])

            ->add('myEvents', CheckboxType::class, ['label'=> 'Sorties auxquelles je suis inscrit(e)',
                'required' => false,])

            ->add('otherEvents', CheckboxType::class, ['label'=> 'Sorites auxquelles je ne suis pas inscrit(e)',
                'required' => false,])

            ->add('pastEvents', CheckboxType::class, ['label'=> 'Sorties passées',
                'required' => false,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventFilterModel::class,
        ]);
    }
}
