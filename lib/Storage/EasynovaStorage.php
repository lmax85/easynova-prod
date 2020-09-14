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
                                User $user) {
        $this->userMountCache = $userMountCache;
        $this->db = $db;
        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
        $this->config = $config;
        $this->user = $user;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function store($file) {
        $userUID = $this->user->getUser();
        $userFolder = $this->rootFolder->getUserFolder($userUID);

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
    public function get($fileId) {
        $mountPoints = $this->userMountCache->getMountsForFileId($fileId);

        if (empty($mountPoints)) {
            throw new NotFoundException('Could not get file by id = ' . $fileId);
        }

        $mountPoint = array_shift($mountPoints);

        try {
            $userFolder = $this->rootFolder->getUserFolder($mountPoint->getUser()->getUID());
        } catch (\Exception $e) {
            $this->logger->logException($e, ['level' => ILogger::DEBUG]);
            throw new NotFoundException('Could not get user');
        }

        $nodes = $userFolder->getById($fileId);

        if (empty($nodes)) {
            throw new NotFoundException('File found but have errors');
        }

        return array_shift($nodes);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function delete($fileId) {
        try {
            $fileNode = $this->get($fileId);
            $fileNode->delete();

            return true;
        } catch(NotFoundException $e) {
            return $e->getMessage();
        }
    }
}
