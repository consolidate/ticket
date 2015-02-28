<?php

namespace Consolidate\Ticket\Data;

/**
 * Describes the basic functionality of data store
 */
interface Store
{
    public function supports($data);
    public function resolve($data);
    public function persist($data);
}
