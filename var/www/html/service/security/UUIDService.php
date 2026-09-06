<?php

namespace App\service\security;

class UUIDService {
    public static function generate(): string {
        $timestamp = (int) (microtime(true) * 1000);
        $timeHigh = ($timestamp >> 16) & 0xFFFFFFFF; 
        $timeLow = $timestamp & 0xFFFF;
        $v7AndRand = random_int(0, 0x0FFF) | 0x7000;
        $variantAndRand = random_int(0, 0x3FFF) | 0x8000;
        $randomBits = random_int(0, 0xFFFFFFFFFFFF);

        return sprintf(
            '%08x-%04x-%04x-%04x-%012x',
            $timeHigh,
            $timeLow,
            $v7AndRand,
            $variantAndRand,
            $randomBits
        );
    }
}