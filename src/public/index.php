<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;


require __DIR__ . '/../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "root";
$config['db']['dbname'] = "imageupload";


$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

// $container['view'] = new \Slim\Views\PhpRenderer("../templates/");

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    // $pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/images/', function (Request $request, Response $response) {
    $this->logger->addInfo("Images list");
    $mapper = new ImageMapper($this->db);

    $lat = (float)$request->getParam('lat');
    $lon = (float)$request->getParam('lon');

    $images = $mapper->getSortedImages($lat, $lon);

    return $response->withJson($images, 201);
});

$app->post('/image/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $image_data = [];
    $image_data['image_desc'] = filter_var($data['image_desc'], FILTER_SANITIZE_STRING);
    $location_data['location_desc'] = filter_var($data['location_desc'], FILTER_SANITIZE_STRING);
    $location_data['lat'] = (float)$data['lat'];
    $location_data['lon'] = (float)$data['lon'];

    $location = new LocationEntity($location_data);
    $location_mapper = new LocationMapper($this->db);
    $location_mapper->save($location);
    $image_data['location_id'] = $location->getId();

    $directory = $this->get('upload_directory');

    $uploadedFiles = $request->getUploadedFiles();

    $uploadedFile = $uploadedFiles['image_file'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);
        $image_data['image_path'] = filter_var($filename, FILTER_SANITIZE_STRING);
        $response->write('Uploaded ' . $filename . '<br/>');
        $this->logger->addInfo("Image path: " . $filename);
    }

    $image = new ImageEntity($image_data);
    $image_mapper = new ImageMapper($this->db);
    $image_mapper->save($image);
    return $response;
});

$app->get('/image/{id}', function (Request $request, Response $response, $args) {
    $image_id = (int)$args['id'];
    $mapper = new ImageMapper($this->db);
    $image = $mapper->getImageById($image_id);
    
    return $response->withJson($image, 201);
});

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @param UploadedFile $uploaded file uploaded file to move
 * @return string filename of moved file
 */
function moveUploadedFile($directory, UploadedFile $uploadedFile) {
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

$app->run();

?>