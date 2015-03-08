<?php

namespace Consolidate\Ticket\Repository\Storage;

use Consolidate\Ticket\Ticket;

interface Store
{
   public function load($id);
   public function persist(Ticket $ticket);
}