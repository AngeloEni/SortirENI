<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Status;
use App\Form\AddEventType;
use App\Form\SearchEventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StatusRepository;
use App\Repository\VenueRepository;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/internal")
     */


class HomeController extends AbstractController
{
    private function getParticipantUser():Participant {
        return $this->getUser();
    }
    /**
     * @Route("/", name="home")
     */

//je crée le formulaire dès le chargement de la page
    public function showAll(Request $req, EventRepository $eventRepo, ParticipantRepository $partiRepo, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        $this->updateStatus($eventRepo, $em, $statusRepository);


        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1

        $user = $this->getParticipantUser();
        //$participant = new Participant();
       //participants = $partiRepo->findAll();

        $events = $eventRepo->findAll();
        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterModel = $form->getData();

            $events = $eventRepo->findByFilters($eventFilterModel, $user);

        }


        return $this->render('/home.html.twig', [
            'events' => $events,
            'form' => $form->createView(),
            'user'=>$user,
            'now' => $now,
        ]);
    }


    /**
     * @Route("/addEvent", name="addEvent")
     */

    public function addEvent(Request $req, EntityManagerInterface $em, StatusRepository $statusRepo, ParticipantRepository $partiRepo): Response
    {
        $user = $this->getParticipantUser();
        $statusCreated = new Status();
        $statusOpen = new Status();

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
        $event->setCampus($campus);
        $event->setOrganizer($user);

        $form = $this->createForm(AddEventType::class, $event);

        // $form->get('campus')->setData($campus);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('publish')->isClicked()) {
                $event->setStatus($statusOpen);
            }
            if ($form->get('save')->isClicked()) {
                $event->setStatus($statusCreated);
            }


            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('home');

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

        //rajouter en base de donnée le cas ARchived
        //itérer dans la table pour tester la date

        foreach ($allEvent as $event) {
            $dateEvent = $event->getDateTimeStart();
            $interval = $dateEvent->diff($dateTimeNow);

            // évenement en cours
            if ($dateEvent > $dateTimeNow) {
                $event->setStatus($statusEnded[0]);
                $em->persist($event);
            }
            if ($interval->days > 31 and $dateEvent > $dateTimeNow) {
                //réaliser un tableau des des évenements à update
                $event->setStatus($statusArchived[0]);
                $em->persist($event);
            }


        }


        $em->flush();

    }
}



