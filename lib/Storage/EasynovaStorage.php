<?php

namespace OCA\Easynova\Storage;

use OCP\Files\Config\IUserMountCache;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Files\Storage;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\IRootFolder;
use OCP\ILogger;
use OCP\User;
use OCP\EventDispatcher\IEventDispatcher;

use OCA\Easynova\Events\FileDeletedEvent;
use OCA\Easynova\Hooks\FileHooksStatic;

class EasynovaStorage {

    const INBOX_FOLDER = 'inbox';

    /** @var IUserMountCache */
    private $userMountCache;

    /** @var IDBConnection */
    private $db;

    /** @var IRootFolder */
    private $rootFolder;

    /** @var ILogger */
    private $logger;

    /** @var IConfig */
    private $config;

    /** @var User */
    private $user;

    public function __construct(IUserMountCache $userMountCache,
                                IDBConnection $db,
                                IRootFolder $rootFolder,
                                ILogger $logger,
                                IConfig $config,
                                User $user,
                                IEventDispatcher $eventDispatcher) {
        $this->userMountCache = $userMountCache;
        $this->db = $db;
        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
        $this->config = $config;
        $this->user = $user;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @NoCSRFRequired
     */
    public function store($file, $userUID) {
        try {
            $userFolder = $this->rootFolder->getUserFolder($userUID);
        } catch (Exception $e) {
            throw new NotFoundException('Could not found user with id = ' . $userUID);
        }

        if (!$userFolder->nodeExists(self::INBOX_FOLDER)) {
            try {
                $userFolder->newFolder(self::INBOX_FOLDER);
            } catch (NotPermittedException $e) {
                $this->logger->logException($e, ['level' => ILogger::DEBUG]);
                throw new NotPermittedException('Could not create folder');
            }
        }

        try {
            $path = self::INBOX_FOLDER . '/' . $file['name'];
            $newFile = $userFolder->newFile($path);
            $newFile->putContent(file_get_contents($file['tmp_name']));

            return $newFile->getId();
        } catch (NotPermittedException $e) {
            $this->logger->logException($e, ['level' => ILogger::DEBUG]);
            throw new NotPermittedException('Could not store file');
        }
    }

    /**
     * Get a node for the given fileid.
     *
     * @param int $fileid
     * @return Node
     * @throws NotFoundException
     */
    public function get($fileId, $userId) {
        try {
            $userFolder = $this->rootFolder->getUserFolder($userId);
        } catch (\Exception $e) {
            $this->logger->logException($e, ['level' => ILogger::DEBUG]);
            throw new NotFoundException('Could not get user');
        }

        $nodes = $userFolder->getById($fileId);

        if (empty($nodes)) {
            throw new NotFoundException('Could not get file by id = ' . $fileId . ' for user_id = ' . $userId);
        }

        return array_shift($nodes);
    }

    /**
     * @NoCSRFRequired
     */
    public function delete($fileEasnynova) {
        try {
            $fileNode = $this->get($fileEasnynova['file_id'], $fileEasnynova['user_id']);
            $fileNode->delete();

            // call file hook for change row in DB and send request to backend
            FileHooksStatic::fileDeletedByCronJob($fileEasnynova['id']);

            return true;
        } catch(NotFoundException $e) {
            return $e->getMessage();
        }
    }
}
