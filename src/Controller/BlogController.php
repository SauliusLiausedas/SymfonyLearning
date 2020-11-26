<?php

namespace App\Controller;

use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotations\Route;

class BlogController extends AbstractController
{
    /**
     * @var Greeting
     */
    private $greeting;

    public function __construct(Greeting $greeting)
    {
        $this->greeting = $greeting;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render('base.html.twig', [
            'message' => $this->greeting->greet($rqequest->get('name'))
        ]);
    }
}