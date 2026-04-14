<?php
define('FCPATH', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$controller = new \App\Controllers\VisitorReport();
try {
    $res = $controller->generate();
    echo $res->getBody();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
