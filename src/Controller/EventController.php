<?php

namespace App\Controller;

use App\Entity\Event;

use App\Form\AddEventType;
use App\Form\CancelEventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
    /**
     * @Route("/internal")
     */


class EventController extends AbstractController
{

    /**

     * @Route("/event/{id}", name="app_event")
     */
    public function index(int $id, EventRepository $eventRepository, EntityManagerInterface $em, ParticipantRepository $pr): Response
    {
        $event = $eventRepository->find($id);

        $eventParticipant = $event->getParticipants();

        $arrayParticipant = array();

        foreach ($eventParticipant as $u){
            array_push($arrayParticipant, $u);
        }



        return $this->render('event/index.html.twig', [
            'event' => $event,
            'participants' => $arrayParticipant
        ]);
    }

    /**
     * @Route("/RegisterForEvent/{id}", name="register_for_revent")
     */
    public function registerForEvent(Event $e, EntityManagerInterface $em, StatusRepository $sr): Response
    {
        $user = $this->getUser();
        $partincipantsTab = $e->getParticipants();


        if ($e->getStatus()->getDescription() == "Open"
            and $partincipantsTab->contains($user) == false
            and count($e->getParticipants()) < $e->getMaxParticipants())
        {

            $e->addParticipant($user);

            if(count($e->getParticipants()) == $e->getMaxParticipants()){
                $statusClosed = $sr->findBy(array('description' => "Closed"));
                $e->setStatus($statusClosed[0]);
            }

            $em->flush();
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/unregisterForEvent/{id}", name="unregister_for_revent")
     */
    public function unregisterForEvent(Event $e, EntityManagerInterface $em, StatusRepository $sr): Response
    {
        $user = $this->getUser();
        $partincipantsTab = $e->getParticipants();

        if ($e->getStatus()->getDescription() == "Open"
            or $e->getStatus()->getDescription() == "Closed"
            and $partincipantsTab->contains($user) == true)
        {

            if(count($e->getParticipants()) == $e->getMaxParticipants()){
                $statusOpen = $sr->findBy(array('description' => "Open"));
                $e->setStatus($statusOpen[0]);
            }

            $e->removeParticipant($user);

            $em->flush();
        }

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/editEvent/{id}", name="editEvent")
     */
    public function editEvent(Event $event, Request $req, EntityManagerInterface $em): Response
    {
        // $now = new \DateTime();
        // $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1

        $form = $this->createForm(AddEventType::class,$event);
        $form->handleRequest($req);

        if ($form->isSubmitted()){
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('event/add.html.twig',
            ['addEventForm'=> $form-> createView()]);

    }


    /**
     * @Route("/cancelEvent/{id}", name="cancelEvent")
     */
    public function cancelEvent(Event $event, Request $req, EntityManagerInterface $em, StatusRepository $statusRepository ): Response
    {
        $statusCancelled = $statusRepository->findBy(array('description' => "Cancelled"));

        $form = $this->createForm(CancelEventType::class,$event);
        $form->handleRequest($req);

        if ($form->isSubmitted()){
            $event->setStatus($statusCancelled[0]);
            $event->setEventInfo($event->getEventInfo().'   Annulée : '.$form['eventInfo']->getData());
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('event/cancel.html.twig', [
                'event'=>$event,
                'addEventForm'=> $form-> createView()]);

    }

    /**
     * @Route("/publishEvent/{id}", name="publishEvent")
     */
    public function publishEvent(Event $event, Request $req, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        $statusOpen = $statusRepository->findBy(array('description' => "Open"));

        $event->setStatus($statusOpen[0]);
            $em->flush();
            return $this->redirectToRoute('home');
        }


}
