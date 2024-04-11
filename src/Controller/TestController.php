<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function test(Request $request): Response
    {
        $imageTitle = "endgame.jpg";
        $rootDir = $this->getParameter('kernel.project_dir');
        $file = readfile($rootDir.'\public\images\endgame.jpg');

        $headers = array(
            'Content-Type'     => 'image/png',
            'Content-Disposition' => 'inline; filename="'.$imageTitle.'"');
        return new Response($file, 200, $headers);
    }
}