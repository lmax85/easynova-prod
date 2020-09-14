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
        // Run once a day
        $this->setInterval(5);

        $this->db = $db;
        $this->timeFactory = $timeFactory;
        $this->logger = $logger;
        $this->fileService = $fileService;
        $this->storage = $storage;
    }

    public function run($argument) {

        var_dump('backgroundjob DeleteFiles >>> called');

        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('custom_properties')
            ->where($qb->expr()->eq('name', $qb->createNamedParameter('time_to_live')));
        $cursor = $qb->execute();
        $property = $cursor->fetch();

        if ($property) {
            $qb = $this->db->getQueryBuilder();
            $qb->select('*')
                ->from('file_property')
                ->where($qb->expr()->eq('property_id', $qb->createNamedParameter($property['id'])));

            $cursor = $qb->execute();
            $data = $cursor->fetchAll();

            if (count($data) > 0) {
                foreach ($data as $file) {
                    $this->checkForDelete($file);
                }
            }
        }

        var_dump('backgroundjob DeleteFiles >>> end');
    }

    public function checkForDelete($file) {
        $now = new \Datetime();
        $expireDate = new \Datetime($file['created_at']);
        $period = explode('|', $file['value']);

        if ($period[0] > 0 && in_array($period[1], self::PERIODS)) {
            $expireDate->modify("+{$period[0]} {$period[1]}");

            if ($now > $expireDate) {
                try {
                    $this->fileService->delete($file);
                    var_dump('file deleted ' . $file['file_id']);
                } catch (Exception $e) {
                    var_dump('cant delete file with id = ' . $file['file_id']);
                }

            }
        }
    }
}
