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

    const API_URL = 'https://jsonplaceholder.typicode.com';

    public function __construct(ILogger $logger) {
        $this->logger = $logger;
    }

    public function sendFileReaded($file)
    {
        try {
            $url = self::API_URL . '/posts';
            $client = new Client();
            $response = $client->request('POST', $url, [
                'json' => [
                    // back end params
                    'file_id' => $file->fileId,
                    'opened' => true,
                    'opened_at' => $file->readedAt,
                    'ip' => $file->ip,

                    // test params for fake api
                    'title' => $file->fileId,
                    'body' => 'ip = ' . $file->ip . ', readed_at = ' . $file->readedAt,
                    'userId' => 123
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
        try {
            $url = self::API_URL . '/posts';
            $client = new Client();
            $response = $client->request('POST', $url, [
                'json' => [
                    // back end params
                    'file_id' => $file->fileId,
                    'deleted' => true,
                    'deleted_at' => $file->deletedAt,

                    // test params for fake api
                    'title' => $file->fileId,
                    'body' => 'ip = ' . $file->ip . ', readed_at = ' . $file->deletedAt,
                    'userId' => 123
                ]
            ]);
            $this->logger->info('SendRequestService >> sendFileDeleted called - request to backend succefull', ['app' => 'Easynova']);
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
}
