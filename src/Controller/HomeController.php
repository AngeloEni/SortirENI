<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Status;
use App\Entity\Venue;
use App\Form\AddEventType;
use App\Form\SearchEventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StatusRepository;
use App\Repository\VenueRepository;


use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Cast\Int_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/internal")
 */
class HomeController extends AbstractController
{
    private function getParticipantUser(): Participant
    {
        return $this->getUser();
    }
    /**
     * @Route("/", name="home")
     */

//je crée le formulaire dès le chargement de la page
    public function showAll(Request $req, EventRepository $eventRepo, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        $this->updateStatus($eventRepo, $em, $statusRepository);


        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1

        $user = $this->getParticipantUser();
        $eventsSorted =array();

        $events = $eventRepo->findAll();

        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterModel = $form->getData();

            $events = $eventRepo->findByFilters($eventFilterModel, $user);

        }
        // triage de events pour ne pas avoir les statuts archived et les created ou l'utilisateur n est pas organiseur
        foreach ($events as $event){
            if ( $event->getStatus()->getDescription()!= "Archived"
                and !($event->getOrganizer()->getId() != $user->getId() and $event->getStatus()->getDescription() == "Created")){
                array_push($eventsSorted, $event);
            }
        }

        return $this->render('/home.html.twig', [
            'events' => $eventsSorted,
            'form' => $form->createView(),
            'user' => $user,
            'now' => $now,
        ]);
    }


    /**
     * @Route("/addEvent", name="addEvent")
     */

    public function addEvent(Request $req, EntityManagerInterface $em, StatusRepository $statusRepo): Response
    {
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1
        $user = $this->getParticipantUser();
        $statusCreated = new Status();
        $statusOpen = new Status();
       // $newVenue = new Venue();

        $event = new Event(); // je crée une sortie et un lieu
        //$venues = $venueRepo->findAll();
        // creation du form avec asso. avec $event

        $statusAll = $statusRepo->findAll();

        foreach ($statusAll as $s) {
            if ($s->getDescription() == "Open") {
                $statusOpen = $s;
            }
            if ($s->getDescription() == "Created") {
                $statusCreated = $s;
            }
        }


        $campus = $user->getCampus();
        $event->setDateTimeStart($now);
        $event->setRegistrationClosingDate($now);
        $event->setCampus($campus);
        $event->setOrganizer($user);

        $form = $this->createForm(AddEventType::class, $event);

        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {

            $isClickedPublish = $form->get('publish')->isClicked();
            $isClickedSave = $form->get('save')->isClicked();


            if ($isClickedPublish) {
                $event->setStatus($statusOpen);
            }
            if ($isClickedSave) {
                $event->setStatus($statusCreated);
            }

            if ($isClickedPublish || $isClickedSave) {
                $em->persist($event);
                $em->flush();
                return $this->redirectToRoute('home');
            }
            //si c'est une requête ajax, il n'entrera pas dans le if

        }
        return $this->render('event/add.html.twig', [
            'addEventForm' => $form->createView(),
        ]);
    }


    public function updateStatus(EventRepository $eventRepository, EntityManagerInterface $em, StatusRepository $statusRepository)
    {
        // Récupérer la table des events et date du jour
        $allEvent = $eventRepository->findAll();
        $dateTimeNow = new \DateTime();





        $statusArchived = $statusRepository->findBy(array('description' => "Archived"));
        $statusEnded = $statusRepository->findBy(array('description' => "Ended"));
        $statusOngoing = $statusRepository->findBy(array('description' => "Ongoing"));
        $statusClosed = $statusRepository->findBy(array('description' => "Closed"));

        //rajouter en base de donnée le cas Archived
        //itérer dans la table pour tester la date

        foreach ($allEvent as $event) {
            $dateEvent = $event->getDateTimeStart();
            $dateRegistration = $event->getRegistrationClosingDate();
            $interval = $dateEvent->diff($dateTimeNow);
            $duration = $event->getDuration();



            //évenement Closed filtre
            if( $event->getStatus()->getDescription() == "Open" and $dateTimeNow > $dateRegistration){
                $event->setStatus($statusClosed[0]);
                $em->persist($event);
            }

            //évenement Ongoing filtre
            if ($event->getStatus()->getDescription() == "Closed" and $dateTimeNow > $dateEvent and $dateTimeNow < $dateEvent->modify('+'.$duration.' minutes')){
                $event->setStatus($statusOngoing[0]);
                $em->persist($event);
            }



            // évenement Ended filtre
            if ($event->getStatus()->getDescription() == "Ongoing" and $dateEvent->modify('+'.$duration.' minutes') < $dateTimeNow) {
                $event->setStatus($statusEnded[0]);
                $em->persist($event);
            }

            // évenement Archived filtre
            if ( $event->getStatus()->getDescription() == "Ended" and  $interval->days > 31 and $dateEvent < $dateTimeNow) {
                //réaliser un tableau des des évenements à update
                $event->setStatus($statusArchived[0]);
                $em->persist($event);
            }


        }


        $em->flush();

    }
}



