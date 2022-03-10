<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/internal")
 */

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="app_participant")
     */
    public function index(ParticipantRepository $pRepo, \Symfony\Component\HttpFoundation\Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, SluggerInterface $slugger): Response
    {

        //récupération de l'utilisateur en session
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

            $brochureFile = $form->get('brochure')->getData();


            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                //méthode pour supprimer l'ancienne image du participant
                $this->deleteImage($participant->getImage(),$em);

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('image'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $participant->setImage($newFilename);
            }

            $em->persist($participant);
            $em->flush();

        }

        return $this->render('participant/index.html.twig', [
            'form' => $form->createView(),
            'particpant' => $participant
        ]);
    }


    public function deleteImage($name)
    {
        /**
         * Je gère la suppression du dossier "uploads" ou l'image est stockée
         */
        //Je récupère le nom de l'image
        $filename = $name;

        // Je crée une instance de kla classe fileSystem
        $fileSystem = new Filesystem();
        //Je supprime l'image du dossier
        $projectDir = $this->getParameter('kernel.project_dir');
        $fileSystem->remove($projectDir.'/public/uploads/'.$filename);

        return new Response('deleted', Response::HTTP_OK);

    }
}
