<?php

require_once __DIR__ . "/Modelo/Producto.php";

configurarRespuestaJson();

try {
    validarMetodoPost();

    $accion = obtenerAccion($_POST);
    $respuesta = procesarAccion($accion, $_POST);

    enviarRespuesta($respuesta);
} catch (Exception $e) {
    enviarRespuesta(respuestaErrorServidor());
}

function configurarRespuestaJson(): void
{
    header("Content-Type: application/json; charset=utf-8");
}

function validarMetodoPost(): void
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);

        enviarRespuesta([
            "success" => false,
            "message" => "Método no permitido. Use POST.",
            "accion" => "MetodoNoPermitido",
            "errors" => [
                "metodo" => "La solicitud debe realizarse mediante POST."
            ],
            "data" => null
        ]);
    }
}

function obtenerAccion(array $datos): string
{
    $accion = $datos["accion"] ?? $datos["Accion"] ?? "";
    return Sanitizador::accion($accion);
}

function procesarAccion(string $accion, array $datos): array
{
    switch ($accion) {
        case "Guardar":
            return guardarProducto($datos);

        case "Modificar":
            return modificarProducto($datos);

        case "Buscar":
            return buscarProducto($datos);

        case "Listar":
            return listarProductos();

        case "Eliminar":
            return eliminarProducto($datos);

        default:
            return accionNoValida($accion);
    }
}

function guardarProducto(array $datos): array
{
    $producto = Producto::crearDesdeArray($datos);
    return $producto->guardar();
}

function modificarProducto(array $datos): array
{
    $producto = Producto::crearDesdeArray($datos);
    return $producto->modificar();
}

function buscarProducto(array $datos): array
{
    $codigo = $datos["codigo"] ?? "";
    return Producto::buscarPorCodigo($codigo);
}

function listarProductos(): array
{
    return Producto::listar();
}

function eliminarProducto(array $datos): array
{
    $id = $datos["id"] ?? null;
    return Producto::eliminar($id);
}

function accionNoValida(string $accion): array
{
    return [
        "success" => false,
        "message" => "La acción solicitada no es válida.",
        "accion" => $accion,
        "errors" => [
            "accion" => "Acción permitida: Guardar, Modificar, Buscar, Listar o Eliminar."
        ],
        "data" => null
    ];
}

function respuestaErrorServidor(): array
{
    http_response_code(500);

    return [
        "success" => false,
        "message" => "Ocurrió un error interno en el servidor.",
        "accion" => "ErrorServidor",
        "errors" => [
            "servidor" => "No se pudo procesar la solicitud."
        ],
        "data" => null
    ];
}

function enviarRespuesta(array $respuesta): void
{
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}