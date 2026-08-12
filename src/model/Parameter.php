<?php
namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Parameter{
    private string $name;
    private int $parameterType;
    private string $value;
    private string $description;
    private bool $required;
    /*
    Necessário criar model User
    private string $useralt;
    private DateTime $altDate;
    */

    public function __construct(string $name, int $parameterType, string $value, string $description, bool $required)
    {
        $this->name = $name;

        $this->parameterType = $parameterType;

        $this->value = $value;

        $this->description = $description;

        $this->required = $required;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setParameterType(int $parameterType): void {
        $this->parameterType = $parameterType;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setRequired(bool $required): void {
        $this->required = $required;
    }

    public function getName(): string {
        return htmlspecialchars($this->name);
    }
    
    public function getParameterType(): int {
        return $this->parameterType;
    }

    public function getValue(): string {
        return htmlspecialchars($this->value);
    }

    public function getDescription(): string {
        return htmlspecialchars($this->description);
    }

    public function getRequired(): bool {
        return $this->required;
    }
}