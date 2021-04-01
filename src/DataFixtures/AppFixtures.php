<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\Profile;
use App\Entity\Search;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager ){
        $faker = \Faker\Factory::create("fr_FR");
        $profileList=[];
        for ($i=0;$i<200;$i++){
            $profile = new Profile();
            $profile->setBirthday($faker->dateTimeBetween('-50 years'));
            $profile->setCity($faker->city);
            $profile->setPostalCode($faker->postcode);
            $profile->setSex($faker->randomElement(["Man","Woman"]));
            $profile->setPresentation($faker->realText());
            $manager->persist($profile);
            $profileList [] = $profile;
        }
        $manager->flush();

        $photosList=[];
        for ($i=0;$i<200;$i++){
            $photo = new Picture();
            $photo->setDateCreated($faker->dateTimeBetween('-5 years'));
            $photo->setFileName($faker->randomElement(["6NAwoww5yAYY63xjnn2wdkt6zNPr4p.jpg","bAfuNxKDJ3vRxvMVA3khfSFC87xMLe.jpg","J56rPNh9bo2qR1k2k2WL7CZjRZGWDn.jpg",
                "Joh5Z86LUBtn7uwm8TLgQ5d7iMJBeM.jpg","LHHToss2auRunCCj4JvPqhNPMh5cko.jpg","MZkDrnzAEgtsgNuFeBRyRy93Gfdeg2.jpg","qHiAFf6jveLz4szoN2srLTbHbu3ssQ.jpg",
                "T4moG5ybB1jGH2vw6Kc2fDzaRUQARj.jpg","VeYfBqj8LWLfPhRCRvv36aegPWQFZf.jpg","vhoyrmMgWkVaAwmRaw6psGa8VitsMx.jpg"]));
            $manager->persist($photo);
            $photosList [] = $photo;
        }
        $manager->flush();

        $searchList =[];
        for ($i=0;$i<50;$i++){
            $search = new Search();
            $search->setSex($faker->randomElement(["Man","Woman"]));
            $search->setDepartment($faker->randomElement(["44000","35000","29000","98000","11000"]));
            $search->setAgeMax($faker->numberBetween(40,60));
            $search->setAgeMin($faker->numberBetween(20,39));
            $manager->persist($search);
            $searchList[]=$search;
        }
        $manager->flush();

        for ($i = 0; $i<200; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setDateCreated($faker->dateTimeBetween('-2 years'));
            $user->setPassword($faker->password(6, 20));
            $user->setProfile($faker->randomElement($profileList));
            $user->setFirstPicture($faker->randomElement($photosList));
            $user->setRoles($faker->randomElements(["ROLE_USER", "ROLE_USER_NON_PROFILED"]));
            $user->setSearch($faker->randomElement($searchList));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
