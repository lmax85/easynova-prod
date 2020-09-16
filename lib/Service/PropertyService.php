<?php

namespace OCA\Easynova\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Easynova\Db\CustomProperty;
use OCA\Easynova\Db\CustomPropertyMapper;
use OCA\Easynova\Db\FileProperty;
use OCA\Easynova\Db\FilePropertyMapper;

class PropertyService {

	/** @var CustomPropertyMapper */
	private $customPropertyMapper;

	/** @var FilePropertyMapper */
	private $filePropertyMapper;

	public function __construct(CustomPropertyMapper $customPropertyMapper, FilePropertyMapper $filePropertyMapper) {
		$this->customPropertyMapper = $customPropertyMapper;
		$this->filePropertyMapper = $filePropertyMapper;
	}

	public function findAll(): array {
		return $this->customPropertyMapper->findAll();
	}

	// public function find($id, $userId) {
	// 	try {
	// 		return $this->mapper->find($id, $userId);

	// 		// in order to be able to plug in different storage backends like files
	// 	// for instance it is a good idea to turn storage related exceptions
	// 	// into service related exceptions so controllers and service users
	// 	// have to deal with only one type of exception
	// 	} catch (Exception $e) {
	// 		$this->handleException($e);
	// 	}
	// }

	public function create($name) {
		$existName = $this->customPropertyMapper->findByName($name);

		if (count($existName) === 0) {
			$property = new CustomProperty();
			$property->setName($name);

			return $this->customPropertyMapper->insert($property);
		}

		return 'exist';
	}

	// public function update($id, $title, $content, $userId) {
	// 	try {
	// 		$note = $this->mapper->find($id, $userId);
	// 		$note->setTitle($title);
	// 		$note->setContent($content);
	// 		return $this->mapper->update($note);
	// 	} catch (Exception $e) {
	// 		$this->handleException($e);
	// 	}
	// }

	// public function delete($id, $userId) {
	// 	try {
	// 		$note = $this->mapper->find($id, $userId);
	// 		$this->mapper->delete($note);
	// 		return $note;
	// 	} catch (Exception $e) {
	// 		$this->handleException($e);
	// 	}
	// }
}
