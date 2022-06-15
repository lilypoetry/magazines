<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Magazine;
use App\Form\CategoryFormType;
use App\Form\MagazineFormType;
use App\Repository\CategoryRepository;
use App\Repository\MagazineRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(MagazineRepository $magazineRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        // Création de la pagination de résultats
        $magazines = $paginatorInterface->paginate(
            $magazineRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 5) // Nombre d'enregistrements par page
        );

        return $this->render('home/index.html.twig', [
            // 'magazines' => $magazineRepository->findAll()
            'magazines' => $magazines
        ]);
    }

    #[Route('/magazine/{id}', name: 'details_magazine', requirements:['id' => '\d+'])]
    public function details(Magazine $magazine): Response // Magazine le nom de entity
    {
        return $this->render('home/details.html.twig', [
            'magazine' => $magazine
        ]);
    }

    // crée une route pour afficher la liste de category
    #[Route('/category', name: 'category_list')]
    public function listCategory(CategoryRepository $categoryRepository): Response 
    {      

        return $this->render('home/listCategory.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    
    // crée une route pour afficher une formulaire ajout d'une nouvelle category
    #[Route('category/new', name: 'category_new')]
    public function newCategory(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est valide
            $categoryRepository->add($category, true); // enregistrer

            $this->addFlash('success', 'Votre categorie est bien été enregistré !');

            // Redirection vers une autre page "#[Route('/category', name: 'category_list')]"
            return $this->redirectToRoute('category_list');

            // $category = new Category();
            // $this->createForm(CategoryFormType::class, $category);
        }

        return $this->render('home/newCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]

    // Crée une route pour afficher une formulaire ajoute d'un nouveau magazine
    #[Route('/magazine/new', name: 'new_magazine')]
    public function new(Request $request, MagazineRepository $magazineRepository): Response
    {
        $magazine = new Magazine();
        $form = $this->createForm(MagazineFormType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $magazineRepository->add($magazine, true);

            $this->addFlash('success', 'Le magazine à bien été enregistré !');
            
            return $this->redirectToRoute('app_home');            
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView()
        ]);
        
    }

    #[IsGranted('ROLE_USER')]

    // Creée une route pour editer une category
    #[Route('/category/edit/{id}', name: 'edit_category', requirements: ['id' => '\d+'])]
    public function edit(Category $category, CategoryRepository $categoryRepository, Request $request): Response
    {
        // $category = new Category(); est recuperer dans edit(Category $category, ...)
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryRepository->add($category, true);
            $this->addFlash('success', 'La catégorie à bien été enregistrée');            

            return $this->redirectToRoute('category_list');
        }
        return $this->render('home/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]

    // Créer une route pour supprime une category
    #[Route('/category/delete/{id}', name: 'delete_category', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Category $category, CategoryRepository $categoryRepository, Request $request): RedirectResponse
    {
        $tokenCsrf = $request->request->get('token');

        // compare le jetton avec le serveur
        // passer une clé 'delete-category-'. $category->getId()'
        if ($this->isCsrfTokenValid('delete-category-'. $category->getId(), $tokenCsrf)) {

            $categoryRepository->remove($category, true);
            $this->addFlash('success', 'La catégorie à bien été supprimé');
        }

        return $this->redirectToRoute('category_list');
    }

    #[IsGranted('ROLE_USER')]

    // Creée une route pour editer un magazine
    #[Route('/magazine/modif/{id}', name: 'edit_magazine', requirements: ['id' => '\d+'])]
    public function modif(Magazine $magazine, MagazineRepository $magazineRepository, Request $request): Response
    {
        // $magazine = new Magazine(); est recuperer dans edit(Magazine $magazine, ...)
        $form = $this->createForm(MagazineFormType::class, $magazine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $magazineRepository->add($magazine, true);
            $this->addFlash('success', 'Le magazine à bien été enregistrée');            

            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/editmag.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')] // pour avoir access
    // Créer une route pour supprime un magazine
    #[Route('/magazine/unset/{id}', name: 'delete_magazine', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unset(Magazine $magazine, MagazineRepository $magazineRepository, Request $request): RedirectResponse
    {
        $tokenCsrf = $request->request->get('token');

        // compare le jetton avec le serveur
        // passer une clé 'delete-category-'. $category->getId()'
        if ($this->isCsrfTokenValid('delete-magazine-'. $magazine->getId(), $tokenCsrf)) {

            $magazineRepository->remove($magazine, true);
            $this->addFlash('success', 'Le magazine à bien été supprimé');
        }

        return $this->redirectToRoute('app_home');
    }

}
