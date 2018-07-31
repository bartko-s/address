<?php

declare(strict_types=1);

namespace App\UI\Front\Dashboard\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function execute()
    {
        return $this->render('@Front/dashboard/home.twig');
    }
}
