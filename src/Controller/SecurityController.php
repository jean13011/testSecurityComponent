<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/inscription", name="security_registration")
     * 
     * @param Request wich send the resquest from the user
     * @param EntityManagerInterface wich add you the commands for doctrine and persist all the infos from the request
     * @param UserPasswordEncoderInterface take your normal password and make him crypte
     * 
     * we bind the form created in the command with all the request sent by the new user
     * then if the form is submitted and if its valid we encode the password, we setting it , persist it and then finally we flush it
     * to be redirect into the login view
     */
    public function register(Request $req, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder )
    {
        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($req);
         if($form->isSubmitted() && $form->isValid())
         {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
         }

        return $this->render("security/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render("security/login.html.twig");
    }
}
