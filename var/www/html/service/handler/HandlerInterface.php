<?php

namespace App\service\handler;

use App\model\Request;

interface HandlerInterface {
<<<<<<< HEAD
    public function setNext(AbstractHandler $handler): void;
=======
    public function setNext(): void;
>>>>>>> refactor-request
    public function handle(Request $request);
}