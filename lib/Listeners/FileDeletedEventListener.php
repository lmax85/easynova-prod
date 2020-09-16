<?php

namespace OCA\Easynova\Listeners;

use OCA\Easynova\Events\FileDeletedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

class FileDeletedEventListener implements IEventListener {

    public function handle(Event $event): void {
        if (!($event instanceOf FileDeletedEvent)) {
            return;
        }

        $event->logger->info('FileDeletedListener >> handle');
    }
}