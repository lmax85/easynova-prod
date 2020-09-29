<?php

namespace OCA\Easynova\Controller;

use OCP\IRequest;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

use OCA\Easynova\Service\FileService;
use OCA\Easynova\Storage\EasynovaStorage;
use OCA\Easynova\Hooks\FileHooksStatic;

 class ApiUserFileController extends Controller {

    const FILE_PARAM = 'myfile';

    private $storage;

    public function __construct($appName,
                                IRequest $request,
                                IUserSession $userSession,
                                IGroupManager $groupManager,
                                EasynovaStorage $storage,
                                FileService $fileService) {
        $this->appName = $appName;
        $this->request = $request;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
        $this->storage = $storage;
        $this->fileService = $fileService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getFiles()
    {
        try {
            $user = $this->userSession->getUser();

            if (is_null($user)) {
                throw new Exception("Not logged in");
            }

            $userId = $user->getUID();
            $files = $this->fileService->getEasynovaFilesByUserId($userId);

            return new JSONResponse(['files' => $files], 200);
        } catch (Exception $e) {
            return new JSONResponse(['error' => $e->getMessage, 'message' => 'Can\'t obtain files for user'], 200);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function updateFile($file_id, $paper_flag)
    {
        try {
            $user = $this->userSession->getUser();

            if (is_null($user)) {
                throw new Exception("Not logged in");
            }

            $userId = $user->getUID();
            $file = $this->fileService->updateEasynovaFileField($file_id, $userId, $paper_flag);
            FileHooksStatic::fileUpdatedByUser($file);

            return new JSONResponse(['success' => true, 'message' => 'file updated successfully'], 200);
        } catch (Exception $e) {
            return new JSONResponse(['error' => $e->getMessage, 'message' => 'Can\'t obtain files for user'], 200);
        }
    }
 }
