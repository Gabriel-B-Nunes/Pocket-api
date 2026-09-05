<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\exception\ValidationException;
use App\model\Request;
use App\service\Dispatcher;

$request = new Request();
$requestModule = $request->getModules()[1] ?? null;

switch ($requestModule) {
    case "api":
        $dispatcher = new Dispatcher();
        try {
            $dispatcher->dispatch($request);
        } catch (ValidationException $e) {
            $errors["Errors"] = $e->getErrors();

            $response = [
                "Success" => false,
                "Code" => $e->getCode(),
                "Message" => $e->getMessage()
            ];

            return array_merge($response, $errors);
        } catch (Exception $e) {
            $response = [
                "Success" => false,
                "Code" => 500,
                "Message" => "Internal server error"
            ];

            return $response;
        }
        break;
}
