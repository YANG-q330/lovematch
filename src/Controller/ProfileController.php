<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\PictureType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/create", name="profile_create")
     */
    public function create(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $user->setRoles(['ROLE_USER']) ;
            //todo Censurer les gros mots
            $entityManager->persist($profile);
            $entityManager->flush();

        $this->addFlash("success", "You have filled up your profile successfully!");
        return $this->redirectToRoute("main_home");
        }
    return $this->render('profile/create.html.twig', [
        "profileForm"=>$form->createView()
    ]);
    }

    /**
     * @Route ("/profile/create/pic", name="profile_pic")
     */
    public function pic(){
        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($picture);

        if ($formPicture->isSubmitted() && $formPicture->isValid()){
            /**@var UploadedFile $uploadedFile*/
            $uploadedFile = $formPicture->get('pic')->getData();

            $newFileName = ByteString::fromRandom(30).".".$uploadedFile->guessExtension();
            try {
                $uploadedFile->move($this->getParameter('upload_dir', $newFileName));
            } catch (\Exception $e){
                $e->getMessage();
            }





        }

        return $this->render('profile/pic.html.twig', [
            "picForm"=>$formPicture->createView()
        ]);
    }
}
