<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

abstract class AbstractModel
{
    protected static array $ILIKE_COLUMNS = [];
    protected static array $DATE_COLUMNS = [];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = [];
    protected static array $INSERT_VALUES = [];
    protected static string $TABLE_NAME = "";
    protected static string $PRIMARY_KEY = "";

    private function getAllProperties(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if ($property->isInitialized($this)) {
                $properties[$property->getName()] = $property->getValue($this);
            } else {
                $properties[$property->getName()] = null;
            }
        }

        return $properties;
    }

    public function getCondictionPairs(): array
    {
        return array_filter($this->getAllProperties(), function ($condiction) {
            return isset($condiction);
        });
    }

    public function getInsertValuesPairs(): array
    {
        return array_filter($this->getAllProperties(), function ($column) {
            return in_array($column, static::$INSERT_VALUES);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getPrimaryKeyPairs(): array
    {
        return array_filter($this->getAllProperties(), function ($column) {
            return $column == static::$PRIMARY_KEY;
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getPrimaryKey(): string
    {
        return static::$PRIMARY_KEY;
    }

    public function getTableName(): string
    {
        return static::$TABLE_NAME;
    }

    public function getIlikeColumns(): array
    {
        return static::$ILIKE_COLUMNS;
    }

    public function getColumnsWithAnAllOption(): array
    {
        return static::$COLUMNS_WITH_AN_ALL_OPTION;
    }

    public function getDateColumns(): array
    {
        return static::$DATE_COLUMNS;
    }

    public function getClassName(): string
    {
        $explode = explode("\\", static::class);
        $end = end($explode);
        $classNameLcfirst = lcfirst($end);
        return $classNameLcfirst;
    }

    public static function getClassNameExtended(): string
    {
        $explode = explode("\\", static::class);
        $end = end($explode);
        $classNameLcfirst = lcfirst($end);
        return ($classNameLcfirst . ".php");
    }

    public static function getClassViewNameExtended(): string
    {
        $explode = explode("\\", static::class);
        $end = end($explode);
        $classNameLcfirst = lcfirst($end);
        return ($classNameLcfirst . "View" . ".php");
    }
}
