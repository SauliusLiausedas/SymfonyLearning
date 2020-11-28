<?php

namespace App\Service;

//use Symfony\Component\DependencyInjection\ContainerAwareInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;

class VeryBadDesign
{
    /**
     * @required
     */
    public function setContainer()
    {
        return false;
    }
}