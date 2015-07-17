<?php

namespace Consolidate\Ticket\Importer\Source;

interface Source {
    /**
     * Returns a list of new events from a source.
     * This can be done by returning an array or using a yield
     * 
     * @return array
     */
    public function getEvents();
}
