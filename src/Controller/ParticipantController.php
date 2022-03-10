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

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="app_participant")
     */
    public function index(ParticipantRepository $pRepo, \Symfony\Component\HttpFoundation\Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        //mock test
        //$participant = $pRepo->find(1);


        $participant = $this->getUser();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($req);

        if($form->isSubmitted()){

                //controle si le mdp est changer
                if($form->get('password')->getData() != null){
                //récupération du mdp -> hashage -> set à l'objt participant
                    $mdp = $form->get('password')->getData();
                    $hashedPassword = $hasher->hashPassword($participant, $mdp);
                     $participant->setPassword($hashedPassword);
                }


            $em->persist($participant);
            $em->flush();

        }

        return $this->render('participant/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
