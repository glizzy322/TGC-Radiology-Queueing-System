<?php

namespace App\Services;

use App\Repositories\TicketRepository;

class TicketService
{
    protected TicketRepository $ticketRepository;

    public function __construct()
    {
        $this->ticketRepository = new TicketRepository();
    }

    public function createTicket(int $procedureId, string $procedurePrefix, int $categoryId, int $issuerId = 1): array
    {
        // issuerId defaults to 1 (System Admin) for now since Auth is Phase 4
        return $this->ticketRepository->generateTicket($procedureId, $procedurePrefix, $categoryId, $issuerId);
    }
}
