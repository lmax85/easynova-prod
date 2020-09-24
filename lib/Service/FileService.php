<?php

namespace OCA\Easynova\Service;

use Exception;

use OCP\IUserSession;
use OCP\ILogger;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Easynova\Db\FileEasynova;
use OCA\Easynova\Db\FileEasynovaMapper;
use OCA\Easynova\Db\CustomProperty;
use OCA\Easynova\Db\CustomPropertyMapper;
use OCA\Easynova\Db\FileProperty;
use OCA\Easynova\Db\FilePropertyMapper;
use OCA\Easynova\Storage\EasynovaStorage;


class FileService {

	/** @var IUserSession */
	private $userSession;

	/** @var ILogger */
	private $logger;

	/** @var CustomPropertyMapper */
	private $customPropertyMapper;

	/** @var FilePropertyMapper */
	private $filePropertyMapper;

	/** @var EasynovaStorage */
	private $storage;

	public function __construct(IUserSession $userSession,
			ILogger $logger,
			CustomPropertyMapper $customPropertyMapper,
			FileEasynovaMapper $fileEasynovaMapper,
			FilePropertyMapper $filePropertyMapper,
			EasynovaStorage $storage) {
		$this->userSession = $userSession;
		$this->logger = $logger;
		$this->customPropertyMapper = $customPropertyMapper;
		$this->fileEasynovaMapper = $fileEasynovaMapper;
		$this->filePropertyMapper = $filePropertyMapper;
		$this->storage = $storage;
	}

	public function findAll(): array {
		return $this->filePropertyMapper->findAll();
	}

	public function parse($fileEasynovaId, $request)
	{
		$properties = $this->customPropertyMapper->findAll();

		foreach ($properties as $property) {
			if ($request->getParam($property->name)) {
				$value = $request->getParam($property->name);
				$this->create($fileEasynovaId, $property->id, $value);
			}
		}
	}

	public function getInfo($fileId)
	{
		return $this->filePropertyMapper->findInfo($fileId);
	}

	public function getEasynovaInfo($fileId)
	{
		return $this->fileEasynovaMapper->find($fileId);
	}

	public function create($fileEasynovaId, $propertyId, $value) {
		$exist = $this->filePropertyMapper->findByFileAndProp($fileEasynovaId, $propertyId);

		$fileProperty = count($exist) > 0
			? $exist[0]
			: new FileProperty();

		$now = new \DateTime();
		$fileProperty->setFileId($fileEasynovaId);
		$fileProperty->setPropertyId($propertyId);
		$fileProperty->setValue($value);
		$fileProperty->setCreatedAt($now->format('Y-m-d H:i:s'));

		if (count($exist) > 0) {
			return $this->filePropertyMapper->update($fileProperty);
		}

		return $this->filePropertyMapper->insert($fileProperty);
	}

	public function saveFileEasynova($fileId, $userId, $fileName)
	{
		$exist = $this->fileEasynovaMapper->findByAttributes([
			'file_id' => $fileId,
			'user_id' => $userId,
		]);

		$fileEasnynova = count($exist) > 0
			? $exist[0]
			: new FileEasynova();

		$now = new \DateTime();
		$fileEasnynova->setFileId($fileId);
		$fileEasnynova->setFileName($fileName);
		$fileEasnynova->setUserId($userId);
		$fileEasnynova->setCreatedAt($now->format('Y-m-d H:i:s'));

		if (count($exist) > 0) {
			return $this->fileEasynovaMapper->update($fileEasnynova);
		}

		return $this->fileEasynovaMapper->insert($fileEasnynova);
	}

	public function readFileEasynova($fileId)
	{
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			return null;
		}
		$userId = $user->getUID();

		$exist = $this->fileEasynovaMapper->findByAttributes([
			'file_id' => $fileId,
			'user_id' => $userId
		]);

		if (count($exist) > 0) {
			$fileEasnynova = $exist[0];

			if ($fileEasnynova->readedAt === null) {
				$ip = $this->getIp();
				$now = new \DateTime();
				$fileEasnynova->setReadedAt($now->format('Y-m-d H:i:s'));
				$fileEasnynova->setIp($ip);

				return $this->fileEasynovaMapper->update($fileEasnynova);
			} else {
				return null;
			}
		}

		return null;
	}

	public function deleteFileEasynova($fileId)
	{
		$user = $this->userSession->getUser();

		if (is_null($user)) {
			return null;
		}

		$userId = $user->getUID();

		$exist = $this->fileEasynovaMapper->findByAttributes([
			'file_id' => $fileId,
			'user_id' => $userId
		]);

		if (count($exist) > 0) {
			$fileEasnynova = $exist[0];

			if ($fileEasnynova->deletedAt === null) {
				$now = new \DateTime();
				$fileEasnynova->setDeletedAt($now->format('Y-m-d H:i:s'));

				return $this->fileEasynovaMapper->update($fileEasnynova);
			} else {
				return null;
			}
		}

		return null;
	}

	public function getNotDeleteFiles()
	{
		return $this->fileEasynovaMapper->findNotDeleteFiles();
	}

	public function delete($fileEasnynova) {
		try {
			$deleteFileFromStorage = $this->storage->delete($fileEasnynova);

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	private function getIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'ip not found';
	}
}
