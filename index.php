<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD con Fetch + PHP + MySQL</title>

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <main class="container py-5">

        <section class="mb-4 text-center">
            <h1 class="fw-bold text-primary">Gestión de Productos</h1>
            <p class="text-muted">
                CRUD con Fetch API, PHP OOP, MySQL, Bootstrap y SweetAlert2
            </p>
        </section>

        <section class="row justify-content-center mb-5">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0" id="tituloFormulario">Registrar producto</h5>
                    </div>

                    <div class="card-body">
                        <form id="formProducto" autocomplete="off">
                            <input type="hidden" id="idProducto" name="id">

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="codigo" 
                                        name="codigo" 
                                        maxlength="20"
                                        placeholder="Ejemplo: A001"
                                    >
                                    <div class="form-text">
                                        Máximo 20 caracteres.
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="producto" class="form-label">Producto</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="producto" 
                                        name="producto" 
                                        maxlength="100"
                                        placeholder="Ejemplo: Mouse óptico"
                                    >
                                    <div class="form-text">
                                        Máximo 100 caracteres.
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="precio" 
                                        name="precio" 
                                        min="0.01"
                                        step="0.01"
                                        placeholder="Ejemplo: 10.50"
                                    >
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="cantidad" 
                                        name="cantidad" 
                                        min="0"
                                        step="1"
                                        placeholder="Ejemplo: 5"
                                    >
                                    <div class="form-text">
                                        Para registrar debe ser mínimo 1. Para modificar puede ser 0.
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="button" class="btn btn-primary" id="btnGuardar">
                                    Guardar
                                </button>

                                <button type="button" class="btn btn-warning text-white d-none" id="btnModificar">
                                    Modificar
                                </button>

                                <button type="button" class="btn btn-danger d-none" id="btnEliminar">
                                    Eliminar
                                </button>

                                <button type="button" class="btn btn-info text-white" id="btnBuscar">
                                    Buscar por código
                                </button>

                                <button type="button" class="btn btn-secondary" id="btnLimpiar">
                                    Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de productos</h5>

                <button type="button" class="btn btn-outline-light btn-sm" id="btnListar">
                    Recargar tabla
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tbodyProductos">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay productos cargados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap JS -->
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Script principal -->
    <script src="script.js"></script>
</body>
</html>