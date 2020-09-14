<?php

namespace OCA\Easynova\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class FileProperty extends Entity implements JsonSerializable {
	public $id;
	public $fileId;
	public $propertyId;
	public $value;
	public $createdAt;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'file_id' => $this->fileId,
			'property_id' => $this->propertyId,
			'value' => $this->value,
			'created_at' => $this->createdAt,
		];
	}
}
