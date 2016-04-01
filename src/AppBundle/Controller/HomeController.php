<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $articleRepository = $manager->getRepository('AppBundle:Article\Article');

        $articles = $articleRepository->findAll();

        return $this->render('AppBundle:Article:index.html.twig', [
            'articles' => $articles,
        ]);
    }
}