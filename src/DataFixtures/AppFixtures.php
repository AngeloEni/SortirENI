<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Status;
use App\Entity\Town;
use App\Entity\Venue;
use App\Repository\CampusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private Generator $generator;
    private ObjectManager $manager;
    private UserPasswordHasherInterface $hasher;


    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->generator = Factory::create('fr_FR');
        $this->hasher = $passwordHasher;

    }


    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        //d:f:l
    $this->manager = $manager;
       $this->generateStatus();
       $this->generateCampus();
       $this->generateTown();
       $this->generateVenue();
       $this->generateParticipant();
       $this->generateEvent();


        $this->manager->flush();
    }

    public function generateParticipant(){

        $campus = $this->manager->getRepository(Campus::class)->findAll();

        for ($i = 0; $i < 10; $i++){


            $participant = new Participant();
            $participant->setLastname($this->generator->lastName);
            $participant->setPseudo($this->generator->company);
            $participant->setFirstname($this->generator->firstName);
            //$participant->setTel($this->generator->phoneNumber); //problème sur la taille des numéros à l'insertion
            $participant->setEmail($this->generator->email);
            $participant->setPassword($this->hasher->hashPassword($participant, '123456'));
            $participant->setActive(true);
            $participant->setCampus($this->generator->randomElement($campus));
            $participant->setRoles(["ROLE_USER"]);
            $this->manager->persist($participant);
        }
        $this->manager->flush();
    }

    public function generateCampus(){

        $campusArray = Array("Rennes", "Nantes", "Niort", "Quimper");

        foreach ($campusArray as $c){
            $campus = new Campus();
            $campus->setName($c);
            $this->manager->persist($campus);

        }
        $this->manager->flush();

    }

    public function generateStatus(){

        $statusArray = Array("Created", "Open", "Closed", "Ongoing", "Ended", "Cancelled");

        foreach ($statusArray as $s){
            $status = new Status();
            $status->setDescription($s);
            $this->manager->persist($status);

        }
        $this->manager->flush();

    }

    public function generateTown(){

        for ($i = 0; $i < 100; $i++){


            $town = new Town();
            $town->setName($this->generator->city);
            $town->setPostCode(35000);

            $this->manager->persist($town);
        }
        $this->manager->flush();
    }
    public function generateVenue(){

        $towns = $this->manager->getRepository(Town::class)->findAll();
        $placeNames = Array("Bar", "Plage", "Forêt", "Cinéma", "Centre Sportif", "Port", "Bibliothèque");

        for ($i = 0; $i < 100; $i++){


            $venue = new Venue();
            $venue->setName($this->generator->randomElement($placeNames));
            $venue->setStreet($this->generator->streetAddress);
            $venue->setTown($this->generator->randomElement($towns));


            $this->manager->persist($venue);
        }
        $this->manager->flush();
    }

    public function generateEvent(){

        $campus = $this->manager->getRepository(Campus::class)->findAll();
        $status = $this->manager->getRepository(Status::class)->findAll();
        $venues = $this->manager->getRepository(Venue::class)->findAll();
        $participants = $this->manager->getRepository(Participant::class)->findAll();



        for ($i = 0; $i < 10; $i++){


            $event = new Event();
            $event->setName($this->generator->colorName);
            $event->setDateTimeStart($this->generator->dateTimeBetween('-1 year', '+1 year', 'Europe/Amsterdam'));
            $event->setDuration(60);
            $date = clone $event->getDateTimeStart();
            $date->modify("-1 week");
            $event->setRegistrationClosingDate($date);
            $event->setMaxParticipants(20);
            $event->setEventInfo("Bonjour");
            $event->setCampus($this->generator->randomElement($campus));
            $event->setStatus($this->generator->randomElement($status));
            $event->setVenue($this->generator->randomElement($venues));
            $event->setOrganizer($this->generator->randomElement($participants));
            $event->addParticipant($this->generator->randomElement($participants));
            $event->addParticipant($event->getOrganizer());



            $this->manager->persist($event);
        }
        $this->manager->flush();
    }



}
