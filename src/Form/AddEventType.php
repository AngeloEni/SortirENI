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
use Symfony\Component\Form\FormInterface;
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
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('registrationClosingDate', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('maxParticipants', TextType::class)
            ->add('duration', null, array('attr' => array(
                'min' => '10',
                'max' => '500',
            )))
            ->add('eventInfo', TextareaType::class)
            ->add('campus', TextType::class, array(
                'attr' => array(
                    'readonly' => true,
                    'disabled' => true,
                )))
            ->add('town', EntityType::class, [
                // looks for choices from this entity
                'class' => Town::class,
                // uses the Town.name property as the visible option string
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
            ->add('street', TextType::class, array(
                'attr' => array(
                    'readonly' => true,
                    'disabled' => true,
                ),
                'mapped' => false))

            ->add('postCode', TextType::class, array(
                   'mapped' => false,
               ))

            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier'])
            ->add('cancel', ResetType::class, ['label' => 'Annuler']);

        $formModifier = function (FormInterface $form, Town $town = null, Venue $venue = null) {
            $venues = (null === $town) ? [] : $town->getVenues();
            $postCode = (null === $town) ? '' : $town->getPostCode();
            $street = (null === $venue) ? '' : $venue->getStreet();

            error_log(print_r($venue, true), 3, 'C:/annest.log');

            $form->add('venue', EntityType::class, [
                'class' => Venue::class,
                'placeholder' => '',
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
                'choices' => $venues,
            ]);

            $form->remove('postCode');
            $form->add('postCode', TextType::class, array(
                'data' => $postCode,
                'mapped' => false,
            ));

            $form->add('street', TextType::class, array(
                'attr' => array(
                    'readonly' => true,
                ),
                'data' => $street,
                'mapped' => false,
            ));
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $form = $event->getForm();
            $town = $form['town']->getData();
            $venue = $form['venue']->getData();

            $formModifier($form, $town, $venue);
        });
        $builder->get('town')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $town = $event->getForm()->getData();
            //$this->venue = $town
            $formModifier($event->getForm()->getParent(), $town, null);
        });

        $builder->get('venue')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {

            $venue = $event->getForm()->getData();
            error_log('toto', 3, 'C:/toto.log');
            $formModifier($event->getForm()->getParent(), null, $venue);

        });
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
