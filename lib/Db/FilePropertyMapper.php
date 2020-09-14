<?php

namespace OCA\Easynova\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class FilePropertyMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'file_property', FileProperty::class);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Entity|FileProperty
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('file_property')
			->where($qb->expr()->eq('file_id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

		return $this->findEntity($qb);
	}

	public function findInfo(int $id) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('properties.name as property', 'file.value', 'file.created_at')
			->from('file_property', 'file')
			->leftJoin('file', 'custom_properties', 'properties', 'file.property_id = properties.id')
			->where($qb->expr()->eq('file_id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

		$cursor = $qb->execute();

		return $cursor->fetchAll();
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Entity|FileProperty
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByFileAndProp($fileId, $propertyId) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('file_property')
			->where($qb->expr()->eq('file_id', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('property_id', $qb->createNamedParameter($propertyId, IQueryBuilder::PARAM_INT)));

		return $this->findEntities($qb);
	}

	/**
	 * @param string $userId
	 * @return array
	 */
	public function findAll(): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('file_property');

		return $this->findEntities($qb);
	}
}
