<?php

namespace OCA\Easynova\Hooks;

use OCP\IUserManager;
use OCP\Ilogger;
use OCP\Exception;

class UserHooks {

    private $userManager;

    /** @var ILogger */
    protected $logger;

    public function __construct(IUserManager $userManager, Ilogger $logger){
        $this->userManager = $userManager;
        $this->logger = $logger;
    }

    public function register() {
        // $this->logger->error('UserHooks >> register');
        $callback = function($user) {
            // your code that executes before $user is deleted
            $this->logger->error('UserHooks >> callback called');
            // throw new Exception("UserHooks >> callback called", 1);
        };
        $this->userManager->listen('\OC\User', 'preDelete', $callback);
        $this->userManager->listen('\OC\User', 'postCreateUser', $callback);
    }

}