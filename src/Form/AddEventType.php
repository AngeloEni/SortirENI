<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Town;
use App\Entity\Venue;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEventType extends AbstractType
{
    /**
     * @param ParticipantRepository $userRepository
     */
    public function setUserRepository(ParticipantRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }
    public function __construct(ParticipantRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, $options): void
    {

        $builder
            ->add('name', TextType::class)
            ->add('dateTimeStart', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('registrationClosingDate', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('maxParticipants', TextType::class)
            ->add('duration', null, array('attr' => array(
                'min' => '10',
                'max' => '500',
            )))
            ->add('eventInfo', TextareaType::class,)
            ->add('campus', EntityType::class, [
                // looks for choices from this entity
                'class' => Campus::class,
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
            ])
            ->add('town', EntityType::class, [
                // looks for choices from this entity
                'class' => Town::class,
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
                'placeholder' => '',
                'mapped' => false,
            ])
            ->add('venue', EntityType::class, [
                // looks for choices from this entity
                'class' => Venue::class,
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
                'placeholder' => '',
              ])
            ->add ('street', TextType::class, [
                'mapped' => false,
            ])
            ->add('postCode', TextType::class,[
            'mapped' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier'])
            ->add('cancel', ResetType::class, ['label' => 'Annuler']);

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $form = $event->getForm();
//           // $data = $event->getData();
//            $town = $form['town']->getData();
//           //$town= $data->get('town');
//
//            $venues = (null === $town) ? [] : $town->getVenues();
//            var_dump($town);
//            var_dump($venues);
//           // var_dump($form);
//            $form->add('venue', EntityType::class, [
//                'class' => Town::class,
//                'placeholder' => '',
//                'choices' => $venues,
//            ]);
//
//        });

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
