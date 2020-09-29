<?php

namespace OCA\Easynova\Hooks;

/**
 * The class to handle the filesystem hooks
 */
class FileHooksStatic {

    /**
     * @return FilesHooks
     */
    static protected function getHooks() {
        return \OC::$server->query(FileHooks::class);
    }

    /**
     * Store the read hook events
     * @param array $params The hook params
     */
    public static function fileReaded($params) {
        self::getHooks()->fileReaded($params['path']);
    }

    /**
     * Store the delete hook events
     * @param array $params The hook params
     */
    public static function fileDeleted($params) {
        self::getHooks()->fileDeleted($params['path']);
    }

    /**
     * Store the delete from easynova app cron job hook events
     * @param array $params The hook params
     */
    public static function fileDeletedByCronJob($fileId) {
        self::getHooks()->fileDeletedByCronJob($fileId);
    }

    /**
     * Update file from user hook events
     * @param {FileEasynova} $file
     */
    public static function fileUpdatedByUser($file) {
        self::getHooks()->fileUpdatedByUser($file);
    }
}
