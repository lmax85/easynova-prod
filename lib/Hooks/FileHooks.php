<?php

namespace OCA\Easynova\Hooks;

use OCP\Files\FileInfo;
use OCP\ILogger;

use OCA\Easynova\Service\FileService;
use OCA\Easynova\Service\SendRequestService;

/**
 * The class to handle the filesystem hooks
 */
class FileHooks {
    /** @var ILogger */
    protected $logger;

    public function __construct(ILogger $logger, FileService $fileService, SendRequestService $sendRequestService) {
        $this->logger = $logger;
        $this->fileService = $fileService;
        $this->sendRequestService = $sendRequestService;
    }

    /**
     * Hook when user read file
     * 1. add mark readed_at to files_easynova row DB
     * 2. send http request to Easynova backend
     * @param  {string} $path [path to file at nextcloud]
     * @return void
     */
    public function fileReaded($path)
    {
        $view = \OC\Files\Filesystem::getView();
        $node = $view->getFileInfo($path);

        if ($node->getType() === FileInfo::TYPE_FILE) {
            try {
                $fileEasynova = $this->fileService->readFileEasynova($node->getId());

                // need to check - because it hook call on all files
                if (!is_null($fileEasynova)) {
                    $this->logger->info('FileHooks >> fileReaded | id = ' . $fileEasynova->id . ' | name = ' . $fileEasynova->fileName, ['app' => 'easynova']);
                    $this->sendRequestService->sendFileReaded($fileEasynova);
                }
            } catch (Exception $e) {
                $this->logger->error('Error in fileReaded hook call...');
            }
        } else {
            $this->logger->error('FileHooks >> fileReaded | error: no found file with path = ' . $path, ['app' => 'easynova']);
        }
    }

    /**
     * Hook when user delete file
     * 1. add mark deleted_at to files_easynova row DB
     * 2. send http request to Easynova backend
     * @param  {string} $path [path to file at nextcloud]
     * @return void
     */
    public function fileDeleted($path)
    {
        $view = \OC\Files\Filesystem::getView();
        $node = $view->getFileInfo($path);

        if ($node->getType() === FileInfo::TYPE_FILE) {
            try {
                $fileEasynova = $this->fileService->deleteFileEasynova($node->getId());

                // need to check - because it hook call on all files
                if (!is_null($fileEasynova)) {
                    $this->logger->info('FileHooks >> fileDeleted | id = ' . $fileEasynova->id . ' | name = ' . $fileEasynova->fileName, ['app' => 'easynova']);
                    $this->sendRequestService->sendFileDeleted($fileEasynova);
                }
            } catch (Exception $e) {
                $this->logger->error('Error in fileDeleted hook call...');
            }
        } else {
            $this->logger->error('FileHooks >> fileDeleted | error: no found file with path = ' . $path, ['app' => 'easynova']);
        }
    }

    /**
     * Hook when file deleted by cron job of easynova app
     * 1. add mark deleted_at to files_easynova row DB
     * 2. send http request to Easynova backend
     * @param  {string} $fileId [id of file at nextcloud]
     * @return void
     */
    public function fileDeletedByCronJob($fileId)
    {
        try {
            $fileEasynova = $this->fileService->deleteFileEasynova($fileId);

            if (!is_null($fileEasynova)) {
                $this->logger->info('FileHooks >> fileDeletedByCronJob | id = ' . $fileEasynova->id, ['app' => 'easynova']);
                $this->sendRequestService->sendFileDeleted($fileEasynova);
            }
        } catch (Exception $e) {
            $this->logger->error('Error in fileDeletedByCronJob hook call...');
        }
    }
}
