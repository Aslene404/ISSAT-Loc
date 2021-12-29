<?php
namespace App\Controller;

use App\Entity\Location;
use App\Entity\Images;
use App\Entity\Demande;
use App\Entity\User;
use App\Entity\Message;
use App\Form\RegistrationFormType;

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
use Carbon\Carbon;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use \Mailjet\Resources;



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
     * @Route("/location/notif/{id}",name="location_notif")
     * @Method({"GET"})
     */
    public function notify(FlashyNotifier $flashy,$id,MailerInterface $mailer)
    {
        //return new Response('<html><body>Hello</body></html>');
        $demande = new Demande();
        $user = $this->security->getUser();
        $demande->setSender($user);
        $location = new Location();
        $location = $this->getDoctrine()->getRepository(Location::class)->find($id);
        $demande->setReceiver($location->getOwner());
        $demande->setLocationId($id);
        
        $demande->setDate(new \DateTime('now'));
        $demande->setLocation($location);
        $demande->setIsAccept(false);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($demande);
        $entityManager->flush();
        $mj = new \Mailjet\Client('d334e66ed2f7cdce808c166ec3a0facb','9bac980f8d57b26ebe3ea8a0fd71ec9c',true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => "aslenerazor@gmail.com",
          'Name' => "Issat Co-Location"
        ],
        'To' => [
          [
            'Email' => $demande->getReceiver()->getEmail(),
            'Name' => $demande->getReceiver()->getFirstname(),
          ]
        ],
        'Subject' => "Nouvelle Notification.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Vous avez une nouvelle notification sur notre platforme Issat Co-Location vous pouvez consulter vos notfications <a href='http://127.0.0.1:8000/location/notif_list/'>Ici</a>!</h3>",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response->success() && var_dump($response->getData());
        $flashy->success('Demande envoyée vers Le proprietaire de cette location');
        return $this->redirectToRoute('location_list');
        

    }
    /**
     * @Route("/location/notif_list",name="location_notif_list")
     * @Method({"GET"})
     */
    public function notif_list()
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $this->security->getUser();
        
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Demande p
            WHERE p.receiver =:user
            ORDER BY p.date DESC'
        )->setParameter('user', $user);

        // returns an array of Product objects
        $demandes= $query->getResult();
        return $this->render('Notifications/list.html.twig', array('demandes' => $demandes));
        

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
/**
     * @Route("/location/notif_list/delete/{id}",name="location_notif_list_delete")
     * @Method({"GET"})
     */

function delete_notif(Request $request, $id)
    {
    $demande = $this->getDoctrine()->getRepository(Demande::class)->find($id);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($demande);
    $entityManager->flush();
    $response = new Response();
    $response->send();
    return $this->redirectToRoute('location_notif_list');
}
/**
     * @Route("/location/notif_list/update/{id}",name="location_notif_list_update")
     * @Method({"GET"})
     */

    function update_notif(FlashyNotifier $flashy,Request $request, $id)
    {
        $old_r=new User();
        $old_s=new User();
        $demande = $this->getDoctrine()->getRepository(Demande::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $old_r=$demande->getReceiver();
        $old_s=$demande->getSender();
        $demande->setReceiver($old_s);
        $demande->setSender($old_r);
        $demande->setIsAccept(true);
        $demande->setDate(new \DateTime('now'));
        
        $location = new Location();
        $location = $this->getDoctrine()->getRepository(Location::class)->find($demande->getLocationId());
        
        if (count($location->getMembers())==$location->getCapacity())
        {
            $entityManager->refresh($location);
            $entityManager->refresh($demande);
            $flashy->error('Capacité maximale de la maison est depassée');
        }

        
else{
    $location->addMember($old_s);

    $entityManager->flush();
    $mj = new \Mailjet\Client('d334e66ed2f7cdce808c166ec3a0facb','9bac980f8d57b26ebe3ea8a0fd71ec9c',true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => "aslenerazor@gmail.com",
          'Name' => "Issat Co-Location"
        ],
        'To' => [
          [
            'Email' => $demande->getReceiver()->getEmail(),
            'Name' => $demande->getReceiver()->getFirstname(),
          ]
        ],
        'Subject' => "Nouvelle Notification.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Vous avez une nouvelle notification sur notre platforme Issat Co-Location vous pouvez consulter vos notifications <a href='http://127.0.0.1:8000/location/notif_list/'>Ici</a>!</h3>",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response->success() && var_dump($response->getData());
    
    
    $flashy->success('Utilisateur est maintenant locataire de cette maison');
}
    return $this->redirectToRoute('location_notif_list');
}
/**
     * @Route("/location/messages/{id}", name="messages")
     * @Method({"GET"})
     */
    public function message_list($id)
    {


        

        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user_c = $this->security->getUser();

        
        
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Message p
            WHERE p.receiver =:user or p.sender =:user
            ORDER BY p.date ASC'
        )->setParameter('user', $user);

        // returns an array of Product objects
        $messages= $query->getResult();
        return $this->render('messages/list.html.twig', array('messages' => $messages));
        

    }
    /**
     * @Route("/location/contacts",name="contacts")
     * @Method({"GET"})
     */
    public function contact_list()
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $this->security->getUser();
        
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\User p
            WHERE p.id <>:user '
        )->setParameter('user', $user->getId());

        // returns an array of Product objects
        $users= $query->getResult();
        return $this->render('users/list.html.twig', array('users' => $users));
        

    }
    /**
     * @Route("/location/messages/add/{id}",name="message_add")
     * @Method({"GET","POST"})
     */
    public function message_add(Request $request,FlashyNotifier $flashy,$id,MailerInterface $mailer)
    {   $message = new Message();
        $form = $this->createFormBuilder($message)
            ->add('content', TextareaType::class, array('attr' => array('placeholder' => 'Votre Message','class' => 'form-control')))
            
            ->add('save', SubmitType::class, array('label' => 'Envoyer', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
    
            $message = $form->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $user_c = $this->security->getUser();
            $message->setSender($user_c);
            $message->setReceiver($user);
            $message->setDate(new \DateTimeImmutable('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            $demande = new Demande();
            
            $demande->setSender($user_c);
            
            $demande->setReceiver($user);
            
            // $location = $this->getDoctrine()->getRepository(Location::class)->find('4');
            // $demande->setLocation($location);
            $demande->setDate(new \DateTime('now'));
            
            $demande->setIsAccept(true);    
            $demande->setAck(69);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demande);
            $entityManager->flush();
            $mj = new \Mailjet\Client('d334e66ed2f7cdce808c166ec3a0facb','9bac980f8d57b26ebe3ea8a0fd71ec9c',true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => "aslenerazor@gmail.com",
          'Name' => "Issat Co-Location"
        ],
        'To' => [
          [
            'Email' => $message->getReceiver()->getEmail(),
            'Name' => $message->getReceiver()->getFirstname(),
          ]
        ],
        'Subject' => "Nouveau Message.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Vous avez une nouvelle notification sur notre platforme Issat Co-Location vous pouvez consulter vos messages <a href='http://127.0.0.1:8000/location/contacts/'>Ici</a>!</h3>",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response->success() && var_dump($response->getData());
            $flashy->success('Message envoyé avec success');
            return $this->redirectToRoute('contacts');
        }
        return $this->render("messages/new.html.twig", array('form' => $form->createView()));

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
        ->add('price', IntegerType::class, array('attr' => array('class' => 'form-control')))
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
    ->add('dispo_date', DateType::class, array('attr' => array('class' => 'form-control')))
    ->add('capacity', IntegerType::class, array('attr' => array('class' => 'form-control')))
    ->add('price', IntegerType::class, array('attr' => array('class' => 'form-control')))
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
    if ($form->isSubmitted() && $form->isValid()) {$images = $form->get('images')->getData();
    
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
