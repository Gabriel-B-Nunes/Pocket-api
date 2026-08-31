<?php

namespace App\service\data\validator;

use DateTime;
use Attribute;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

#[Attribute(Attribute::TARGET_PROPERTY)] 
class StringValidator implements ValidatorInterface {
    public function __construct(
        private array $errorMessages = [],
        private ?int $minLength = null,
        private ?int $maxLength = null,
        private bool $acceptNull = false,
        private bool $acceptEmptyString = false,
        private bool $acceptNumbers = true,
        private bool $acceptSpecialChars = true,
        private bool $onlyAcceptNumbers = false,
        private bool $mustContainNumbers = false,
        private bool $mustContainSpecialCharacters = false,
        private bool $validEmail = false,
        private bool $validDateTime = false,
        private string $dateTimeFormat = "Y-m-d H:i:s"
    )
    {}

    public function validate(mixed $value, string $name): bool
    {
        $validString = true;

        if ($this->minLength && mb_strlen($value) < $this->minLength) {
            $this->errorMessages[] = "Field {$name} cannot be shorter than {$this->minLength}.";
            $validString = false;
        }

        if ($this->maxLength && mb_strlen($value) > $this->maxLength) {
            $this->errorMessages[] = "Field {$name} cannot be longer than {$this->maxLength}.";
            $validString = false;
        }

        if (!$this->acceptNull && $value === null) {
            $this->errorMessages[] = "Field {$name} cannot be null.";
            $validString = false;
        }

        if (!$this->acceptEmptyString && $value === "") {
            $this->errorMessages[] = "Field {$name} cannot be empty.";
            $validString = false;
        }

        if (!$this->acceptNumbers && preg_match("/[0-9]/", $value)) {
            $this->errorMessages[] = "Field {$name} cannot contain numbers.";
            $validString = false;
        }

        if (!$this->acceptSpecialChars && preg_match("/[^a-zA-Z0-9\s]/", $value)) {
            $this->errorMessages[] = "Field {$name} cannot contain special characters.";
            $validString = false;
        }

        if ($this->onlyAcceptNumbers && preg_match("/[^0-9]/", $value)) {
            $this->errorMessages[] = "Field {$name} can only contain numbers.";
            $validString = false;
        }

        if ($this->mustContainNumbers && !preg_match("/[0-9]/", $value)) {
            $this->errorMessages[] = "Field {$name} must contain numbers.";
            $validString = false;
        }

        if ($this->mustContainSpecialCharacters && !preg_match("/[\p{M}\p{P}\p{S}]/u", $value)) {
            $this->errorMessages[] = "Field {$name} must contain special characters.";
            $validString = false;
        }

        if ($this->validEmail && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessages[] = "Field {$name} must be a valid email.";
            $validString = false;
        }

        if ($this->validDateTime && !$this->validateDateTimeFormat($value, $this->dateTimeFormat)) {
            $this->errorMessages[] = "Field {$name} must be in the format {$this->dateTimeFormat}.";
            $validString = false;
        }

        return $validString;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function validateDateTimeFormat(mixed $value, string $format): bool {
        $dateTime = DateTime::createFromFormat($format, $value);

        if ($dateTime !== false) {
            return true;
        }

        return false;
    }
}