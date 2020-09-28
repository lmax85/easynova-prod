<?php

namespace OCA\Easynova\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;

use OCA\Easynova\Service\PropertyService;

 class ApiCustomPropertyController extends Controller {

    const FILE_PARAM = 'myfile';

    private $storage;

 	public function __construct($appName,
                                IRequest $request,
                                PropertyService $service) {
        $this->appName = $appName;
        $this->request = $request;
        $this->service = $service;
 	}

    // var_dump($this->request);
    // var_dump($_FILES['myfile']);
    // $data['type'] = $this->request->getHeader('Content-Type');  // $_SERVER['HTTP_CONTENT_TYPE']
    // $data['cookie'] = $this->request->getCookie('myCookie');  // $_COOKIES['myCookie']
    // // $data['myfile'] = $_FILES['myfile']  // $_FILES['myfile']
    // $data['file'] = $this->request->getUploadedFile('myfile');  // $_FILES['myfile']
    // $data['env'] = $this->request->getEnv('SOME_VAR');  // $_ENV['SOME_VAR']

 	/**
 	 * @NoCSRFRequired
 	 */
 	public function createProperty() {
        try {
            $name = $this->request->getParam('name');

            if (!is_null($name)) {
                $create = $this->service->create($name);

                if ($create === 'exist') {
                    return new JSONResponse(['message' => 'property already exists.'], 200);
                }

                return new JSONResponse(['message' => 'property created.'], 200);
            }

            return new JSONResponse(['message' => 'nothing to create.'], 400);
        } catch (Exception $e) {
            return new JSONResponse(['message' => 'error when creating property...'], 500);
        }
 	}

    /**
     * @NoCSRFRequired
     */
    public function store()
    {
        $file = $this->request->getUploadedFile(self::FILE_PARAM);

        if (!is_null($file)) {
            $fileId = $this->storage->storeFile($file);

        }

        return new JSONResponse($data);
    }

    public function delete()
    {
        $data['storedId'] = $this->storage->delete($data['file']);
    }
 }
