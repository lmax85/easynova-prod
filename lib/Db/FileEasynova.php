<?php

namespace OCA\Easynova\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class FileEasynova extends Entity implements JsonSerializable {
	public $id;
	public $fileId;
	public $fileName;
	public $userId;
	public $ip;
	public $userAgent;
	public $readedAt;
	public $createdAt;
	public $deletedAt;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'file_id' => $this->fileId,
			'file_name' => $this->fileName,
			'user_id' => $this->userId,
			'ip' => $this->ip,
			'user_agent' => $this->userAgent,
			'readed_at' => $this->readedAt,
			'created_at' => $this->createdAt,
			'deleted_at' => $this->deletedAt,
		];
	}
}
