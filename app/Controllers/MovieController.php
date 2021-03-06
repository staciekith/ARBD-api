<?php

namespace CinemaHD\Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CinemaHD\Utils\Silex\ValidatorUtils;

use CinemaHD\Entities\Movie;

class MovieController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->get('/movies', [$this, 'getMovies']);

        $controllers->post("/movies", [$this, 'createMovie']);

        $controllers->put('/movies/{movie}', [$this, 'updateMovie'])
            ->assert("movie", "\d+")
            ->convert("movie", $app["findOneOr404"]('Movie', 'id'));

        $controllers->get('/movies/{movie}', [$this, 'getMovie'])
            ->assert("movie", "\d+")
            ->convert("movie", $app["findOneOr404"]('Movie', 'id'));

        $controllers->get('/movies/{movie}/showings', [$this, 'getShowingsForMovie'])
            ->assert("movie", "\d+")
            ->convert("movie", $app["findOneOr404"]('Movie', 'id'));

        $controllers->get('/movies/{movie}/peoples', [$this, 'getPeoplesForMovie'])
            ->assert("movie", "\d+")
            ->convert("movie", $app["findOneOr404"]('Movie', 'id'));

        $controllers->get('/movies/{movie}/types', [$this, 'getTypesForMovie'])
            ->assert("movie", "\d+")
            ->convert("movie", $app["findOneOr404"]('Movie', 'id'));

        return $controllers;
    }

    /**
     * Récupère tous les movies
     *
     * @param  Application   $app     Silex application
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMovies(Application $app)
    {
        $movies = $app["repositories"]("Movie")->findAll();

        return $app->json($movies, 200);
    }

    /**
     * Récupère un movie via son ID
     *
     * @param  Application   $app     Silex application
     * @param  Movie         $movie   L'entité du movie
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMovie(Application $app, Movie $movie)
    {
        return $app->json($movie, 200);
    }

    /**
     * Créé un movie
     *
     * @param  Application   $app         Silex application
     * @param  Request       $req         Requête
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createMovie(Application $app, Request $req)
    {
        $datas = $req->request->all();

        $errors = ValidatorUtils::validateEntity($app, Movie::getConstraints(), $datas);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $movie = new Movie();
        $movie->setProperties($datas);

        $app["orm.em"]->persist($movie);
        $app["orm.em"]->flush();

        return $app->json($movie, 201);
    }

    /**
     * Update un movie
     *
     * @param  Application   $app         Silex application
     * @param  Request       $req         Requête
     * @param  Movie         $movie       L'entité movie
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateMovie(Application $app, Request $req, Movie $movie)
    {
        $datas = $req->request->all();

        $errors = ValidatorUtils::validateEntity($app, Movie::getConstraints(), $datas);
        if (count($errors) > 0) {
            return $app->json($errors, 400);
        }

        $movie->setProperties($datas);

        $app["orm.em"]->persist($movie);
        $app["orm.em"]->flush();

        return $app->json($movie, 200);
    }

    /**
     * Récupère les showings d'un movie
     *
     * @param  Application   $app     Silex application
     * @param  Movie         $movie   L'entité du movie
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getShowingsForMovie(Application $app, Movie $movie)
    {
        $showings = $app["repositories"]("Showing")->findByMovie($movie);

        return $app->json($showings, 200);
    }

    /**
     * Récupère les people d'un movie
     *
     * @param  Application   $app     Silex application
     * @param  Movie         $movie   L'entité du movie
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getPeoplesForMovie(Application $app, Movie $movie)
    {
        $movie_has_people = $app["repositories"]("MovieHasPeople")->findByMovie($movie);
        $peoples = array_map(
            function ($mhp) {
                return [
                    "people"       => $mhp->getPeople(),
                    "role"         => $mhp->getRole(),
                    "significance" => $mhp->getSignificance()
                ];
            },
            $movie_has_people
        );

        return $app->json($peoples, 200);
    }

    /**
     * Récupère les types d'un movie
     *
     * @param  Application   $app     Silex application
     * @param  Movie         $movie   L'entité du movie
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTypesForMovie(Application $app, Movie $movie)
    {
        $movie_has_type = $app["repositories"]("MovieHasType")->findByMovie($movie);
        $types = array_map(
            function ($mht) {
                return $mht->getType();
            },
            $movie_has_type
        );

        return $app->json($types, 200);
    }
}
