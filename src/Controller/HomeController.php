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
    /**
     * @Route("/", name="home")
     */
//je crée le formulaire dès le chargement de la page
    public function showAll(Request $req, EventRepository $eventRepo, ParticipantRepository $partiRepo, EntityManagerInterface $em , StatusRepository  $statusRepository): Response
    {
        $this->updateStatus($eventRepo,$em,$statusRepository);


        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1

        //--------------Controlle de la BDD et des evenement à archiver---------------------------------------

        if ($now)

        //----------------------------------------------------------------------------------------------------

        $user = $this->getUser();
        $participant = new Participant();
        $participants = $partiRepo->findAll();

        foreach ($participants as $p) {
            if ($p->getEmail() == $user->getUserIdentifier()) {
                $participant = $p;
            }
        }


        $events = $eventRepo->findAll();
        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($req);




        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterModel = $form->getData();

            $events = $eventRepo->findByFilters( $eventFilterModel, $user);

        }


        return $this->render('/home.html.twig', [
            'events' => $events,
            'form' => $form->createView(),
            'user'=>$user,
            'now'=>$now,
            'participant'=>$participant,
        ]);
    }


    /**
     * @Route("/addEvent", name="addEvent")
     */

    public function addEvent(Request $req, EntityManagerInterface $em): Response
    {
        $event = new Event(); // je crée une sortie
        // creation du form avec asso. avec $event

        $form =  $this->createForm(AddEventType::class,$event);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('publish')->isClicked()) {
                $event->setStatus(1);
            }
            if ($form->get('save')->isClicked()) {
                $event->setStatus(2);
            }
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('home.html.twig');

    }
        return $this->render('add.html.twig', [
            'addEventForm' => $form->createView(),
        ]);
    }


    public function updateStatus (EventRepository $eventRepository, EntityManagerInterface $em , StatusRepository  $statusRepository) {
        // Récupérer la table des events et date du jour

        $allEvent = $eventRepository->findAll();
        $dateTimeNow = new \DateTime();
        $eventToOld = Array ();
        $satut = $statusRepository->findBy(array('description' => "Archived"));

    //rajouter en base de donnée le cas ARchived
        //itérer dans la table pour tester la date

        foreach ($allEvent as $event){
            $dateEvent = $event->getDateTimeStart();
            $interval = $dateEvent->diff($dateTimeNow);
                if ($interval->days > 31 and $dateEvent<$dateTimeNow){
                    //réaliser un tableau des des évenements à update
                    array_push($eventToOld, $event);
                }

        }
        //itérer dans le tableau pour update le status des event passé

        foreach ($eventToOld as $oldEvent){

            $oldEvent->setStatus($satut[0]);
            $em->persist($oldEvent);
        }
            $em->flush();

    }


}
