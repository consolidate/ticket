<?php
namespace Consolidate\Ticket\Repository;

use Consolidate\Ticket\Ticket;

interface RepoInterface
{
    public function find($id, $findOrFail = false);

    public function save(Ticket $ticket);

    public function delete(Ticket $ticket);
}