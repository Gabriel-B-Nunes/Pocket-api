<?php

namespace App\service\request\handler;

use App\model\Cache;
use App\model\Request;
use App\service\request\handler\AbstractHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ResponseHandler extends AbstractHandler {
    public function __construct(
        private AbstractHandler $nextHandler
    )
    {}

    public function handle(Request $request): void
    {
        $response = $this->nextHandler->handle($request);

        if (ob_get_length()) {
            ob_clean();
        }

        header("Content-Type: application/json; charset=utf-8");
        http_response_code($response->getCode());

        $cache = $response->getCache();
        header("Cache-Control: {$cache->value}");

        if ($cache === Cache::PRIVATE_NO_CACHE) {
            header("Pragma: no-cache");
            header("Expires: 0");
        }

        echo $response->toJson();
    }
}