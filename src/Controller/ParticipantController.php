<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/internal")
 */

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="app_participant")
     */
    public function index(ParticipantRepository $pRepo, \Symfony\Component\HttpFoundation\Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {

        $participant = $pRepo->find(1);



        $yes = new Participant();


        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($req);

        if($form->isSubmitted()){
                $mdp = $form->get('password')->getData();

//            $hashedPassword = $hasher->hashPassword($participant, $mdp);
//            $participant->setPassword($hashedPassword);

            $participant->setPassword($mdp);
            $em->persist($participant);
            $em->flush();

        }

        return $this->render('participant/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
