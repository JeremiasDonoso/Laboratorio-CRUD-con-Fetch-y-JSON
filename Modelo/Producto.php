<?php

require_once __DIR__ . "/Conexion.php";
require_once __DIR__ . "/Sanitizador.php";

class Producto
{
    private ?int $id;
    private string $codigo;
    private string $producto;
    private ?float $precio;
    private ?int $cantidad;

    public function __construct(
        ?int $id,
        string $codigo,
        string $producto,
        ?float $precio,
        ?int $cantidad
    ) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->producto = $producto;
        $this->precio = $precio;
        $this->cantidad = $cantidad;
    }

    public static function crearDesdeArray(array $datos): Producto
    {
        return new Producto(
            Sanitizador::id($datos["id"] ?? null),
            Sanitizador::codigo($datos["codigo"] ?? ""),
            Sanitizador::texto($datos["producto"] ?? ""),
            Sanitizador::decimal($datos["precio"] ?? null),
            Sanitizador::entero($datos["cantidad"] ?? null)
        );
    }

    public function guardar(): array
    {
        $errores = $this->validarParaGuardar();

        if (!empty($errores)) {
            return self::respuesta(false, "Hay errores en los datos enviados.", "Guardar", $errores);
        }

        if ($this->existeCodigo()) {
            return self::respuesta(false, "Ya existe un producto con ese código.", "Guardar", [
                "codigo" => "El código ingresado ya está registrado."
            ]);
        }

        $this->insertar();

        return self::respuesta(true, "Producto guardado correctamente.", "Guardar");
    }

    public function modificar(): array
    {
        $errores = $this->validarParaModificar();

        if (!empty($errores)) {
            return self::respuesta(false, "Hay errores en los datos enviados.", "Modificar", $errores);
        }

        if (!$this->existeId()) {
            return self::respuesta(false, "El producto que intentas modificar no existe.", "Modificar", [
                "id" => "No se encontró el producto seleccionado."
            ]);
        }

        if ($this->existeCodigoEnOtroProducto()) {
            return self::respuesta(false, "Ya existe otro producto con ese código.", "Modificar", [
                "codigo" => "El código pertenece a otro producto."
            ]);
        }

        $this->actualizar();

        return self::respuesta(true, "Producto modificado correctamente.", "Modificar");
    }

    public static function buscarPorCodigo(string $codigo): array
    {
        $codigo = Sanitizador::codigo($codigo);

        if ($codigo === "") {
            return self::respuesta(false, "Debe ingresar un código para buscar.", "Buscar", [
                "codigo" => "El código es obligatorio."
            ]);
        }

        $producto = self::obtenerPorCodigo($codigo);

        if ($producto === null) {
            return self::respuesta(false, "No se encontró ningún producto con ese código.", "Buscar");
        }

        return self::respuesta(true, "Producto encontrado.", "Buscar", [], $producto);
    }

    public static function listar(): array
    {
        $sql = "SELECT id, codigo, producto, precio, cantidad 
                FROM productos 
                ORDER BY id DESC";

        $productos = Conexion::consultar($sql);

        return self::respuesta(true, "Productos cargados correctamente.", "Listar", [], $productos);
    }

    public static function eliminar($id): array
    {
        $id = Sanitizador::id($id);

        if ($id === null) {
            return self::respuesta(false, "Debe seleccionar un producto válido para eliminar.", "Eliminar", [
                "id" => "El ID del producto es obligatorio."
            ]);
        }

        if (!self::existeProductoPorId($id)) {
            return self::respuesta(false, "El producto que intentas eliminar no existe.", "Eliminar", [
                "id" => "No se encontró el producto seleccionado."
            ]);
        }

        self::eliminarPorId($id);

        return self::respuesta(true, "Producto eliminado correctamente.", "Eliminar");
    }

    private static function existeProductoPorId(int $id): bool
    {
        $sql = "SELECT id FROM productos WHERE id = :id LIMIT 1";

        $producto = Conexion::consultarUno($sql, [
            ":id" => $id
        ]);

        return $producto !== null;
    }

    private static function eliminarPorId(int $id): bool
    {
        $sql = "DELETE FROM productos WHERE id = :id";

        return Conexion::ejecutar($sql, [
            ":id" => $id
        ]);
    }

    private function validarParaGuardar(): array
    {
        $errores = $this->validarCamposComunes();

        if ($this->cantidad !== null && $this->cantidad < 1) {
            $errores["cantidad"] = "Para registrar un producto nuevo, la cantidad debe ser mínimo 1.";
        }

        return $errores;
    }

    private function validarParaModificar(): array
    {
        $errores = $this->validarCamposComunes();

        if ($this->id === null) {
            $errores["id"] = "El ID del producto es obligatorio para modificar.";
        }

        if ($this->cantidad !== null && $this->cantidad < 0) {
            $errores["cantidad"] = "La cantidad no puede ser negativa.";
        }

        return $errores;
    }

    private function validarCamposComunes(): array
    {
        $errores = [];

        $this->validarCodigo($errores);
        $this->validarNombreProducto($errores);
        $this->validarPrecio($errores);
        $this->validarCantidad($errores);

        return $errores;
    }

    private function validarCodigo(array &$errores): void
    {
        if ($this->codigo === "") {
            $errores["codigo"] = "El código es obligatorio.";
            return;
        }

        if (strlen($this->codigo) > 20) {
            $errores["codigo"] = "El código no puede tener más de 20 caracteres.";
        }
    }

    private function validarNombreProducto(array &$errores): void
    {
        if ($this->producto === "") {
            $errores["producto"] = "El nombre del producto es obligatorio.";
            return;
        }

        if (strlen($this->producto) > 100) {
            $errores["producto"] = "El nombre del producto no puede tener más de 100 caracteres.";
        }
    }

    private function validarPrecio(array &$errores): void
    {
        if ($this->precio === null) {
            $errores["precio"] = "El precio es obligatorio y debe ser numérico.";
            return;
        }

        if ($this->precio <= 0) {
            $errores["precio"] = "El precio debe ser mayor que 0.";
        }
    }

    private function validarCantidad(array &$errores): void
    {
        if ($this->cantidad === null) {
            $errores["cantidad"] = "La cantidad es obligatoria y debe ser un número entero.";
        }
    }

    private function insertar(): bool
    {
        $sql = "INSERT INTO productos (codigo, producto, precio, cantidad)
                VALUES (:codigo, :producto, :precio, :cantidad)";

        return Conexion::ejecutar($sql, $this->parametrosProducto());
    }

    private function actualizar(): bool
    {
        $sql = "UPDATE productos
                SET codigo = :codigo,
                    producto = :producto,
                    precio = :precio,
                    cantidad = :cantidad
                WHERE id = :id";

        $parametros = $this->parametrosProducto();
        $parametros[":id"] = $this->id;

        return Conexion::ejecutar($sql, $parametros);
    }

    private function parametrosProducto(): array
    {
        return [
            ":codigo" => $this->codigo,
            ":producto" => $this->producto,
            ":precio" => $this->precio,
            ":cantidad" => $this->cantidad
        ];
    }

    private function existeCodigo(): bool
    {
        return self::obtenerPorCodigo($this->codigo) !== null;
    }

    private function existeId(): bool
    {
        $sql = "SELECT id FROM productos WHERE id = :id LIMIT 1";

        $producto = Conexion::consultarUno($sql, [
            ":id" => $this->id
        ]);

        return $producto !== null;
    }

    private function existeCodigoEnOtroProducto(): bool
    {
        $sql = "SELECT id 
                FROM productos 
                WHERE codigo = :codigo 
                AND id != :id 
                LIMIT 1";

        $producto = Conexion::consultarUno($sql, [
            ":codigo" => $this->codigo,
            ":id" => $this->id
        ]);

        return $producto !== null;
    }

    private static function obtenerPorCodigo(string $codigo): ?array
    {
        $sql = "SELECT id, codigo, producto, precio, cantidad
                FROM productos 
                WHERE codigo = :codigo 
                LIMIT 1";

        return Conexion::consultarUno($sql, [
            ":codigo" => $codigo
        ]);
    }

    private static function respuesta(
        bool $success,
        string $message,
        string $accion,
        array $errors = [],
        $data = null
    ): array {
        return [
            "success" => $success,
            "message" => $message,
            "accion" => $accion,
            "errors" => $errors,
            "data" => $data
        ];
    }
}