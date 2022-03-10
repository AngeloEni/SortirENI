<?php

namespace App\Controller;

use App\Form\SearchEventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/internal", name="internal")
     */


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */

    public function showAll(Request $req, EventRepository $repo): Response
    {
        $events = $repo->findAll();
        $form = $this->createForm(SearchEventType::class);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterModel = $form->getData();

            $events = $repo->findByFilters( $eventFilterModel);


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
