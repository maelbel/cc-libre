<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Cinema;
use App\Form\CinemaType;
use App\Service\Contract\CinemaServiceInterface;
use App\Service\Contract\CrudServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace administrateur - CRUD Cinemas (avec pagination et recherche).
 *
 * @extends AbstractCrudController<Cinema>
 */
#[Route('/admin/cinemas')]
class CinemaCrudController extends AbstractCrudController
{
    public function __construct(private readonly CinemaServiceInterface $cinemaService)
    {
    }

    protected function crudService(): CrudServiceInterface
    {
        return $this->cinemaService;
    }

    protected function formClass(): string
    {
        return CinemaType::class;
    }

    protected function templatePrefix(): string
    {
        return 'admin/cinema';
    }

    protected function indexRoute(): string
    {
        return 'admin_cinema_index';
    }

    protected function newEntity(): object
    {
        return new Cinema();
    }

    protected function searchQueryBuilder(?string $search): QueryBuilder
    {
        return $this->cinemaService->search($search);
    }

    #[Route('', name: 'admin_cinema_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->doIndex($request, $paginator);
    }

    #[Route('/nouveau', name: 'admin_cinema_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->doNew($request);
    }

    #[Route('/{id}/modifier', name: 'admin_cinema_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->doEdit($request, $this->getOrFail($id));
    }

    #[Route('/{id}/supprimer', name: 'admin_cinema_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): RedirectResponse
    {
        return $this->doDelete($request, $this->getOrFail($id), 'delete_cinema_'.$id);
    }
}
