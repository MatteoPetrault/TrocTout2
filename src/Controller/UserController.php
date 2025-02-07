<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;


class UserController extends AbstractController
{
    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('troctout.vente@yahoo.com')
            ->to('matteo.petraultpro@gmail.com')
            ->subject('Test d\'envoi via Yahoo')
            ->text('Ceci est un test d’envoi d’email avec Yahoo SMTP via Symfony.');

        $mailer->send($email);

        return new Response('Email envoyé avec succès.');
    }
    #[Route('/user/create', name: 'user_create', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $mail = $request->request->get('mail');
        $mdp = $request->request->get('mdp');
        $rue = $request->request->get('rue');
        $cp = $request->request->get('cp');
        $ville = $request->request->get('ville');
        $pays = $request->request->get('pays');

        // Validation simple des données requises
        if (empty($nom) || empty($prenom) || empty($mail) || empty($mdp) || empty($rue) || empty($cp) || empty($ville) || empty($pays)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
            return $this->redirectToRoute('app_index');
        }

        // Vérification si l'email existe déjà
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);
        if ($existingUser) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('app_index');
        }

        // Génération du code de vérification à 6 chiffres
        $verificationCode = random_int(100000, 999999);

        // Création d'un nouvel utilisateur
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setMail($mail);
        $user->setMdp(password_hash($mdp, PASSWORD_BCRYPT));
        $user->setRue($rue);
        $user->setCp($cp);
        $user->setVille($ville);
        $user->setPays($pays);
        $user->setDerniereConnexion(new \DateTime());
        $user->setVerificationCode($verificationCode);
        $user->setIsVerified(false); // Nouvel utilisateur non vérifié

        // Enregistrement en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Envoi de l'email de vérification
        $email = (new Email())
            ->from('matteo.petraultpro@gmail.com')
            ->to($mail)
            ->subject('Code de vérification de votre compte')
            ->text("Bonjour $prenom, votre code de vérification est : $verificationCode");

        $mailer->send($email);

        $this->addFlash('success', 'Un code de vérification a été envoyé à votre adresse email.');

        return $this->redirectToRoute('app_verify'); // Redirection vers la page de vérification
    }
    #[Route('/user/verify', name: 'user_verify', methods: ['POST'])]
    public function verifyUser(Request $request, EntityManagerInterface $entityManager)
    {
        $mail = $request->request->get('mail');
        $code = $request->request->get('code');

        $user = $entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);

        if (!$user || $user->getVerificationCode() != $code) {
            $this->addFlash('error', 'Code de vérification incorrect.');
            return $this->redirectToRoute('app_verify');
        }

        // Valider l'utilisateur
        $user->setIsVerified(true);
        $user->setVerificationCode(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est vérifié. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }

    

    #[Route('/user/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $mail = trim($request->request->get('mail'));
        $mdp = $request->request->get('mdp');
    
        if (empty($mail) || empty($mdp)) {
            $this->addFlash('error', 'Tous les champs sont obligatoires.');
            return $this->redirectToRoute('app_home');
        }
    
        $user = $entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);
    
        if (!$user || !password_verify($mdp, $user->getMdp())) {
            $this->addFlash('error', 'Utilisateur ou mot de passe incorrect.');
            return $this->redirectToRoute('app_index');
        }
    
        // Stocker les informations dans la session
        $session->set('user_id', $user->getId());
        $session->set('user_mail', $user->getMail());
        $session->set('user_prenom', $user->getPrenom());
        $session->set('user_nom', $user->getNom());
    
        return $this->redirectToRoute('app_home');
    }
    
    #[Route('/user/logout', name: 'user_logout')]
    public function logout(SessionInterface $session)
    {
        // Vider les données de session
        $session->remove('user_id');
        $session->remove('user_mail');
        $session->remove('user_prenom');
        $session->remove('user_nom');

        // Rediriger vers la page d'accueil ou une autre page
        return $this->redirectToRoute('app_index');
    }
   

    

    
}
