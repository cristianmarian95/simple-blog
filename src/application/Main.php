<?php

namespace App;

class Main
{
    /**
     * @var
     */
    private $container;

    /**
     * Main constructor.
     * @param $container
     */
    public function __construct($container)
    {
        //Set container
        $this->container = $container;
    }

    /**
     * @param $property
     * @return null
     */
    public function __get($property)
    {
        //Check if the property is set
        if (isset($this->container->{$property})){
            //Return property
            return $this->container->{$property};
        }
        return null;
    }

    /**
     * @param string $routeName
     * @return mixed
     */
    public function redirect($routeName = '')
    {
        return $this->response->withRedirect($this->router->pathFor($routeName));
    }
}