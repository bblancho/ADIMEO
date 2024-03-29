<?php

namespace App\Service;

use DateTime;
use App\Entity\Nasa;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class NasaService
{
    private $entityManager;
    private $client;
    private $api_Key_nasa;

    public function __construct(string $api_Key_nasa, EntityManagerInterface $entityManager, HttpClientInterface $client){
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->api_Key_nasa= $api_Key_nasa ;
    }
    
    // tâche crône ['app:cron:get-image-nasa-current-day']
    public function getImageNasaCurrentDay(): Nasa
    {
        $api_Key_nasa = $this->api_Key_nasa;

        $data = $this->client->request(
            'GET', 
            "https://api.nasa.gov/planetary/apod?api_key=$api_Key_nasa",
        )->toArray() ;

        $date = new  \DateTime($data['date']) ;
        $date->format('Y-m-d');

        // verifier si $data['url'] contient une vidéo youtube https://www.youtube.com/embed/x-wX-wClfig?rel=0

        $nasa = ( new Nasa() )
            ->setTitle( $data['title'] )
            ->setDescription( $data['explanation'] )
            ->setDateTime( $date )
            ->setImage( $data['url'] )
        ;

        $this->entityManager->persist($nasa) ;
        $this->entityManager->flush() ;

        return $nasa ;
    }


   

}
