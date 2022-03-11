<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\AddEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/eventEdit{id}", name="eventEdit")
     */
    public function edit(Event $event, Request $req, EntityManagerInterface $em): Response
    {
        // $now = new \DateTime();
        // $now->setTimezone(new \DateTimeZone('+0100')); //GMT+1

        $form = $this->createForm(AddEventType::class,$event);
        $form->handleRequest($req);

        if ($form->isSubmitted()){
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('/add.html.twig',
            ['form'=> $form-> createView()]);

    }

}
