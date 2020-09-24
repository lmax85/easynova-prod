<?php

namespace OCA\Easynova\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

use OCP\ILogger;

class SendRequestService {

    /** @var ILogger */
    private $logger;

    /** @var ClientService */
    private $httpClient;

    const API_URL_READ = 'https://wiki.siecom.de:8443/display/ENPUB/nextcloudDocumentRead';
    const USERNAME = 'easynova.partner';
    const PASSWORD = '2RQ9yk258pXq';

    public function __construct(ILogger $logger) {
        $this->logger = $logger;
    }

    public function sendFileReaded($file)
    {
        try {
            $url = self::API_URL_READ;
            $client = new Client();
            $response = $client->request('POST', $url, [
                'verify' => false,
                'auth' => [self::USERNAME, self::PASSWORD],
                'json' => [
                    // back end params
                    'documentId' => $file->fileId,
                    'userId' => $file->userId,
                    'opened' => true,
                    'timestamp' => $file->readedAt,
                    'ip' => $file->ip,
                ]
            ]);
            $this->logger->info('SendRequestService >> sendFileReaded called - request to backend succefull', ['app' => 'Easynova']);
        } catch (ClientException $e) {
            $this->logger->info('SendRequestService >> ClientException: ' . $e->getMessage(), ['app' => 'Easynova']);
        } catch (RequestException $e) {
            $this->logger->info('SendRequestService >> RequestException: ' . $e->getMessage(), ['app' => 'Easynova']);
        } catch (BadResponseException $e) {
            $this->logger->info('SendRequestService >> BadResponseException: ' . $e->getMessage(), ['app' => 'Easynova']);
        } catch (\Exception $e) {
            $this->logger->info('SendRequestService >> Exception: ' . $e->getMessage(), ['app' => 'Easynova']);
        }
    }

    public function sendFileDeleted($file)
    {
        // try {
        //     $url = self::API_URL_DELETED;
        //     $client = new Client();
        //     $response = $client->request('POST', $url, [
        //         'verify' => false,
        //         'auth' => [self::USERNAME, self::PASSWORD],
        //         'json' => [
        //             // back end params
        //             'documentId' => $file->fileId,
        //             'userId' => $file->userId,
        //             'deleted_at' => $file->deletedAt,
        //         ]
        //     ]);
        //     $this->logger->info('SendRequestService >> sendFileDeleted called - request to backend succefull', ['app' => 'Easynova']);
        // } catch (ClientException $e) {
        //     $this->logger->info('SendRequestService >> ClientException: ' . $e->getMessage(), ['app' => 'Easynova']);
        // } catch (RequestException $e) {
        //     $this->logger->info('SendRequestService >> RequestException: ' . $e->getMessage(), ['app' => 'Easynova']);
        // } catch (BadResponseException $e) {
        //     $this->logger->info('SendRequestService >> BadResponseException: ' . $e->getMessage(), ['app' => 'Easynova']);
        // } catch (\Exception $e) {
        //     $this->logger->info('SendRequestService >> Exception: ' . $e->getMessage(), ['app' => 'Easynova']);
        // }
    }
}
