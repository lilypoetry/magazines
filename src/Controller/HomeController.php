<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Magazine;
use App\Form\CategoryFormType;
use App\Form\MagazineFormType;
use App\Repository\CategoryRepository;
use App\Repository\MagazineRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(MagazineRepository $magazineRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'magazines' => $magazineRepository->findAll()
        ]);
    }

    #[Route('/magazine/{id}', name: 'details_magazine', requirements:['id' => '\d+'])]
    public function details(Magazine $magazine): Response // Magazine le nom de entity
    {
        return $this->render('home/details.html.twig', [
            'magazine' => $magazine
        ]);
    }

    #[Route('/category', name: 'category_list')]
    public function listCategory(CategoryRepository $categoryRepository): Response 
    {      

        return $this->render('home/listCategory.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

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

}
