<?php

namespace App\UI\Controller;

use App\Application\UseCase\LoadAllWidgets;
use App\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(LoadAllWidgets $allWidgets): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to access the dashboard.');
        }

        $dashboard = $currentUser->getDashboard();

        $widgets = $allWidgets->execute($dashboard);

        return $this->render('dashboard/index.html.twig', [
            'widgets' => $widgets,
        ]);
    }
}
