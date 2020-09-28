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
            $files = $this->fileService->updateEasynovaFileField($file_id, $userId, $paper_flag);

            return new JSONResponse(['success' => true, 'message' => 'file updated successfully'], 200);
        } catch (Exception $e) {
            return new JSONResponse(['error' => $e->getMessage, 'message' => 'Can\'t obtain files for user'], 200);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getFileInfo($fileId) {
        try {
            $info = $this->fileService->getEasynovaInfo($fileId);
            $info['properties'] = $this->fileService->getInfo($fileId);

            return new JSONResponse($info, 200);
        } catch (NotFoundException $e) {
            return new JSONResponse(['error' => $e->getMessage(), 'message' => 'error when obtain file info.'], 500);
        } catch (Exception $e) {
            return new JSONResponse(['error' => $e->getMessage(), 'message' => 'error when obtain file info.'], 500);
        }
    }

    /**
     * @NoCSRFRequired
     */
    public function store()
    {
        $file = $this->request->getUploadedFile(self::FILE_PARAM);
        $userId = $this->request->getParam('user_id');

        if (!is_null($file) && !is_null($userId)) {
            try {
                $fileId = $this->storage->store($file, $userId);
                // save main easynova file info
                $fileEasynova = $this->fileService->saveFileEasynova($fileId, $userId, $file['name']);
                // insert time_to_live to DB table file_property
                $this->fileService->parse($fileEasynova->id, $this->request);

                return new JSONResponse(['message' => 'file stored and properties added.', 'file_id' => $fileEasynova->id], 200);
            } catch (NotPermittedException $e) {
                return new JSONResponse(['error' => $e->getMessage(), 'message' => 'Haven\'t permissions for store file.'], 500);
            } catch (Exception $e) {
                return new JSONResponse(['error' => $e->getMessage(), 'message' => 'Something went wrong while storing file.'], 500);
            }
        }

        return new JSONResponse(['error' => 'no file or user id in request', 'message' => 'No file or user in request.'], 400);
    }

    /**
     * @NoCSRFRequired
     */
    public function delete()
    {
        $data['storedId'] = $this->storage->delete($data['file']);
    }
 }
