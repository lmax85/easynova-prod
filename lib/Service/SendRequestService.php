<?php

namespace OCA\Easynova\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Middleware;

use OCP\ILogger;

class SendRequestService {

    /** @var ILogger */
    private $logger;

    /** @var ClientService */
    private $httpClient;

    const API_URL_READ = 'https://develop.easynova.de:9443/CoreService/CoreWebService.svc/nextcloudDocumentRead';
    const USERNAME = 'nextcloud';
    const PASSWORD = 'SFVeaTaEqS3hN8cCth0uncRt2c2mBTGPxncXPPsVVCs=';

    public function __construct(ILogger $logger) {
        $this->logger = $logger;
    }

    public function sendFileReaded($file)
    {
        try {
            $url = self::API_URL_READ;
            $client = new Client([
                'verify' => false,
                'headers' => [ 'Content-Type' => 'application/json' ],
                'auth' => [self::USERNAME, self::PASSWORD]
            ]);

            // // Grab the client's handler instance.
            // $clientHandler = $client->getConfig('handler');
            // $log = $this->logger;
            // // Create a middleware that echoes parts of the request.
            // $tapMiddleware = Middleware::tap(function ($request) use ($log) {
            //     $log->info('Content-Type - ' . $request->getHeaderLine('Content-Type'));
            //     // application/json
            //     $log->info('request body - ' . $request->getBody());
            //     // {"foo":"bar"}
            // });

            $response = $client->request('POST', $url, [
                'json'    => [
                    'documentId' => $file->id,
                    'timestamp' => "/Date(" . strtotime($file->readedAt) . ")/", // /Date(1599473334000+0200)/
                    'ipAddress' => $file->ip
                ],
                // 'handler' => $tapMiddleware($clientHandler)
            ]);
            $body = $response->getBody();
            $this->logger->info('SendRequestService >> sendFileReaded called - request to backend succefull', ['app' => 'Easynova']);
            $this->logger->info('url: ' . $url);
            $this->logger->info('status: ' . $response->getStatusCode());
            $this->logger->info('body: ' . $body);
            $this->logger->info('======================================================');
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
