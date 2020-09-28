<?php

namespace OCA\Easynova\Controller;

use OCP\IRequest;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Util;

use OCA\Easynova\Service\FileService;
use OCA\Easynova\Service\PropertyService;
use OCA\Easynova\Storage\EasynovaStorage;

 class PageController extends Controller {

    const FILE_PARAM = 'myfile';

    private $storage;

    /** @var IUserSession */
    private $userSession;

    /** @var OCP\IGroupManager */
    private $groupManager;

    public function __construct($appName,
                                IRequest $request,
                                IUserSession $userSession,
                                IGroupManager $groupManager,
                                EasynovaStorage $storage,
                                PropertyService $propertyService,
                                FileService $fileService) {
        $this->appName = $appName;
        $this->request = $request;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
        $this->storage = $storage;
        $this->propertyService = $propertyService;
        $this->fileService = $fileService;
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index() {
        // $properties = $this->propertyService->findAll();
        // $files = $this->fileService->findAll();
        // return new JSONResponse(['properties' => $properties, 'files' => $files]);
        
        $user = $this->userSession->getUser();
        if (is_null($user)) {
            throw new Exception("Not logged in");
        }

        // if ($this->groupManager->isAdmin($user->getUID())) {
        //     return new TemplateResponse($this->appName, 'admin');
        // }

        Util::addScript($this->appName, 'easynova-main');
        Util::addStyle($this->appName, 'style');

        return new TemplateResponse($this->appName, 'main');
    }
// 
    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function files() {
        

        
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
