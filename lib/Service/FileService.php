<?php

namespace OCA\Easynova\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Easynova\Db\CustomProperty;
use OCA\Easynova\Db\CustomPropertyMapper;
use OCA\Easynova\Db\FileProperty;
use OCA\Easynova\Db\FilePropertyMapper;
use OCA\Easynova\Storage\EasynovaStorage;

class FileService {

	/** @var CustomPropertyMapper */
	private $customPropertyMapper;

	/** @var FilePropertyMapper */
	private $filePropertyMapper;

	/** @var EasynovaStorage */
	private $storage;

	public function __construct(CustomPropertyMapper $customPropertyMapper, FilePropertyMapper $filePropertyMapper, EasynovaStorage $storage) {
		$this->customPropertyMapper = $customPropertyMapper;
		$this->filePropertyMapper = $filePropertyMapper;
		$this->storage = $storage;
	}

	public function findAll(): array {
		return $this->filePropertyMapper->findAll();
	}

	public function parse($fileId, $request)
	{
		$properties = $this->customPropertyMapper->findAll();

		foreach ($properties as $property) {
			if ($request->getParam($property->name)) {
				$value = $request->getParam($property->name);
				$this->create($fileId, $property->id, $value);
			}
		}
	}

	public function getInfo($fileId)
	{
		return $this->filePropertyMapper->findInfo($fileId);
	}

	public function create($fileId, $propertyId, $value) {
		$exist = $this->filePropertyMapper->findByFileAndProp($fileId, $propertyId);

		$fileProperty = count($exist) > 0
			? $exist[0]
			: new FileProperty();

		$now = new \DateTime();
		$fileProperty->setFileId($fileId);
		$fileProperty->setPropertyId($propertyId);
		$fileProperty->setValue($value);
		$fileProperty->setCreatedAt($now->format('Y-m-d H:i:s'));

		if (count($exist) > 0) {
			return $this->filePropertyMapper->update($fileProperty);
		}

		return $this->filePropertyMapper->insert($fileProperty);
	}

	public function delete($fileProperty) {
		try {
			$deleteFileFromStorage = $this->storage->delete($fileProperty['file_id']);

			if ($deleteFileFromStorage) {
				$fileProperty = $this->filePropertyMapper->find($fileProperty['id']);
				$this->filePropertyMapper->delete($fileProperty);
			}

			return $fileProperty;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
}
