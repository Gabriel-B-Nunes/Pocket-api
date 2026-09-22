<?php

namespace App\service\handler;

use App\model\Request;
use App\service\cache\Container;
use App\service\handler\AbstractHandler;
use App\service\translation\LangEnum;
use App\service\translation\Translator;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class TranslationHandler extends AbstractHandler {

    public function __construct(
        private AbstractHandler $nextHandler
    )
    {}

    public function handle(Request $request)
    {
        $acceptLanguageHeader = $request->getHeader()["Accept-Language"] ?? null;
        $translator = $acceptLanguageHeader ? new Translator(LangEnum::match($acceptLanguageHeader)) : new Translator();
        Container::set("translator", $translator);

        $this->nextHandler->handle($request);
    }
}