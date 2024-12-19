<?php

declare(strict_types=1);

namespace App\UI\Front\Dashboard\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function execute()
    {
        return $this->render('@Front/dashboard/home.twig');
    }
}
