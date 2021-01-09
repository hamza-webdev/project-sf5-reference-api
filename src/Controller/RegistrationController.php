<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\SendEmail;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        EntityManagerInterface $entityManager,
        Request $request, 
        SendEmail $sendEmail,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $passwordEncoder
        ): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationToken = $tokenGenerator->generateToken();
            $user->setRegisterToken($registrationToken)                    
                 ->setPassword($passwordEncoder->encodePassword($user,$form->get('password')->getData()))
                ;

            // $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            $entityManager->flush();

            // send an email
            $sendEmail->send([
                'recepient_email'   => $user->getEmail(), 
                'subject'           => "Vérification de votre adresse email pour activer votre compte utilisateur", 
                'html_template'     => "registration/register_confirm_email.html.twig",
                'context'           =>  [
                    'userID'            => $user->getId(),
                    'registrationToken' => $registrationToken, 
                    'tokenLifeTime'     => $user->getAccountMustBeVerifeidBefore()->format('d/m/Y à H:i')
                ]

            ]);

            $this->addFlash('success', "Votre compte utilisateur a bien été crée. Veuillez consulter vos-emails. pour l'activer");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verifier url cliquer par l utilisateur appartir de son email
     * @Route("/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})
     */
     public function VerifyUserAccount(
         EntityManagerInterface $entityManager,
         User $user, 
         string $token
     ): Response
     { 
         if(($user->getRegisterToken() === null) || ($user->getRegisterToken() !== $token) || 
            ($this->isNotRequestedInTime($user->getAccountMustBeVerifeidBefore())))
            {
                throw new AccessDeniedException();
            }
        $user->setIsVerified(true)
            ->setAccountVerifiedAt(new \DatetimeImmutable('now'))
            ->setRegisterToken(null);

        $this->addFlash('success', 'Votre compte utilisateur est dés à present activé, vous pouvez vous connecter');

        return $this->redirectToRoute('app_login');

     }

     public function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifeidBefore): bool
     {
         return (new \DatetimeImmutable('now') > $accountMustBeVerifeidBefore);
     }


}
