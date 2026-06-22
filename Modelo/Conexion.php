<?php

class Conexion
{
    private static ?PDO $conexion = null;

    private const HOST = "localhost";
    private const DB_NAME = "productosdb";
    private const USER = "root";
    private const PASSWORD = "";
    private const CHARSET = "utf8mb4";

    public static function conectar(): PDO
    {
        if (self::$conexion === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::CHARSET;

                self::$conexion = new PDO($dsn, self::USER, self::PASSWORD);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error de conexión con la base de datos.");
            }
        }

        return self::$conexion;
    }

    public static function ejecutar(string $sql, array $parametros = []): bool
    {
        $stmt = self::conectar()->prepare($sql);
        return $stmt->execute($parametros);
    }

    public static function consultar(string $sql, array $parametros = []): array
    {
        $stmt = self::conectar()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public static function consultarUno(string $sql, array $parametros = []): ?array
    {
        $stmt = self::conectar()->prepare($sql);
        $stmt->execute($parametros);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }
}