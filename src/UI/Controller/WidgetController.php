<?php

namespace App\UI\Controller;

use App\Application\DTO\CreateWidgetDTO;
use App\Application\UseCase\CreateWidget;
use App\Application\UseCase\DeleteWidget;
use App\Application\UseCase\ReorderWidgets;
use App\Application\UseCase\ResolveWidgetData;
use App\Domain\Entity\User;
use App\Domain\Entity\Widget;
use App\Infrastructure\Doctrine\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WidgetController extends AbstractController
{
    #[Route('/widget/new', name: 'app_widget_create', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(): Response
    {
        return $this->render('widget/new.html.twig');
    }

    #[Route('/widget/new', name: 'app_widget_store', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function store(#[MapRequestPayload] CreateWidgetDTO $createWidgetDTO, CreateWidget $createWidget): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to create a widget.');
        }

        $createWidget->execute($createWidgetDTO, $user->getDashboard());

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/widgets/reorder', name: 'app_widget_reorder', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function reorder(Request $request, ReorderWidgets $reorderWidgets, CsrfTokenManagerInterface $csrf): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrf->isTokenValid(new CsrfToken('reorder-widgets', (string)$token))) {
            return $this->json(['error' => 'Invalid CSRF token'], 400);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['order']) || !is_array($data['order'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $reorderWidgets->execute($data);

        return $this->json(['ok' => true]);
    }

    #[Route('/widget/resolve/{id}', name: 'app_widget_resolve', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function resolve(ResolveWidgetData $resolveWidgetData, #[MapEntity] Widget $widget): Response
    {
        try {
            $dashboard = $widget->getDashboard();
            $dashboardUser = $dashboard->getAuthor();

            $currentUser = $this->getUser();
            if (!$currentUser instanceof User || $currentUser->getId() !== $dashboardUser->getId()) {
                throw $this->createAccessDeniedException('You do not have permission to delete this widget.');
            }

            $widgetData = $resolveWidgetData->execute($widget);

            return $this->json([
                'data' => $widgetData->getResult(),
            ], $widgetData->isSuccess() ? 200 : 400);
        } catch (\Exception $e) {
            return $this->json([
                'data' => 'An error occurred while resolving widget data.',
            ], 500);
        }
    }

    #[Route('/widget/{id}/delete', name: 'app_widget_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(#[MapEntity] Widget $widget, Request $request, DeleteWidget $deleteWidget): Response
    {
        $dashboard = $widget->getDashboard();
        $dashboardUser = $dashboard->getAuthor();

        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || $currentUser->getId() !== $dashboardUser->getId()) {
            throw $this->createAccessDeniedException('You do not have permission to delete this widget.');
        }

        if (!$this->isCsrfTokenValid('delete-widget-' . $widget->getId(), $request->request->get('_token'))) {
             throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $deleteWidget->execute($widget);

        return $this->redirectToRoute('app_dashboard');
    }
}
