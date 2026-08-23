<?php
interface ControllerInterface {
    public function handleRequest(string $request): string;
}