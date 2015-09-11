<?php

namespace Consolidate\Ticket\Data;

/**
 * Describes the basic functionality of data store
 */
interface Store
{
    /**
     * Does this store support a particular data set?
     *
     * @param  Conslidate\Ticket\Data\Data $data
     * @return boolean
     */
    public function supports($data);

    /**
     * Resolve the data set based on what data already exists in it
     *
     * @param Conslidate\Ticket\Data\Data $data
     */
    public function resolve($data);

    /**
     * Persist the data set
     *
     * @param Conslidate\Ticket\Data\Data $data
     */
    public function persist($data);
}
