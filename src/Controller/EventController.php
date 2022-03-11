<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
