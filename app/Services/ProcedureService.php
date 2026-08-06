<?php

namespace App\Services;

use App\Repositories\ProcedureRepository;

class ProcedureService
{
    protected ProcedureRepository $procedureRepository;

    public function __construct()
    {
        $this->procedureRepository = new ProcedureRepository();
    }

    public function getAllProcedures(): array
    {
        return $this->procedureRepository->getAll();
    }
}
