<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form;
use App\Repository\ArticleRepository;

class BlogController extends AbstractController
{
  /**
  * @Route("/blog", name="blog")
  */
  public function index(ArticleRepository $repo)
  {
    $articles = $repo->findAll();

    return $this->render('blog/index.html.twig', [
      'controller_name' => 'BlogController',
      'articles' => $articles
    ]);
  }

  /**
  * @Route("/", name="home")
  */
  public function home()
  {
    return $this->render('blog/home.html.twig', [
      'title' => "Bienvenue dans mon blog",
      'age' => 31
    ]);
  }

  /**
  *  @Route("/blog/new", name="blog_create")
  *  @Route("/blog/{id}/edit", name="blog_edit")
  */
  public function form (Article $article = null, Request $request, ObjectManager $manager) {
    if(!$article) {
      $article = new Article();
    }


    /**pré remplissage formulaire
    $article->setTitle("Titre d'exemple")
    ->setContent("Le contenu de l'article");
    */

    $form = $this->createFormBuilder($article)
    ->add('title')
    ->add('content')
    ->add('image')
    -> getForm();
    //manipule les données
    $form ->handleRequest($request);
    //Soumission form
    if($form->isSubmitted()&& $form->isValid()){
      //si l'article n'a pas d'id donc est un nouvel article, création de la date
      if(!$article->getId()){
        //rajout de la date de création qui n'était pas dans le form
        $article->setCreatedAt(new \DateTime());
      }

      $manager->persist($article);

      $manager->flush();
      // redirection sur la page blog_show
      return $this->redirectToRoute('blog_show', ['id'=> $article->getId()
    ]);
  }

//editmode le bouton va changer dans le formulaire suivant si edition ou création
  return $this->render('blog/create.html.twig', [
    'formArticle' => $form->createView(),
    'editMode' => $article->getId() !== null
  ]);
}

/**
* @Route("/blog/{id}", name="blog_show")
*/
public function show(Article $article){

  return $this->render('blog/show.html.twig', [
    'article' => $article
  ]);
}

}
