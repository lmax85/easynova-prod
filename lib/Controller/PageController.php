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
                                PropertyService $propertyService,
                                FileService $fileService) {
        $this->appName = $appName;
        $this->request = $request;
        $this->storage = $storage;
        $this->propertyService = $propertyService;
        $this->fileService = $fileService;
    }

    /**
     * @NoCSRFRequired
     */
    public function index() {
        $properties = $this->propertyService->findAll();
        $files = $this->fileService->findAll();

        return new JSONResponse(['properties' => $properties, 'files' => $files]);
    }

    /**
     * @NoCSRFRequired
     */
    public function test()
    {
        $files = $this->fileService->getNotDeleteFiles();
        return new JSONResponse(['files' => $files]);
    }
 }
