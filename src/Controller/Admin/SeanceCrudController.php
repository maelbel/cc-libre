<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Horaire;
use App\Entity\Seance;
use App\Form\SeanceType;
use App\Service\Contract\CrudServiceInterface;
use App\Service\Contract\SeanceServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace administrateur - CRUD Films et Seances associees (avec pagination et
 * recherche).
 *
 * @extends AbstractCrudController<Seance>
 */
#[Route('/admin/seances')]
class SeanceCrudController extends AbstractCrudController
{
    public function __construct(private readonly SeanceServiceInterface $seanceService)
    {
    }

    protected function crudService(): CrudServiceInterface
    {
        return $this->seanceService;
    }

    protected function formClass(): string
    {
        return SeanceType::class;
    }

    protected function templatePrefix(): string
    {
        return 'admin/seance';
    }

    protected function indexRoute(): string
    {
        return 'admin_seance_index';
    }

    protected function newEntity(): object
    {
        // A Seance always owns a fresh Horaire so the embedded HoraireType
        // sub-form has something to bind to (see SeanceType/HoraireType docblocks).
        return (new Seance())->setHoraire(new Horaire());
    }

    protected function searchQueryBuilder(?string $search): QueryBuilder
    {
        return $this->seanceService->search($search);
    }

    #[Route('', name: 'admin_seance_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->doIndex($request, $paginator);
    }

    #[Route('/nouveau', name: 'admin_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->doNew($request);
    }

    #[Route('/{id}/modifier', name: 'admin_seance_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->doEdit($request, $this->getOrFail($id));
    }

    #[Route('/{id}/supprimer', name: 'admin_seance_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): RedirectResponse
    {
        return $this->doDelete($request, $this->getOrFail($id), 'delete_seance_'.$id);
    }
}
