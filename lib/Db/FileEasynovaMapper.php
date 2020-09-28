<?php

namespace OCA\Easynova\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class FileEasynovaMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'files_easynova', FileEasynova::class);
    }

    /**
     * @param int $id
     * @param string $userId
     * @return Entity|FileProperty
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws DoesNotExistException
     */
    public function find(int $id) {
        try {
            /* @var $qb IQueryBuilder */
            $qb = $this->db->getQueryBuilder();
            $qb->select('*')
                ->from('files_easynova')
                ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

            return $this->findOneQuery($qb);
        } catch (Exception $e) {
            throw new Exception("File not found", 1);
        }
        
    }

    /**
     * @param array $params
     * @return Entity|FileEasynova|Null
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws DoesNotExistException
     */
    public function findByAttributes(array $params) {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('files_easynova');

        foreach ($params as $key => $value) {
            $qb->andWhere($qb->expr()->eq($key, $qb->createNamedParameter($value)));
        }

        return $this->findEntities($qb);
    }

    /**
     * get not deleted files with time_to_live prop for cron job
     * [
     *     [
     *     "user_id": "test",
     *     "file_id": "890",
     *     "created_at": "2020-09-16 08:42:01",
     *     "readed_at": null,
     *     "property_id": "41",
     *     "property_value": "1|hours",
     *     "property_name": "time_to_live"]
     * ]
     * @return {array}
     */
    public function findNotDeleteFiles(): array
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('files.user_id as user_id',
                    'files.id as id',
                    'files.file_id as file_id',
                    'files.created_at',
                    'files.readed_at',
                    'file_properties.id as property_id',
                    'file_properties.value as property_value',
                    'properties.name as property_name')
            ->from('files_easynova', 'files')
            ->join('files', 'file_property', 'file_properties', 'files.id = file_properties.file_id')
            ->join('file_properties', 'custom_properties', 'properties', 'properties.id = file_properties.property_id AND properties.name = "time_to_live"')
            ->where('files.deleted_at is NULL');

        $cursor = $qb->execute();

        return $cursor->fetchAll();
    }

    /**
     * @return array
     */
    public function findAll(): array {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('files_easynova');

        return $this->findEntities($qb);
    }

    /**
     * @return array
     */
    public function getUserFiles($userId): array {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('files_easynova')
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)));

        return $this->findEntities($qb);
    }
}
