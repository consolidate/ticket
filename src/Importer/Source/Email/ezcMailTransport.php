<?php

namespace Consolidate\Ticket\Importer\Source\Email;

use Consolidate\Ticket\Data\Email;
use Consolidate\Ticket\Data\Participant;

use \ezcMailParser;
use \ezcMailImapTransportOptions;
use \ezcMailImapTransport;

class ezcMailTransport implements MailInterface {

    protected $source;

    /**
     * {@inheritdocs}
     */
    public function __construct($host, $username, $password, $folder = 'INBOX', $ssl = true)
    {
        $options = new ezcMailImapTransportOptions();
        $options->ssl = $ssl;

        $this->source = new ezcMailImapTransport($host, null, $options);

        // Authenticate to the IMAP server
        $this->source->authenticate($username, $password);

        // Select the Inbox mailbox
        $this->source->selectMailbox($folder);
    }

    /**
     * {@inheritdocs}
     */
    public function getUnseenMail()
    {
        $set = $this->source->countByFlag('UNSEEN');
        $parser = new ezcMailParser();

        // Wrap our result in a email interface class
        return array_map(function ($mail) {
            return new Email(
                $mail->generate(),
                new Participant($mail->from->email),
                new Participant($mail->headers['Delivered-To'][0])
            );
        }, $parser->parseMail($set));
    }
}
