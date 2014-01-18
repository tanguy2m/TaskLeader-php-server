<?php

/**
 * Require dependencies
 */
require '../vendor/autoload.php'; // Composer auto-load
require 'bo.php';
require 'conf.inc.php';

/**
 * Instantiate applications
 */
$app = new \Slim\Slim();
$swagger = new \Swagger\Swagger(realpath(dirname(__FILE__)));

/**
 * Proxy pour API server distant
 */
 class CustomMiddleware extends \Slim\Middleware
{
    public function call()
    {
		// CORS and JSON output
		$this->app->response->header('Access-Control-Allow-Origin', 'http://swagger.wordnik.com'); //TODO: à adapter
		$this->app->response->header('Content-Type', 'application/json; charset=UTF-8');
		
		// Proxy pour API server distant
		global $apiServers;
		$remote = $this->app->request->get('remote');
		if($remote)
			if(array_key_exists($remote,$apiServers))
				echo file_get_contents($apiServers[$remote]->url.$this->app->environment['PATH_INFO']);
			else {
				$this->app->response->status(422);
				echo json_encode(array(
					"message" => "Validation failed",
					"errors" => array(
						array(
							"resource" => "remotes",
							"field" => "name",
							"code" => "missing"
						)
					)
				));
			}
		else
			$this->next->call(); // Run inner middleware and application
    }
}
$app->add(new \CustomMiddleware());

/**
 * Description JSON Swagger-compliant de l'API
 */
 
$app->get( '/api-docs', function() use($swagger) {	
	echo $swagger->getResourceList(array(
		'apiVersion' => '0.1beta',
		'output' => 'json'
	));
});
$app->get( '/api-docs/:method', function($method) use($swagger) {	
	echo $swagger->getResource(
		$method,
		array(
			'defaultBasePath' => "http://$_SERVER[HTTP_HOST]".dirname(dirname($_SERVER['REQUEST_URI'])), // Suppression de /api-docs/:method
			'output' => 'json'
		)
	);
});

/**
 *	@SWG\Resource(
 *		resourcePath="remotes",
 *		description="opérations sur les serveurs d'API"
 *	)
 */
 
/**
 *	@SWG\Api(
 *		path="/remotes",
 *		@SWG\Operation(
 *			method="GET", summary="Retourne les serveurs d'API gérés par le serveur principal",
 *			nickname="getRemotes",notes="retourne un Array d'objets APIserver", type="array",@SWG\Items("APIserver")
 *		)
 *	)
 */
$app->get('/remotes',function () {
	global $apiServers;
	echo json_encode($apiServers);
});

/**
 *	@SWG\Resource(
 *		resourcePath="databases",
 *		description="opérations sur les bases d'actions gérées"
 *	)
 */
 
/**
 *	@SWG\Api(
 *		path="/databases",
 *		@SWG\Operation(
 *			method="GET", summary="Retourne les noms des bases d'actions gérées",
 *			nickname="getActivesDatabases",type="array",@SWG\Items("string"),
 *			@SWG\Parameters(
 *				@SWG\Parameter(
 *					name="remote",
 *					description="Nom du serveur d'API remote",
 *					paramType="query",
 *					required=false,
 *					type="string"	
 *				)
 *			),
 *			@SWG\ResponseMessages(
 *				@SWG\ResponseMessage(code=422, message="Validation failed / Unknown remote")
 *			)
 *		)
 *	)
 */
$app->get('/databases',function () use ($app) {
	global $dbs;
	echo json_encode($dbs);
});

/**
 *	@SWG\Api(
 *		path="/databases/default",
 *		@SWG\Operation(
 *			method="GET", summary="Retourne le nom de la base par défaut",
 *			nickname="getDefaultDB",type="string",
 *			@SWG\Parameters(
 *				@SWG\Parameter(
 *					name="remote",
 *					description="Nom du serveur d'API remote",
 *					paramType="query",
 *					required=false,
 *					type="string"	
 *				)
 *			),
 *			@SWG\ResponseMessages(
 *				@SWG\ResponseMessage(code=422, message="Validation failed / Unknown remote")
 *			)
 *		)
 *	)
 */
$app->get('/databases/default',function () {
	global $dbs;
	echo json_encode($dbs[0]);
});

/**
 * @SWG\Resource(
 *     resourcePath="entities",
 * 	   description="opérations sur les entités des bases"
 * )
 */
 
/**
 * @SWG\Api(
 *	 path="/{dbName}/entities",
 *	 @SWG\Operation(
 * 		method="GET", summary="Retourne les entités gérées par une base",
 * 		nickname="getDBentities",notes="Renvoie un tableau de DBEntity",type="array",@SWG\Items("DBEntity"),
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			)
 *		)
 * 	 )
 * )
 */
$app->get('/:dbName/entities',function ($dbName) {
	$result = array();
	$result[] = new DBEntity(1,"Contextes");
	$result[] = new DBEntity(2,"Destinataires");
	$result[] = new DBEntity(3,"Statuts");
	echo json_encode($result);
});

/**
 * @SWG\Api(
 *	 path="/{dbName}/entities",
 *	 @SWG\Operation(
 * 		method="POST", summary="Crée une nouvelle entité dans une base",
 * 		nickname="createDBentity",
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			),
 *			@SWG\Parameter(
 *				name="entity",
 *				description="DBEntity à ajouter à la base",
 *				paramType="body",
 *				required=true,
 *				type="DBEntity"		
 *			)
 *		)
 * 	 )
 * )
 */
$app->post('/:dbName/entities',function ($dbName) {

});

/**
 * @SWG\Api(
 *	 path="/{dbName}/entities/list",
 *	 @SWG\Operation(
 * 		method="GET", summary="Retourne les entités de type List gérées par une base",
 * 		nickname="getDBListentities",notes="Renvoie un tableau de DBEntity",type="array",@SWG\Items("DBEntity"),
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			)
 *		)
 * 	 )
 * )
 */
$app->get('/:dbName/entities/list',function ($dbName) {
	$result = array();
	$result[] = new DBEntity(1,"Contextes");
	$result[] = new DBEntity(2,"Destinataires");
	$result[] = new DBEntity(3,"Statuts");
	echo json_encode($result);
});

/**
 * @SWG\Api(
 *	 path="/{dbName}/entities/list/{id}/values",
 *	 @SWG\Operation(
 * 		method="GET", summary="Retourne les entités de type List gérées par une base",
 * 		nickname="getDBentityValues",notes="Renvoie un tableau de ListValue",type="array",@SWG\Items("ListValue"),
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			),
 *			@SWG\Parameter(
 *				name="id",
 *				description="id en base de l'entité",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			) 
 *		)
 * 	 )
 * )
 */
 $app->get('/:dbName/entities/list/:id/values',function ($dbName) {

});

/**
 * @SWG\Resource(
 *     resourcePath="filtres",
 * 	   description="opérations sur les filtres des bases"
 * )
 */

 /**
 * @SWG\Api(
 *	 path="/{dbName}/filtres",
 *	 @SWG\Operation(
 * 		method="GET", summary="Retourne les filtres enregistrés dans une base",
 * 		nickname="getFilters",notes="Renvoie un tableau de Filtre",type="array",@SWG\Items("Filtre"),
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			)
 *		)
 * 	 )
 * )
 */
 
 /**
 * @SWG\Resource(
 *     resourcePath="actions",
 * 	   description="opérations sur les actions des bases"
 * )
 */

 /**
 * @SWG\Api(
 *	 path="/actions",
 *	 @SWG\Operation(
 * 		method="GET", summary="Retourne les actions stockées en base",
 * 		nickname="getFilters",notes="Renvoie un tableau de Filtre",type="array",@SWG\Items("Filtre"),
 *		@SWG\Parameters(
 *			@SWG\Parameter(
 *				name="dbName",
 *				description="Nom de la base contenant les entités",
 *				paramType="path",
 *				required=true,
 *				type="string"		
 *			)
 *		)
 * 	 )
 * )
 */
 
 /**
 * Step 4: Run the Slim application
 */
$app->run();
