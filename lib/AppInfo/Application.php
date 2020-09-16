<?php
declare(strict_types=1);

namespace OCA\Easynova\AppInfo;

use OC\Files\Filesystem;
use OCP\AppFramework\App;
use OCP\Util;
use OCP\EventDispatcher\IEventDispatcher;

use OCA\Easynova\Events\FileDeletedEvent;
use OCA\Easynova\Listeners\FileDeletedEventListener;
use OCA\Easynova\Hooks\FileHooksStatic;

class Application extends App {

    public function __construct(array $urlParams=array()) {
        parent::__construct('easynova', $urlParams);

        $container = $this->getContainer();
        // $logger = $container->getServer()->getLogger();
        // $this->logger = $logger;

        /* @var IEventDispatcher $eventDispatcher */
        // $dispatcher = $this->getContainer()->query(IEventDispatcher::class);
        // $dispatcher->addServiceListener(FileDeletedEvent::class, FileDeletedEventListener::class);
        // $this->dispatcher = $dispatcher;

    }

    public function registerHooks() {
        Util::connectHook('OC_Filesystem', 'read', FileHooksStatic::class, 'fileReaded');

        // this delete hook called only from Nextcloud GUI interface when you delete file by usual action (move to trash)
        // for our delete method from cron job we call this FileHooksStatic::fileDeleted manually from OCA\Easynova\Storage\EasynovaStorage
        Util::connectHook('OC_Filesystem', 'delete', FileHooksStatic::class, 'fileDeleted');

        // @TODO can register user hooks here
    }
}
