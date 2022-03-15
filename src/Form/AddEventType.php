<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Town;
use App\Entity\Venue;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private $em;
//    /**
//     * @param ParticipantRepository $userRepository
//     */
//    public function setUserRepository(ParticipantRepository $userRepository): void
//    {
//        $this->userRepository = $userRepository;
//    }
//
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private $postCode;
    private $street;

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
            ->add('duration', null, ['attr' => [
                'min' => '10',
                'max' => '500',
            ]])
            ->add('eventInfo', TextareaType::class)
            ->add('campus', EntityType::class, [
                // looks for choices from this entity
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('town', EntityType::class, [

                'class' => Town::class,
                'choice_label' => 'name',
                'mapped' => false,
            ])
            ->add('street', TextType::class, [
                'attr' => array(
                    'readonly' => true,
                    'disabled' => true,
                ),
                'mapped' => false])
            ->add('venue', EntityType::class, [
                // looks for choices from this entity
                'class' => Venue::class,
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
                'placeholder' => '',
            ])
            ->add('postCode', TextType::class, [
                'attr' => array(
                    'readonly' => true,
                    'disabled' => true,
                ),
                'mapped' => false,
            ])
            /*  ->add('longitude', TextType::class,[
                  'mapped' => false,
                  'required'=>false
              ])
              ->add('latitude', TextType::class,[
                  'mapped' => false,
                  'required'=>false
              ])*/

            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier'])
            ->add('cancel', ResetType::class, ['label' => 'Annuler']);

        $formModifierTown = function (FormInterface $form, Town $town = null) {
            $venues = (null === $town) ? [] : $town->getVenues();

            // error_log(print_r($postCode, true), 3, 'C:/www.log');

            $form->add('venue', EntityType::class, [
                'class' => Venue::class,
                'placeholder' => '',
                // uses the Venue.name property as the visible option string
                'choice_label' => 'name',
                'choices' => $venues,
            ]);
        };


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifierTown) {
            $form = $event->getForm();
            $data = $event->getData();
            $town = $form['town']->getData();
            $venue = $form['venue']->getData();


            $form->add('campus', TextType::class, [
                'data' => $data->getCampus()->getName(),
                'attr' => array(
                    'readonly' => true,
                    'disabled' => true,
                ),
                'mapped' => false]);


            if ($data->getVenue()) {
                $town = $data->getVenue()->getTown();

                $form->add('street', TextType::class, [
                    'data' => $data->getVenue()->getStreet(),
                    'attr' => array(
                        'readonly' => true,
                        'disabled' => true,
                    ),
                    'mapped' => false])
                    ->add('town', EntityType::class, [

                        'class' => Town::class,
                        'choice_label' => 'name',
                        'data' => $data->getVenue()->getTown(),
                        'mapped' => false,
                    ])
                    ->add('postCode', TextType::class, [
                        'data' => $town->getPostCode(),
                        'attr' => array(
                            'readonly' => true,
                            'disabled' => true,
                        ),
                        'mapped' => false]);

            }


            $formModifierTown($form, $town);

        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($formModifierTown) {
            $form = $event->getForm();
            $data = $event->getData();


            if (isset($data['town']) && !empty($data['town'])) {
                $repository = $this->em->getRepository(Town::class);
                $town = $repository->find($data['town']);
                $data['postCode'] = $town->getPostCode();
                $formModifierTown($form, $town);
            }
            if (isset($data['venue']) && !empty($data['venue'])) {
                $repository = $this->em->getRepository(Venue::class);
                $venue = $repository->find($data['venue']);
                $data['street'] = $venue->getStreet();

            }
            $event->setData($data);

        });

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
