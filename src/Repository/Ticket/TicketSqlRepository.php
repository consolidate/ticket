<?php
namespace Consolidate\Ticket\Repository\Ticket;

use Consolidate\Ticket\Ticket;

class TicketSqlRepository implements TicketRepoInterface
{
    public function find($id, $findOrFail = false)
    {
        // Use Aura.SqlQuery to build a lookup

        // Use Aura.Sql or PDO to run the query

        // if no records found and $findOrFail == true, throw exception

        // return new Tickey or TicketCollection
    }

    public function save(Ticket $ticket)
    {
        // If $ticket has a primary key, execute a
        // update...else execute an insert statement
        // using Aura.SqlQuery

        // If insert, set the auto increment key on the ticket
        // before returning true/false
    }

    public function delete(Ticket $ticket)
    {
        // Do some sanity check to see if the primary/unique
        // keys are set to avoid updating the wrong records

        // Return true/false based on affected rows
    }
}