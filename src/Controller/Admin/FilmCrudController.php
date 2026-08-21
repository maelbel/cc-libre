<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Film;
use App\Form\FilmType;
use App\Service\Contract\CrudServiceInterface;
use App\Service\Contract\FilmServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace administrateur - CRUD Films (avec pagination et recherche). Les
 * seances associees sont gerees depuis /admin/seances (voir SeanceCrudController).
 *
 * @extends AbstractCrudController<Film>
 */
#[Route('/admin/films')]
class FilmCrudController extends AbstractCrudController
{
    public function __construct(private readonly FilmServiceInterface $filmService)
    {
    }

    protected function crudService(): CrudServiceInterface
    {
        return $this->filmService;
    }

    protected function formClass(): string
    {
        return FilmType::class;
    }

    protected function templatePrefix(): string
    {
        return 'admin/film';
    }

    protected function indexRoute(): string
    {
        return 'admin_film_index';
    }

    protected function newEntity(): object
    {
        return new Film();
    }

    protected function searchQueryBuilder(?string $search): QueryBuilder
    {
        return $this->filmService->search($search);
    }

    #[Route('', name: 'admin_film_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->doIndex($request, $paginator);
    }

    #[Route('/nouveau', name: 'admin_film_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->doNew($request);
    }

    #[Route('/{id}/modifier', name: 'admin_film_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->doEdit($request, $this->getOrFail($id));
    }

    #[Route('/{id}/supprimer', name: 'admin_film_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): RedirectResponse
    {
        return $this->doDelete($request, $this->getOrFail($id), 'delete_film_'.$id);
    }
}
