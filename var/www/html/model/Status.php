<?php

namespace App\model;


enum Status: string {
    case ACTIVE = "ACTIVE";
    case DISABLED = "DISABLED";
}