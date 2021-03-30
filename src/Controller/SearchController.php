<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search_newSearch")
     */
    public function newSearch(Request $request, EntityManagerInterface $entityManager):Response{
        $search = new Search();
        //$user = new User();
        //$search->setUser($user);
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $entityManager->persist($search);
            $entityManager->flush();
            return $this->redirectToRoute("main_home");
        }
        return $this->render('search/newSearch.html.twig', ["searchForm"=>$form->createView()]
        );

    }
}
