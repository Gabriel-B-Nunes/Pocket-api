<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\exception\BadRequestException;
use App\exception\NotFoundException;
use App\exception\ValidationException;
use App\model\Request;
use App\service\Dispatcher;

header('Content-Type: application/json');

$request = new Request();

if ($request->getMethod() === "POST" && json_last_error() !== JSON_ERROR_NONE) {
    $e = new BadRequestException();

    $response = [
        "Success" => false,
        "Code" => $e->getCode(),
        "Message" => $e->getMessage(),
        "Error" => json_last_error_msg()
    ];

    http_response_code($e->getCode());
    echo json_encode($response);
} else {
    $requestModule = $request->getModules()[1] ?? null;

    switch ($requestModule) {
        case "api":
            $dispatcher = new Dispatcher();

            try {
                echo $dispatcher->dispatch($request);
            } catch (ValidationException $e) {
                $errors["Errors"] = $e->getErrors();

                $response = [
                    "Success" => false,
                    "Code" => $e->getCode(),
                    "Message" => $e->getMessage()
                ];

                http_response_code($e->getCode());
                echo json_encode(array_merge($response, $errors));
            } catch (Exception $e) {
                $response = [
                    "Success" => false,
                    "Code" => $e->getCode(),
                    "Message" => $e->getMessage()
                ];

                http_response_code($e->getCode());
                echo json_encode($response);
            }
            break;
        
        default:
            $e = new NotFoundException();
            $response = [
                    "Success" => false,
                    "Code" => $e->getCode(),
                    "Message" => $e->getMessage()
                ];

                http_response_code($e->getCode());
                echo json_encode($response);
    }
}
