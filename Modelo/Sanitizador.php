<?php

class Sanitizador
{
    public static function texto(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = strip_tags($valor);
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor;
    }

    public static function codigo(?string $valor): string
    {
        $valor = self::texto($valor);
        $valor = strtoupper($valor);

        return $valor;
    }

    public static function decimal($valor): ?float
    {
        $valor = trim((string) $valor);
        $valor = str_replace(',', '.', $valor);

        if ($valor === '') {
            return null;
        }

        if (!filter_var($valor, FILTER_VALIDATE_FLOAT) && $valor !== '0' && $valor !== '0.0') {
            return null;
        }

        return (float) $valor;
    }

    public static function entero($valor): ?int
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
            return null;
        }

        return (int) $valor;
    }

    public static function id($valor): ?int
    {
        $id = self::entero($valor);

        if ($id === null || $id <= 0) {
            return null;
        }

        return $id;
    }

    public static function accion(?string $valor): string
    {
        return self::texto($valor);
    }
}