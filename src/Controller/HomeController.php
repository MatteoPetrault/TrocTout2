<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use App\Repository\ImagesRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Annonce; // Assurez-vous d'importer l'entité Annonce
use App\Form\AnnonceType; // Si vous avez un formulaire pour l'annonce
use App\Entity\Images; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/accueil", name="app_home")
     */
    public function accueil(
        Security $security, 
        AnnonceRepository $annonceRepository,  
        CategorieRepository $categorieRepository, 
        ImagesRepository $imagesRepository
    ) {
        // Récupérer toutes les annonces
        $annonces = $annonceRepository->findAll();
        
        // Préparer une structure pour les annonces et leurs images
        $annoncesWithImages = [];
        foreach ($annonces as $annonce) {
            // Récupérer la catégorie de l'annonce
            $categorie = $annonce->getCategorie(); 
            
            // Récupérer les images liées à l'annonce
            $images = $imagesRepository->findBy(['annonce' => $annonce]);

            // Récupérer l'utilisateur qui a ajouté l'annonce
            $ajouter = $annonce->getAjouter(); 
            
            // Ajouter la structure des données pour chaque annonce
            $annoncesWithImages[] = [
                'annonce' => $annonce,
                'categorie' => $categorie,
                'images' => $images,
                'ajouter' => $ajouter,
            ];
        }

        $user = $security->getUser(); // Récupérer l'utilisateur connecté
        
        return $this->render('home/accueil.html.twig', [
            'user' => $user,
            'annoncesWithImages' => $annoncesWithImages, // Passer les annonces avec images au template
        ]);
    }

    #[Route('/user/mes_articles', name: 'user_mes_articles')]
    public function mes_articles(Security $security, SessionInterface $session, AnnonceRepository $annonceRepository, ImagesRepository $imagesRepository)
    {
        $userId = $session->get('user_id');
        
        // Récupérer toutes les annonces de l'utilisateur
        $annonces = $annonceRepository->findBy(
            ['ajouter' => $userId], // Filtrer par l'utilisateur qui a ajouté l'annonce
        );
    
        // Préparer une structure pour les annonces et leurs images
        $annoncesWithImages = [];
        foreach ($annonces as $annonce) {
            // Récupérer les images liées à l'annonce
            $images = $imagesRepository->findBy(['annonce' => $annonce]);
    
            // Ajouter la structure des données pour chaque annonce
            $annoncesWithImages[] = [
                'annonce' => $annonce,
                'images' => $images,
            ];
        }
    
        return $this->render('home/mes_articles.html.twig', [
            'annoncesWithImages' => $annoncesWithImages, // Passer les annonces avec images au template
        ]);
    }
    

    #[Route('/modifier-annonce/{id}', name: 'user_modifier_annonce')]
    public function modifierAnnonce(
        Request $request, 
        $id, 
        EntityManagerInterface $entityManager, 
        ImagesRepository $imagesRepository
    ) {
        // Récupération de l'annonce via l'EntityManager
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée');
        }

        // Récupérer les images liées à l'annonce
        $images = $imagesRepository->findBy(['annonce' => $annonce]);

        // Création du formulaire pour modifier l'annonce
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Sauvegarde les modifications

            return $this->redirectToRoute('user_mes_articles'); // Redirection après modification
        }

        // Passe les données nécessaires à la vue
        return $this->render('user/modifier_annonce.html.twig', [
            'form' => $form->createView(), // Formulaire pour modification
            'annonce' => $annonce,         // L'annonce en cours de modification
            'images' => $images,           // Les images associées à l'annonce
        ]);
    }


    #[Route('/supprimer-annonce/{id}', name: 'user_supprimer_annonce')]
    public function supprimerAnnonce($id, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);
        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée.');
        }
    
        // Supprimez d'abord toutes les images associées
        foreach ($annonce->getImages() as $image) {
            $entityManager->remove($image);
        }
    
        // Puis supprimez l'annonce
        $nomAnnonce = $annonce->getTitre();
        $entityManager->remove($annonce);
        $entityManager->flush();
    
        $this->addFlash('success', "Vous avez bien supprimé l'annonce : $nomAnnonce.");
        return $this->redirectToRoute('user_mes_articles');
    }
    

    /*#[Route('/ajouter-annonce', name: 'user_ajouter_annonce')]
    public function ajouterAnnonce(Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger): Response
    {
        // Créer une nouvelle annonce
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Log des erreurs si l'utilisateur n'est pas connecté
        if (!$user || !($user instanceof \App\Entity\User)) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter une annonce.');
            $logger->error('Tentative d\'ajout d\'annonce sans utilisateur connecté');
            return $this->redirectToRoute('app_login');  
        }

        // Assurez-vous que l'utilisateur connecté a un ID valide
        $userId = $user->getId(); // L'ID de l'utilisateur connecté
        $logger->info('ID utilisateur connecté : ' . $userId);

        // Log supplémentaire si nécessaire
        dump($userId);  // Affiche l'ID de l'utilisateur connecté
        die;

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur à l'annonce
            $annonce->setAjouter($user);

            // Sauvegarder l'annonce en base de données
            try {
                $entityManager->persist($annonce);
                $entityManager->flush();
                $this->addFlash('success', 'Annonce ajoutée avec succès.');
                $logger->info('Annonce ajoutée avec succès, ID de l\'annonce: ' . $annonce->getId());
            } catch (\Exception $e) {
                $logger->error('Erreur lors de l\'ajout de l\'annonce: ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'annonce.');
            }
            
            return $this->redirectToRoute('user_mes_articles');
        }

        return $this->render('ajout_annonce/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
        
    

    #[Route('/supprimer-image/{id}', name: 'user_supprimer_image')]
    public function supprimerImage($id, EntityManagerInterface $entityManager): Response
    {
        // Récupération de l'image via l'EntityManager
        $image = $entityManager->getRepository(Images::class)->find($id);

        if (!$image) {
            throw $this->createNotFoundException('Image non trouvée.');
        }

        // Suppression de l'image
        $entityManager->remove($image);
        $entityManager->flush();

        // Message flash pour informer l'utilisateur
        $this->addFlash('success', "L'image a été supprimée avec succès.");

        // Redirection vers la page précédente ou celle des articles
        return $this->redirectToRoute('user_mes_articles');
    }
    #[Route('/ajouter-image/{id}', name: 'user_ajouter_image')]
    public function ajouterImage(
        $id, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer l'annonce par son ID
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);
    
        if (!$annonce) {
            // Si l'annonce n'est pas trouvée
            $this->addFlash('error', 'Annonce non trouvée.');
            return $this->redirectToRoute('user_mes_articles');
        }
    
        // Vérifier si un fichier est soumis
        if ($request->isMethod('POST')) {
            $imageFile = $request->files->get('image');
            
            if ($imageFile) {
                try {
                    // Définir le chemin pour stocker l'image
                    $uploadsDirectory = $this->getParameter('images_directory'); // Défini dans le fichier `services.yaml`
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                    // Déplacer le fichier dans le répertoire d'upload
                    $imageFile->move($uploadsDirectory, $newFilename);
    
                    // Créer une nouvelle entité `Images`
                    $image = new Images();
                    $image->setChemin($newFilename);
                    $image->setAnnonce($annonce); // Lier l'image à l'annonce
    
                    // Sauvegarder dans la base de données
                    $entityManager->persist($image);
                    $entityManager->flush();
    
                    // Message flash pour informer l'utilisateur du succès
                    $this->addFlash('success', 'Image ajoutée avec succès.');
    
                } catch (\Exception $e) {
                    // Si une erreur se produit lors de l'upload
                    $this->addFlash('error', 'Erreur lors de l\'ajout de l\'image : ' . $e->getMessage());
                }
            } else {
                // Si aucun fichier n'est sélectionné
                $this->addFlash('error', 'Aucun fichier image sélectionné.');
            }
        } else {
            // Si la méthode n'est pas POST
            $this->addFlash('error', 'La requête doit être une méthode POST.');
        }
    
        // Redirection vers la page des articles
        return $this->redirectToRoute('user_mes_articles');
    }
    #[Route('/save-image', name: 'save_image', methods: ['POST'])]
    public function saveImage(Request $request, EntityManagerInterface $entityManager, AnnonceRepository $annonceRepository)
    {
        // Récupération de l'image et de l'ID de l'annonce depuis la requête
        $image = $request->files->get('image_upload');
        $annonceId = $request->get('annonce_id'); // ID de l'annonce associée

        // Vérification si l'image et l'annonce existent
        if (!$image) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Aucune image téléchargée.'
            ], 400); // Bad request
        }

        if (!$annonceId || !$annonceRepository->find($annonceId)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Annonce introuvable.'
            ], 404); // Not found
        }

        try {
            // Traitement de l'image (renommage, déplacement, etc.)
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = uniqid() . '.' . $image->guessExtension(); // Ajout du nom de fichier unique

            // Déplacer l'image vers le répertoire approprié
            $image->move(
                $this->getParameter('images_directory'), // Paramètre de dossier défini dans config/services.yaml
                $newFilename
            );

            // Lier l'image à l'annonce
            $annonce = $annonceRepository->find($annonceId);
            $imageEntity = new Images();
            $imageEntity->setChemin($newFilename);
            $imageEntity->setAnnonce($annonce);
            $imageEntity->setDateCreation(new \DateTime());

            // Sauvegarder l'image dans la base de données avec l'EntityManager
            $entityManager->persist($imageEntity);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'image_url' => $newFilename
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de l\'image : ' . $e->getMessage()
            ], 500); // Internal server error
        }
    }

        
}
    
    


