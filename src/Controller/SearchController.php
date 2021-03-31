<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\SearchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     *
     * @Route("/search/new", name="search_newSearch")
     */
    public function newSearch(Request $request, EntityManagerInterface $entityManager):Response{

        /**@var User $user */
        $user = $this->getUser();

        if ($user->getSearch()){
            $search = $user->getSearch();
        }
        else{
            $search = new Search();
            $search->setUser($user);
        }

        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $entityManager->persist($search);
            $entityManager->flush();
            return $this->redirectToRoute("search_match");
        }

        return $this->render('search/newSearch.html.twig', ["searchForm"=>$form->createView()]
        );

    }

    /**
     * @Route("/search", name="search_match")
     */
    public function match(UserRepository $repository):Response{
        /**@var User $user */
        $user = $this->getUser();

        $results = $repository->findAllMatches($user->getSearch());
        return $this->render('search/match.html.twig', ['matchesUser' => $results]);
    }


}
