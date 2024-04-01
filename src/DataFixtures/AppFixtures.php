<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $userData = [
            [
                'email' => 'radu.tomescu@s.unibuc.ro',
                'password' => '$2a$13$4Y22CMS7vNk91nQe4b2QtOUHMjI8O0Pm7jNHCp0rcvg/y1J6H/kIe',
                'firstName' => 'Radu',
                'lastName' => 'Tomescu',
                'funds' => 10
            ],
            [
                'email' => 'ioan.ionescu@s.unibuc.ro',
                'password' => '$2a$13$4Y22CMS7vNk91nQe4b2QtOUHMjI8O0Pm7jNHCp0rcvg/y1J6H/kIe',
                'firstName' => 'Ioan',
                'lastName' => 'Ionescu',
                'funds' => 10
            ],
            [
                'email' => 'test.exemplu@s.unibuc.ro',
                'password' => '$2a$13$4Y22CMS7vNk91nQe4b2QtOUHMjI8O0Pm7jNHCp0rcvg/y1J6H/kIe',
                'firstName' => 'Test',
                'lastName' => 'Test',
                'funds' => 10
            ]
        ];

        $movieData = [
            [
                'title' => 'Titanic',
                'price' => 20
            ],
            [
                'title' => 'Whiplash',
                'price' => 30
            ],
            [
                'title' => 'Avengers:Endgame',
                'price' => 40
            ]
        ];

        foreach ($userData as $data)
        {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setFunds($data['funds']);
            $manager->persist($user);
        }

        foreach ($movieData as $data)
        {
            $movie = new Movie();
            $movie->setTitle($data['title']);
            $movie->setPrice($data['price']);
            $manager->persist($movie);
        }
        
        $manager->flush();
    }
}
