<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{

    //if lot of $em use(optional), istead of injecting in every function
    /* private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em; 
    } */
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        // return $this->render('pins/index.html.twig', ['pins' => $pins]);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods="GET|POST")
     */
    // methods={"GET", "POST"} same as line 27
    public function create(Request $request, EntityManagerInterface $em): Response {
        $pin = new Pin; 
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
        // dd($form->getData());
        // if data-class is set in PinType.php(not null) ->returns object ?? returns array   
        // is recommended to use dependance injection in function arguments eg:line 35
        // $em = $this->getDoctrine()->getManager(); 
        $em->persist($pin);
        $em->flush();

        $this->addFlash('succes', 'Pin  succesfully added');

        return $this->redirectToRoute('app_home');

        }
        return $this->render('pins/create.html.twig', [
            //bagat de mine urm linie
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    
    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
       return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "POST"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(PinType::class, $pin);
        
        //aici baga el al 3-lea param in create form method put, dar nu merge; TODO sa fac cu form in edit show
    
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
        $em->flush();

         $this->addFlash('succes', 'Pin succesfully edited ');

        return $this->redirectToRoute('app_home');
            
    }
      return $this->render('pins/edit.html.twig', [
          'pin' => $pin,
          'form' => $form->createView()
      ]);
}

    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods={"POST"})
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response {
          
        if($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->request->get('csrf_token') )) { 
            $em->remove($pin);
            $em->flush();

             $this->addFlash('info', 'Pin succesfully deleted');
        }

        return $this->redirectToRoute('app_home');
    }
}