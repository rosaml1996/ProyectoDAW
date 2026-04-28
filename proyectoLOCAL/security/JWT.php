<?php

class JWT
{
    private static string $secret = 'PROYECTO_FISIO_2026_JWT_SECRET_9xLm72QpA4vRz31K';

    public static function generar(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $payload['iat'] = time();
        $payload['exp'] = time() + 3600;

        $header64 = self::base64UrlEncode(json_encode($header));
        $payload64 = self::base64UrlEncode(json_encode($payload));

        $firma = hash_hmac('sha256', $header64 . '.' . $payload64, self::$secret, true);
        $firma64 = self::base64UrlEncode($firma);

        return $header64 . '.' . $payload64 . '.' . $firma64;
    }

    public static function verificar(string $jwt): array|false
    {
        $partes = explode('.', $jwt);

        if (count($partes) !== 3) {
            return false;
        }

        [$header64, $payload64, $firma64] = $partes;

        $firmaCalculada = hash_hmac('sha256', $header64 . '.' . $payload64, self::$secret, true);
        $firmaCalculada64 = self::base64UrlEncode($firmaCalculada);

        if (!hash_equals($firmaCalculada64, $firma64)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($payload64), true);

        if (!is_array($payload)) {
            return false;
        }

        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $resto = strlen($data) % 4;
        if ($resto) {
            $data .= str_repeat('=', 4 - $resto);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}