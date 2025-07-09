<?php

namespace App\Controllers;

use Twig\Environment;

class HomeController
{
    public function __construct(private Environment $twig)
    {
    }

    public function index(): void
    {
        echo $this->twig->render('home.html.twig');
    }
}