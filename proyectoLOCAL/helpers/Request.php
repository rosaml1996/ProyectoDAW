<?php

class Request
{
    public static function json(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!is_array($data)) {
            Response::json(['error' => 'JSON no válido'], 400);
        }

        return $data;
    }
}