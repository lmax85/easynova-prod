<?php
namespace OCA\Easynova\AppInfo;

use \OCP\AppFramework\App;

use \OCA\Easynova\Controller\PageController;
use \OCA\Easynova\Storage\EasynovaStorage;



class Application extends App {

    public function __construct(array $urlParams=array()){
        parent::__construct('easynova', $urlParams);

        $container = $this->getContainer();

        // // var_dump(new Storage());
        // // var_dump('app.php');

        // /**
        //  * Controllers
        //  */
        // $container->registerService('PageController', function($c){
        //   return new PageController(
        //     $c->query('AppName'),
        //     $c->query('Request'),
        //     $c->query('EasynovaStorage')
        //   );
        // });

        // var_dump(new PageController());

        /**
         * Storage Layer
         */
        // $container->registerService('EasynovaStorage', function($c) {
        //     // var_dump($c->query('ServerContainer')->getRootFolder());
        //     return new EasynovaStorage($c->query('RootStorage'));
        // });

        // $container->registerService('RootStorage', function($c) {
        //     return $c->query('ServerContainer')->getRootFolder();
        // });

    }
}