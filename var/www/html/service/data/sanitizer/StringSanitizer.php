<?php

namespace App\service\data\sanitizer;

use Normalizer;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] 
class StringSanitizer implements SanitizerInterface {

    public function __construct(
        private int $normalizationForm = Normalizer::FORM_C,
        private bool $trim = true,
        private bool $removeAccents = false,
        private bool $lowerCase = false,
        private bool $upperCase = false,
        private bool $removeAlphanumerics = false,
        private bool $removeBlank = false,
        private bool $removeSpecialCharacters = false
    )
    {}

    public function sanitize(mixed $string): mixed
    {
        $tmpString = (string) $string;

        if ($this->removeAccents) {
            $tmpString = Normalizer::normalize($tmpString, Normalizer::FORM_D);
            $tmpString = preg_replace("/\p{M}/u", "", $tmpString);
        }

        if ($this->removeAlphanumerics) {
            $tmpString = preg_replace("/[a-zA-Z]/", "", $tmpString);
        }

        if ($this->removeBlank) {
            $tmpString = preg_replace("/\s/", "", $tmpString);
        }

        if ($this->removeSpecialCharacters) {
            $tmpString = preg_replace("/[^\p{L}\p{N}\p{Po}\p{Z}]/u", "", $tmpString);
        }

        $tmpString = Normalizer::normalize($tmpString, $this->normalizationForm);

        if ($this->lowerCase) {
            $tmpString = mb_strtolower($tmpString);
        } else if ($this->upperCase) {
            $tmpString = mb_strtoupper($tmpString);
        }

        if ($this->trim) {
            $tmpString = mb_trim($tmpString);
        }

        return $tmpString;
    }
}