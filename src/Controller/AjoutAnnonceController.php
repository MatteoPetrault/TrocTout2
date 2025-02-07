<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Images;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ImagesRepository;
use App\Form\AjoutAnnonceType;
use Symfony\Bundle\SecurityBundle\Security;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AjoutAnnonceController extends AbstractController
{
    private $security;
    private $entityManager;

    // Injection de l'EntityManager et du service Security via le constructeur
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/ajouter-annonce2', name: 'user_ajouter_annonce2')]
    public function ajouterAnnonce(
        Request $request, 
        LoggerInterface $logger,
        SessionInterface $session,
        ImagesRepository $imagesRepository
    ): Response {

        $annonce = new Annonce();
        $form = $this->createForm(AjoutAnnonceType::class, $annonce);
        $form->handleRequest($request);

        $userId = $session->get('user_id');
        
        if (!$userId) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter une annonce.');
            $logger->error('Tentative d\'ajout d\'annonce sans utilisateur connecté');
            return $this->redirectToRoute('app_index');  
        }

        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($userId);
        
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_index');
        }

        $logger->info('ID utilisateur connecté : ' . $userId);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setAjouter($user);

            $images = $form->get('image')->getData();

            // Si des images ont été téléchargées
            if ($images) {
                // Pour chaque image téléchargée
                foreach ($images as $imageFile) {
                    // Créer un objet Image
                    $image = new Images();

                    // Générer un nom unique pour chaque image
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                    // Déplacer le fichier dans le répertoire des images
                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'), // Défini dans config/services.yaml
                            $newFilename
                        );

                        // Enregistrer le nom du fichier dans l'objet Image
                        $image->setChemin($newFilename);
                        $image->setAnnonce($annonce); // Associer l'image à l'annonce
                        $image->setDateCreation(new \DateTime());

                        // Persister l'image en base de données
                        $this->entityManager->persist($image);
                    } catch (\Exception $e) {
                        $logger->error('Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
                    }
                }
            }

            // Sauvegarder l'annonce en base de données
            try {
                $this->entityManager->persist($annonce);
                $this->entityManager->flush();
                $this->addFlash('success', 'Annonce ajoutée avec succès.');
                $logger->info('Annonce ajoutée avec succès, ID de l\'annonce: ' . $annonce->getId());
            } catch (\Exception $e) {
                // Gérer les erreurs d'ajout d'annonce
                $logger->error('Erreur lors de l\'ajout de l\'annonce: ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'annonce.');
            }
            
            // Rediriger vers la page des annonces de l'utilisateur
            return $this->redirectToRoute('user_mes_articles');
        }


        // Afficher le formulaire pour l'ajout d'annonce
        return $this->render('ajout_annonce/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
