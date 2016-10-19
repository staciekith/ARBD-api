<?php

namespace CinemaHD\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CinemaHD\Entities\User;

class UserController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->get('/users', [$this, 'getUsers']);

        // $controllers->get('/users/{user}', [$this, 'getUser'])
        //     ->assert("user", "\d+")
        //     ->convert("user", $app["findOneOr404"]('User', 'id'));

        return $controllers;
    }

    public function getUsers(Application $app)
    {
        $users = $app["repositories"]("User")->findAll();

        return $app->json($users, 200);
    }
}
