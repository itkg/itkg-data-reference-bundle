<?php

namespace Dsin\RangeManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DsinRangeManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
