<?php

return [
	'resources' => [
		'easynova' => ['url' => '/easynova'],
		'easynova_api' => ['url' => '/api/0.1/notes']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'page#getFileInfo', 'url' => '/file/{fileId}', 'verb' => 'GET'],
		['name' => 'page#store', 'url' => '/', 'verb' => 'POST'],
		['name' => 'apiCustomProperty#createProperty', 'url' => '/property/create', 'verb' => 'POST'],
		['name' => 'easynova_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']
		]
	]
];
