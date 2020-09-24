<?php

namespace OCA\Easynova\Cron;

use OC\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IDBConnection;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\ILogger;

use OCA\Easynova\Service\FileService;
use OCA\Easynova\Storage\EasynovaStorage;

class DeleteFiles extends TimedJob {
    /** @var IDBConnection */
    private $db;

    /** @var ITimeFactory */
    private $timeFactory;

    /** @var ILogger */
    private $logger;

    /** @var ILogger */
    private $fileService;

    /** @var EasynovaStorage */
    private $storage;

    const PERIODS = [
        'minutes', 'hours', 'days', 'weeks'
    ];

    public function __construct(IDBConnection $db,
                                ITimeFactory $timeFactory,
                                ILogger $logger,
                                FileService $fileService,
                                EasynovaStorage $storage) {
        // Run once every 5 minutes
        $this->setInterval(5 * 60);

        $this->db = $db;
        $this->timeFactory = $timeFactory;
        $this->logger = $logger;
        $this->fileService = $fileService;
        $this->storage = $storage;
    }

    public function run($argument) {

        var_dump('backgroundjob DeleteFiles >>> called');

        // get not deleted files for checking
        $files = $this->fileService->getNotDeleteFiles();

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->checkForDelete($file);
            }
        }

        var_dump('backgroundjob DeleteFiles >>> end');
    }

    public function checkForDelete($file) {
        $now = new \Datetime();
        $expireDate = new \Datetime($file['created_at']);
        $period = explode('|', $file['property_value']);

        if ($period[0] > 0 && in_array($period[1], self::PERIODS)) {
            $expireDate->modify("+{$period[0]} {$period[1]}");

            if ($now > $expireDate) {
                try {
                    $this->logger->info('CronJob DeleteFiles >> delete file with file_id = ' . $file['id'] . ' user_id = ' . $file['user_id']);
                    $this->fileService->delete($file);
                    $this->logger->info('======================================================');
                    var_dump('file deleted with file_id = ' . $file['id'] . ' user_id = ' . $file['user_id']);
                } catch (Exception $e) {
                    var_dump('cant delete file with file_id = ' . $file['id'] . ' user_id = ' . $file['user_id']);
                }

            }
        }
    }
}
