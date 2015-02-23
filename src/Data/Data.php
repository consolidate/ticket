<?php

namespace Consolidate\Ticket\Data;

/**
 * Describes the basic requirements for a piece of data in a tickets timeline
 */
interface Data {
    public function __toString();
}