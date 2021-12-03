<?php
namespace App\Controller;

use App\Entity\Location;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LocationController extends AbstractController{
    /**
     * @Route("/location",name="location_list")
     * @Method({"GET"})
     */
    public function index(){
        //return new Response('<html><body>Hello</body></html>');
        $locations=$this->getDoctrine()->getRepository(Location::class)->findAll();
        return $this->render('Locations/index.html.twig',array('locations'=>$locations));

    }
    #[Route("/location/delete/{id}")]
      
     
    public function delete(Request $request,$id){
        $location=$this->getDoctrine()->getRepository(Location::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
            $response= new Response();
            $response->send();
    }
    
    // /**
    //  * @Route("/location/save")
    //  */
    // public function save(){
    //     $entityManager=$this->getDoctrine()->getManager();
    //     $location=new Location();
    //     $location->setTitre("Location two");
    //     $location->setDescription("ici la description de location two");
    //     $entityManager->persist($location);

    //     $entityManager->flush();
    //     return new Response("saved location with id of".$location->getId());
    // }
    /**
     * @Route("/location/new", name="new_location")
     * Method({"GET","POST"})
     */
    public function new(Request $request){
        $location=new Location();
        $form=$this->createFormBuilder($location)
        ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control')))
        ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control')))
        ->add('save',SubmitType::class,array('label'=>'Ajouter','attr'=>array('class'=>'btn btn-primary mt-3')))
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            
            $location=$form->getData();
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();
            return $this->redirectToRoute('location_list');
        }
        return $this->render("Locations/new.html.twig",array('form'=>$form->createView()));

    }



    /**
     * @Route("/location/edit/{id}", name="edit_location")
     * Method({"GET","POST"})
     */
    public function edit(Request $request,$id){
        $location=new Location();
        $location=$this->getDoctrine()->getRepository(Location::class)->find($id);
        $form=$this->createFormBuilder($location)
        ->add('titre',TextType::class,array('attr'=>array('class'=>'form-control')))
        ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control')))
        ->add('save',SubmitType::class,array('label'=>'Enregistrer','attr'=>array('class'=>'btn btn-primary mt-3')))
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            
            
            $entityManager=$this->getDoctrine()->getManager();
            
            $entityManager->flush();
            return $this->redirectToRoute('location_list');
        }
        return $this->render("Locations/edit.html.twig",array('form'=>$form->createView()));

    }
    /**
     * @Route("/location/{id}", name="location_show")
     */
    public function show($id){
        $location=$this->getDoctrine()->getRepository(Location::class)->find($id);

        return $this->render("Locations/show.html.twig",array('location'=>$location));
    }
    
}