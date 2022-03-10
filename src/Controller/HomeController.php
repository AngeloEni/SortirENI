<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Form\AddEventType;
use App\Form\SearchEventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
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
    public function showAll(Request $req, EventRepository $eventRepo): Response
    {
        $events = $eventRepo->findAll();
        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($req);
        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterModel = $form->getData();

            $events = $eventRepo->findByFilters( $eventFilterModel, $user);

        }


        return $this->render('/home.html.twig', [
            'events' => $events,
            'form' => $form->createView(),
            'user'=>$user,
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

    }
        return $this->render('home.html.twig', [
            'addEventForm' => $form->createView(),
        ]);
    }



}
