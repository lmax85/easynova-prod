<?php

namespace OCA\Easynova\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CustomProperty extends Entity implements JsonSerializable {
	public $id;
	public $name;
	public $type;
	public $default;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->type,
			'default' => $this->default
		];
	}
}
