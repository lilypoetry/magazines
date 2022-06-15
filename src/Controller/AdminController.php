<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\ObjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        // Création de la pagination de résultats
        $users = $paginatorInterface->paginate(
            $userRepository->findAll(), // Requête SQL/DQL
            $request->query->getInt('page', 1), // Numérotation des pages
            $request->query->getInt('numbers', 3) // Nombre d'enregistrements par page
        );

        return $this->render('admin/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/user/{id}/roles/{role}', name: 'app_admin_role', methods: ['POST'])]
    public function roles(User $user, string $role, UserRepository $userRepository, MailerInterface $mailerInterface, Request $request): JsonResponse
    {
        $user->setRoles([$role]);
        $userRepository->add($user, true);

        // Envoi un mail à l'utilisateur pour le prévenir d'un changement de rôle
        $email = new TemplatedEmail();

        // Expéditeur
        $email->from(new Address('no-reply@zines.test', 'Zines Admin'));

        // Destinataire
        $email->to(new Address($user->getEmail(), $user->getUserIdentifier()));

        //Objet
        $email->subject('Zines - Changement de rôle utilisateur');

        // Message
        // $email->text('Bonjor, votre rôle a été modifié. Vous $êtes maintenant un : '. $role);
        $email->htmlTemplate('emails/change_role.html.twig');

        $email->context([
            'username' => $user->getUserIdentifier(),
            'role' => $role
        ]);

        // Envoie l'email
        $mailerInterface->send($email);

        return $this->json(['role' => $role]);
    }
}
