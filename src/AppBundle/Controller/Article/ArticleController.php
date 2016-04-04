<?php

namespace AppBundle\Controller\Article;

use AppBundle\Entity\Article\Tag;
use AppBundle\Form\Type\Article\ArticleType;
use AppBundle\Entity\Article\Article;
use AppBundle\Form\Type\Article\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller
{

    /**
     * @Route("/list", name="article_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articleRepository = $em->getRepository('AppBundle:Article\Article');

        $articles = $articleRepository->findAll();

        return $this->render('AppBundle:Article:index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/show/{id}",name="article_show", requirements={"id" = "\d+"})
     */
    public function showAction($id, Request $request)
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article\Article')->find( $id );

        if ( !$articles ) {
            throw $this -> createNotFoundException();
        }

        return $this -> render('AppBundle:Article:show.html.twig', [
                'articles' => $articles,
                ]);
    }

    /**
     * @Route("/show/{articleName}")
     *
     * @param $articleName
     *
     * @return Response
     */
    public function showArticleNameAction($articleName)
    {
        return $this->render('AppBundle:Article:index.html.twig', [
            'articleName' => $articleName,
        ]);
    }

    /**
     * @Route("/author", name="article_author")
     */
    public function authorAction(Request $request)
    {
        $author = $request->query->get('author');

        $em = $this->getDoctrine()->getManager();
        $articleRepository = $em->getRepository('AppBundle:Article\Article');

        $articles = $articleRepository->findBy([
            'author' => $author,
        ]);

        return $this->render('AppBundle:Article:index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/tag", name="article_tag")
     */
    public function tagAction(Request $request)
    {
        $tag = $request->query->get('tag');

        $em = $this->getDoctrine()->getManager();
        $articleRepository = $em->getRepository('AppBundle:Article\Article');

        $articles = $articleRepository->findBy([
            'tag' => $tag,
        ]);

        return $this->render('AppBundle:Article:index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/tag/new")
     */
    public function newTagAction(Request $request)
    {
        $form = $this->createForm(TagType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** @var Tag $tag */
            $tag = $form->getData();

            $stringUtil = $this->get('string.util');

            $slug = $stringUtil->slugify($tag->getName());
            $tag->setSlug($slug);

            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('AppBundle:Article:tag.new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/new", name="article_new")
     *
     * @param Request $request
     *
     * @return Request
     */
    public function createAction(Request $request)
    {
        $articles = new Article();

        $form = $this -> createFormBuilder( $articles )
            -> add ('title', TextType::class, array( 'label'=>'Titre') )
            -> add ('content', TextareaType::class, array ('label'=>'Contenu'))
            -> add ('tag', TextType::class, array ('label' => 'Tag de l\'article'))
            -> add ('save', SubmitType::class, array ('label' => 'Creer l\'article'))
            -> getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $articles = $form->getData();






            $title = $articles->getTitle();
            $articles->setTitle($title);

            $content = $articles->getContent();
            $articles->setContent($content);


            $stringUtil = $this->get('string.util');

            $tag = $stringUtil->slugify($articles->getTag());
            $articles->setTag($tag);


            /*$articles->setTitle();
            $articles->setContent();
            $articles->setTag();*/
            $articles->setCreatedAt( new \DateTime() );

            $em = $this->getDoctrine()->getManager();
            $em->persist($articles);
            $em->flush();

            return $this->redirectToRoute('article_list');
        }
        return $this->render('AppBundle:Article:create.html.twig', [
            'form' => $form->createView()]);




    }


}












