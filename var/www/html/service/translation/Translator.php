<?php

namespace App\service\translation;

use App\service\translation\LangEnum;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class Translator {
    private array $messages;

    public function __construct(
        private LangEnum $lang = LangEnum::EN_US
    )
    {}

    public function translate(string $message): string{
        if ($this->lang === LangEnum::EN_US) {
            return $message;
        }

        self::loadTranslation($this->lang);

        $translation = $this->messages[$message] ?? "";

        if ($translation) {
            return $translation;
        }

        return $message;
    }

    private function loadTranslation(LangEnum $lang) {
        $dir = __DIR__ . "/lang/{$lang->name}.php";

        if (file_exists($dir)) {
            $this->messages = require $dir;
        }
    }
}