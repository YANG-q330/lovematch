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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profile = new Profile();

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $this->getUser()->setRoles(['ROLE_USER']);
            //todo Censurer les gros mots
            $entityManager->persist($profile);
            $entityManager->flush();

        $this->addFlash("success", "You have filled up your profile successfully!");
        return $this->redirectToRoute("profile_pic");
        }
    return $this->render('profile/create.html.twig', [
        "profileForm"=>$form->createView()
    ]);
    }


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

            $picture->setDateCreated(new \DateTime());
            $picture->setFileName($newFileName);

            /** @var User $user */
            $user = $this->getUser();
            $picture->setUser($user);

            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', 'Your beautifull !');
            return $this->redirectToRoute("main_home");
        }

        return $this->render('profile/pic.html.twig', [
            "picForm"=>$formPicture->createView()
        ]);
    }
}
