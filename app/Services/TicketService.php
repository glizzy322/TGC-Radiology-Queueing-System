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

    public function createTicket(int $procedureId, string $procedurePrefix, int $categoryId, int $issuerId): array
    {
        return $this->ticketRepository->generateTicket($procedureId, $procedurePrefix, $categoryId, $issuerId);
    }
}
