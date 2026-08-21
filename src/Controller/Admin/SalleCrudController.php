<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Service\Contract\CrudServiceInterface;
use App\Service\Contract\SalleServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace administrateur - CRUD Salles (avec pagination et recherche).
 *
 * @extends AbstractCrudController<Salle>
 */
#[Route('/admin/salles')]
class SalleCrudController extends AbstractCrudController
{
    public function __construct(private readonly SalleServiceInterface $salleService)
    {
    }

    protected function crudService(): CrudServiceInterface
    {
        return $this->salleService;
    }

    protected function formClass(): string
    {
        return SalleType::class;
    }

    protected function templatePrefix(): string
    {
        return 'admin/salle';
    }

    protected function indexRoute(): string
    {
        return 'admin_salle_index';
    }

    protected function newEntity(): object
    {
        return new Salle();
    }

    protected function searchQueryBuilder(?string $search): QueryBuilder
    {
        return $this->salleService->search($search, null);
    }

    #[Route('', name: 'admin_salle_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->doIndex($request, $paginator);
    }

    #[Route('/nouveau', name: 'admin_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->doNew($request);
    }

    #[Route('/{id}/modifier', name: 'admin_salle_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->doEdit($request, $this->getOrFail($id));
    }

    #[Route('/{id}/supprimer', name: 'admin_salle_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): RedirectResponse
    {
        return $this->doDelete($request, $this->getOrFail($id), 'delete_salle_'.$id);
    }
}
