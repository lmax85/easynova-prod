<?php

namespace OCA\Easynova\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

use OCA\Easynova\Service\FileService;
use OCA\Easynova\Service\PropertyService;
use OCA\Easynova\Storage\EasynovaStorage;

 class PageController extends Controller {

    const FILE_PARAM = 'myfile';

    private $storage;

    public function __construct($appName,
                                IRequest $request,
                                EasynovaStorage $storage,
                                PropertyService $service,
                                FileService $fileService) {
        $this->appName = $appName;
        $this->request = $request;
        $this->storage = $storage;
        $this->service = $service;
        $this->fileService = $fileService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        $properties = $this->service->findAll();
        $files = $this->fileService->findAll();

        return new JSONResponse(['properties' => $properties, 'files' => $files]);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getFileInfo($fileId) {
        try {
            $file = $this->storage->get($fileId);
            $properties = $this->fileService->getInfo($fileId);

            return new JSONResponse([
                'id' => $fileId,
                'name' => $file->getName(),
                'properties' => $properties,
            ], 200);
        } catch (NotFoundException $e) {
            return new JSONResponse(['error' => $e->getMessage(), 'message' => 'error when obtain file info.'], 500);
        } catch (Exception $e) {
            return new JSONResponse(['error' => $e->getMessage(), 'message' => 'error when obtain file info.'], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function store()
    {
        $file = $this->request->getUploadedFile(self::FILE_PARAM);

        if (!is_null($file)) {
            try {
                $fileId = $this->storage->store($file);
                // insert time_to_live to DB table file_property
                $this->fileService->parse($fileId, $this->request);

                return new JSONResponse(['message' => 'file stored and properties added.', 'file_id' => $fileId], 200);
            } catch (NotPermittedException $e) {
                return new JSONResponse(['error' => $e->getMessage(), 'message' => 'Haven\'t permissions for store file.'], 500);
            } catch (Exception $e) {
                return new JSONResponse(['error' => $e->getMessage(), 'message' => 'Something went wrong while storing file.'], 500);
            }
        }

        return new JSONResponse(['error' => 'no file', 'message' => 'No file in request.'], 500);
    }

    public function delete()
    {
        $data['storedId'] = $this->storage->delete($data['file']);
    }
 }
