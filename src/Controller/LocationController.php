<?php
namespace App\Controller;

use App\Entity\Location;
use App\Entity\Images;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Security;
use MercurySeries\FlashyBundle\FlashyNotifier;




use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
     * @Route("/location",name="location_list")
     * @Method({"GET"})
     */
    public function index()
    {
        //return new Response('<html><body>Hello</body></html>');
        
        $locations = $this->getDoctrine()->getRepository(Location::class)->findAll();
        return $this->render('Locations/index.html.twig', array('locations' => $locations));

    }
    /**
     * @Route("/location/notif",name="location_notif")
     * @Method({"GET"})
     */
    public function notify(FlashyNotifier $flashy)
    {
        //return new Response('<html><body>Hello</body></html>');
        $flashy->success('Demande envoyée vers Le proprietaire de cette location');
        return $this->redirectToRoute('location_list');
        

    }
    #[Route("/location/delete/{id}")]

function delete(Request $request, $id)
    {
    $location = $this->getDoctrine()->getRepository(Location::class)->find($id);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($location);
    $entityManager->flush();
    $response = new Response();
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
function new (Request $request) {
    $location = new Location();
    $form = $this->createFormBuilder($location)
        ->add('titre', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('dispo_date', DateType::class, array('attr' => array('class' => 'form-control')))
        ->add('capacity', IntegerType::class, array('attr' => array('class' => 'form-control')))
        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control')))
        ->add('images', FileType::class,[
            'label' => false,
            'multiple' => true,
            'mapped' => false,
            'required' => false
        ])
        ->add('save', SubmitType::class, array('label' => 'Ajouter', 'attr' => array('class' => 'btn btn-primary mt-3')))
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $images = $form->get('images')->getData();
    
    // On boucle sur les images
    foreach($images as $image){
        // On génère un nouveau nom de fichier
        $fichier = md5(uniqid()).'.'.$image->guessExtension();
        
        // On copie le fichier dans le dossier uploads
        $image->move(
            $this->getParameter('images_directory'),
            $fichier
        );
        
        // On crée l'image dans la base de données
        $img = new Images();
        $img->setName($fichier);
        $location->addImage($img);
    }

        $location = $form->getData();
        $user = $this->security->getUser();
        $location->setOwner($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($location);
        $entityManager->flush();
        return $this->redirectToRoute('location_list');
    }
    return $this->render("Locations/new.html.twig", array('form' => $form->createView()));

}

/**
 * @Route("/location/edit/{id}", name="edit_location")
 * Method({"GET","POST"})
 */
function edit(Request $request, $id)
    {
    $location = new Location();
    $location = $this->getDoctrine()->getRepository(Location::class)->find($id);
    $form = $this->createFormBuilder($location)
        ->add('titre', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control')))
        ->add('save', SubmitType::class, array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-primary mt-3')))
        ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->flush();
        return $this->redirectToRoute('location_list');
    }
    return $this->render("Locations/edit.html.twig", array('form' => $form->createView()));

}
/**
 * @Route("/location/{id}", name="location_show")
 */
function show($id)
    {
    $location = $this->getDoctrine()->getRepository(Location::class)->find($id);

    return $this->render("Locations/show.html.twig", array('location' => $location));
}
/**
 * @Route("/supprime/image/{id}", name="locations_delete_image", methods={"DELETE"})
 */
public function deleteImage(Images $image, Request $request){
    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
        // On récupère le nom de l'image
        $nom = $image->getName();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$nom);

        // On supprime l'entrée de la base
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}

}
