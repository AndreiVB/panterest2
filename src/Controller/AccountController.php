<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Container1p1KlHO\getSecurity_Command_UserPasswordEncoderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }
    /**
     * @Route("/edit", name="app_account_edit", methods={"GET", "POST"})
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