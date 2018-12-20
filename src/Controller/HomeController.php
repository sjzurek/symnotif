<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Used for testing service availability.
 *
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homeIndex")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to Simple REST API!',
            'status' => 'success',
        ]);
    }
}
