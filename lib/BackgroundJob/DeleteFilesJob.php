<?php

namespace OCA\Easynova\BackgroundJob;

use OC\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJobList;
use OCP\Files\Config\IUserMountCache;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\IRootFolder;
use OCP\ILogger;
use OCP\Notification\IManager as NotificationManager;
use OCP\SystemTag\ISystemTagManager;
use OCP\SystemTag\ISystemTagObjectMapper;
use OCP\SystemTag\TagNotFoundException;

class DeleteFilesJob extends TimedJob {
    /** @var ISystemTagManager */
    private $tagManager;

    /** @var ISystemTagObjectMapper */
    private $tagMapper;

    /** @var IUserMountCache */
    private $userMountCache;

    /** @var IDBConnection */
    private $db;

    /** @var IRootFolder */
    private $rootFolder;

    /** @var ITimeFactory */
    private $timeFactory;

    /** @var IJobList */
    private $jobList;

    /** @var ILogger */
    private $logger;

    /** @var NotificationManager */
    private $notificationManager;
    /** @var IConfig */
    private $config;

    public function __construct(ISystemTagManager $tagManager,
                                ISystemTagObjectMapper $tagMapper,
                                IUserMountCache $userMountCache,
                                IDBConnection $db,
                                IRootFolder $rootFolder,
                                ITimeFactory $timeFactory,
                                IJobList $jobList,
                                ILogger $logger,
                                NotificationManager $notificationManager,
                                IConfig $config) {
        // Run once a day
        $this->setInterval(24 * 60 * 60);

        $this->tagManager = $tagManager;
        $this->tagMapper = $tagMapper;
        $this->userMountCache = $userMountCache;
        $this->db = $db;
        $this->rootFolder = $rootFolder;
        $this->timeFactory = $timeFactory;
        $this->jobList = $jobList;
        $this->logger = $logger;
        $this->notificationManager = $notificationManager;
        $this->config = $config;
    }

    public function run($argument) {
        // Validate if tag still exists
        $tag = $argument['tag'];
        try {
            $this->tagManager->getTagsByIds($tag);
        } catch (\InvalidArgumentException $e) {
            // tag is invalid remove backgroundjob and exit
            $this->jobList->remove($this, $argument);
            return;
        } catch (TagNotFoundException $e) {
            // tag no longer exists remove backgroundjob and exit
            $this->jobList->remove($this, $argument);
            return;
        }

        // Validate if there is an entry in the DB
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('retention')
            ->where($qb->expr()->eq('tag_id', $qb->createNamedParameter($tag)));

        $cursor = $qb->execute();
        $data = $cursor->fetch();
        $cursor->closeCursor();

        if ($data === false) {
            // No entry anymore in the retention db
            $this->jobList->remove($this, $argument);
            return;
        }

        // Do we notify the user before
        $notifyDayBefore = $this->config->getAppValue(Application::APP_ID, 'notify_before', 'no') === 'yes';

        // Calculate before date only once
        $deleteBefore = $this->getBeforeDate((int)$data['time_unit'], (int)$data['time_amount']);
        $notifyBefore = $this->getNotifyBeforeDate($deleteBefore);

        $offset = '';
        $limit = 1000;
        while ($offset !== null) {
            $fileids = $this->tagMapper->getObjectIdsForTags($tag, 'files', $limit, $offset);

            foreach ($fileids as $fileid) {
                try {
                    $node = $this->checkFileId($fileid);
                } catch (NotFoundException $e) {
                    continue;
                }

                $deleted = $this->expireNode($node, $deleteBefore);

                if ($notifyDayBefore && !$deleted) {
                    $this->notifyNode($node, $notifyBefore);
                }
            }

            if (empty($fileids) || count($fileids) < $limit) {
                break;
            }

            $offset = array_pop($fileids);
        }
    }

    /**
     * Get a node for the given fileid.
     *
     * @param int $fileid
     * @return Node
     * @throws NotFoundException
     */
    private function checkFileId($fileid) {
        $mountPoints = $this->userMountCache->getMountsForFileId($fileid);

        if (empty($mountPoints)) {
            throw new NotFoundException();
        }

        $mountPoint = array_shift($mountPoints);

        try {
            $userFolder = $this->rootFolder->getUserFolder($mountPoint->getUser()->getUID());
        } catch (\Exception $e) {
            $this->logger->logException($e, ['level' => ILogger::DEBUG]);
            throw new NotFoundException('Could not get user');
        }

        $nodes = $userFolder->getById($fileid);
        if (empty($nodes)) {
            throw new NotFoundException();
        }

        return array_shift($nodes);
    }

    /**
     * @param Node $node
     * @param \DateTime $deleteBefore
     */
    private function expireNode(Node $node, \DateTime $deleteBefore) {
        $mtime = new \DateTime();

        // Fallback is the mtime
        $mtime->setTimestamp($node->getMTime());

        // Use the upload time if we have it
        if ($node->getUploadTime() !== 0) {
            $mtime->setTimestamp($node->getUploadTime());
        }

        if ($mtime < $deleteBefore) {
            try {
                $node->delete();
                return true;
            } catch (NotPermittedException $e) {
                //LOG?
            }
        }

        return false;
    }
}
