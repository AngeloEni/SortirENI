<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event/{id}", name="app_event")
     */
    public function index(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        $d = $event->getParticipants();
        dd($d);
        return $this->render('event/index.html.twig', [
            'event' => $event
        ]);
    }
}
