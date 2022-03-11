<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function registerForEvent(Event $e, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $partincipantsTab = $e->getParticipants();


        if ($e->getStatus()->getDescription() == "Open"
            and $partincipantsTab->contains($user) == false
            and count($e->getParticipants()) < $e->getMaxParticipants())
        {

            $e->addParticipant($user);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/unregisterForEvent/{id}", name="unregister_for_revent")
     */
    public function unregisterForEvent(Event $e, EntityManagerInterface $em): Response
    {
        if ($e->getStatus()->getDescription() == "Open")
        {
            $user = $this->getUser();

            $e->removeParticipant($user);
            $em->flush();
        }

        return $this->redirectToRoute('home');

    }



}
