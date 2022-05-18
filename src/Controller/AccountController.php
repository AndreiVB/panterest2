<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/account") 
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function show(): Response
    {

        return $this->render('account/show.html.twig');
    }
    /**
     * @Route("/edit", name="app_account_edit", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
   
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('succes', 'Account updated with succes');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/change-password", name="app_account_change_password", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder): Response
    {
        
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->hashPassword($user, $form['plainPassword']->getData()));
            
            $em->flush();
            $this->addFlash('succes', 'Password updated with succes'); 
            return $this->redirectToRoute('app_account');
        }
       
        return $this->render('account/changePassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}