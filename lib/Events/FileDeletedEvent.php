<?php

namespace OCA\Easynova\Events;

use OCP\EventDispatcher\Event;
use OCP\ILogger;

class FileDeletedEvent extends Event {

    /** @var IUser */
    // private $user;

    public function __construct(ILogger $logger) {
        parent::__construct();
        $this->logger = $logger;
    }

    // public function getUser(): IUser {
    //     return $this->user;
    // }

}