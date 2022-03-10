<?php

namespace App\Controller;

use App\Entity\Participant;
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
        $participant = new Participant();
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
        ]);
    }





    /*
        public function findBy(Request $req, EventRepository $repo, EntityManagerInterface  $em): Response
        {
            $events = $repo->findAll();

            // $em->getFilters()
             //   ->enable('searchFilter');

            $form = $this->createForm(SearchEventType::class);
            $form->handleRequest($req);

            if ($form->isSubmitted() && $form->isValid()) {
                $eventModel = $form->getData();

                $event = new Event();
                $event->setName();
                //($eventModel->name);
                $event->setDateTimeStart();
                $event->setRegistrationClosingDate();
                $event->setMaxParticipants();
                $event->setStatus();
                $event->setOrganizer();

               // $events = $repo->findAllQueryBuilder($eventModel);
            }

            return $this->render('/filtres.html.twig', [
                'events' => $events,
                'form' => $form->createView(),
            ]);
        }*/


}
