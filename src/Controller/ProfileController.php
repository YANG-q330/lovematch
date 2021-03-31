<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\PictureType;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use claviska\SimpleImage;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\String\ByteString;

class ProfileController extends AbstractController
{



    /**
     * @Route ("/profile/create/pic", name="profile_pic")
     */
    public function pic(EntityManagerInterface $entityManager, Request $request){
        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()){
            /**@var UploadedFile $uploadedFile*/
            $uploadedFile = $formPicture->get('pic')->getData();

            $newFileName = ByteString::fromRandom(30).".".$uploadedFile->guessExtension();
            try {
                //upload_dir est configuré dans services.yaml
                $uploadedFile->move($this->getParameter('upload_dir'), $newFileName);
            } catch (\Exception $e){
                dd($e->getMessage());
            }

            $simpleImage = new SimpleImage();
            $simpleImage->fromFile($this->getParameter('upload_dir')."/$newFileName")
                        ->bestFit(300, 300)
                        ->toFile($this->getParameter('upload_dir')."/small/$newFileName");

            $picture->setDateCreated(new \DateTime());
            $picture->setFileName($newFileName);

            /** @var User $user */
            $user = $this->getUser();
            $picture->setUser($user);


            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', 'You are beautiful !');
            return $this->redirectToRoute("main_home");
        }

        return $this->render('profile/pic.html.twig', [
            "picForm"=>$formPicture->createView()
        ]);
    }



    /**
     * @IsGranted ("ROLE_USER_NOT_PROFILED")
     * @Route("/profile/create", name="profile_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $profile = new Profile();

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            /**
             * @var User $user
             */
            $user=$this->getUser();
            $profile->setUser($user);
            $user->setRoles(['ROLE_USER']);
            //todo Censurer les gros mots
            $entityManager->persist($profile);
            $entityManager->flush();
            //reconnecter l'utilisateur
            //on recrée une token pour l'utilisateur, sinon il est déconnecté !
            $token = new PostAuthenticationGuardToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($token);
            $this->addFlash("success", "You have filled up your profile successfully!");
            return $this->redirectToRoute("profile_pic");
        }
        return $this->render('profile/create.html.twig', [
            "profileForm"=>$form->createView()
        ]);
    }



    /**
     * @IsGranted("ROLE_USER")
     * @Route ("/profile/{id}", name="profile_detail", requirements={"id": "\d+"})
     */
    public function detail(Request $request, ProfileRepository $profileRepository, EntityManagerInterface $entityManager): Response{
        /** @var User $user */
        $user = $this->getUser();
        $profile = $profileRepository->find($user->getProfile()->getId());
        if (!$profile){
            throw $this->createNotFoundException('This profile don\'t exist! ');
        }
        return $this->render('profile/detail.html.twig', ["profile"=>$profile]);
    }


}
