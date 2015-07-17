<?php

namespace Consolidate\Ticket\Importer\Source\Email;

interface TransportInterface {
    /**
     * Setup our email transport
     * 
     * @param string  $host     Hostname of the mail server
     * @param string  $username Mail username
     * @param string  $password Mail password
     * @param string  $folder   Folder to process
     * @param boolean $ssl      Enable/disable SSL
     */
    public function __construct($host, $username, $password, $folder = 'INBOX', $ssl = true);

    /**
     * Return all unseen mail from the mailbox
     * @return array List of Consolidate\Ticket\Data\Email in the mail box
     */
    public function getUnseenMail();
}