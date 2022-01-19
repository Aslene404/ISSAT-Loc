<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Carbon\Carbon;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use \Mailjet\Resources;

class RegistrationController extends AbstractController
{    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface,FlashyNotifier $flashy): Response
    {
        $user = new User();
      
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $user->setIsVerfied(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
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
            'Email' => $user->getEmail(),
            'Name' => $user->getFirstname(),
          ]
        ],
        'Subject' => "Activation du compte.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Bienvenue sur notre platforme Issat Co-Location vous pouvez activer votre compte <a href='http://127.0.0.1:8000/activate/{$user->getId()}'>Ici</a>!</h3>",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
            // do anything else you need here, like send an email
            $flashy->info('Consulter votre Email pour activer votre compte');
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
        
        
    }
}
