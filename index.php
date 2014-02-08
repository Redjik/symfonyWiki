<?php
/**
 * @author Ivan Matveev <Redjiks@gmail.com>.
 */

session_cache_limiter('public');

use Symfony\Component\HttpFoundation\Request;


$loader = require_once __DIR__.'/app/autoload.php';
require_once __DIR__.'/app/AppKernel.php';

$kernel = new AppKernel('prod', false);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);