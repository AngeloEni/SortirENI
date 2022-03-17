<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{

///**
// * @Route("/event", name="api_addEvent", methods={"POST"})
// */
//public function add(Request $req, EntityManagerInterface $em): Response
//{
//
//     $e = new Event();
//     $event = json_decode($req->getContent());
//     $e->setNom($event->nom);
//     $e->setDateTimeStart($event->dateTimeStart);
//     $e->setRegistrationClosingDate($event->registrationClosingDate);
//     $e->setMaxParticipants($event->dateTimeStart);
//     $e->setDuration($event->duration);
//     $e->setEventInfo($event->eventInfo);
//     $e->setCampus($event->duration);
//     $e->setEventInfo($event->eventInfo);
//     $em->persist($c);
//     $em->flush();
//     return $this->json($c);
//
//
//    $tab["message"] = "Add...";
//    return $this-> json($tab);
}
