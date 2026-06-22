document.addEventListener("DOMContentLoaded", inicializarAplicacion);

function inicializarAplicacion() {
    registrarEventos();
    ejecutarAccion("Listar");
}

function registrarEventos() {
    obtenerElemento("btnGuardar").addEventListener("click", () => ejecutarAccion("Guardar"));
    obtenerElemento("btnModificar").addEventListener("click", () => ejecutarAccion("Modificar"));
    obtenerElemento("btnEliminar").addEventListener("click", () => ejecutarAccion("Eliminar"));
    obtenerElemento("btnBuscar").addEventListener("click", () => ejecutarAccion("Buscar"));
    obtenerElemento("btnLimpiar").addEventListener("click", limpiarFormulario);
    obtenerElemento("btnListar").addEventListener("click", () => ejecutarAccion("Listar"));
    obtenerElemento("tbodyProductos").addEventListener("click", manejarClickTabla);
}

function ejecutarAccion(accion) {
    switch (accion) {
        case "Guardar":
            guardarProducto();
            break;

        case "Modificar":
            modificarProducto();
            break;

        case "Eliminar":
            eliminarProducto();
            break;

        case "Buscar":
            buscarProducto();
            break;

        case "Listar":
            listarProductos();
            break;

        default:
            mostrarAlerta("error", "Acción no válida", "La acción solicitada no existe.");
            break;
    }
}

async function guardarProducto() {
    const errores = validarFormulario("Guardar");

    if (hayErrores(errores)) {
        mostrarErroresValidacion(errores);
        return;
    }

    const respuesta = await enviarPeticion("Guardar", crearFormData("Guardar"));
    procesarRespuestaGuardar(respuesta);
}

async function modificarProducto() {
    const errores = validarFormulario("Modificar");

    if (hayErrores(errores)) {
        mostrarErroresValidacion(errores);
        return;
    }

    const respuesta = await enviarPeticion("Modificar", crearFormData("Modificar"));
    procesarRespuestaModificar(respuesta);
}

async function eliminarProducto() {
    const id = obtenerValor("idProducto");

    if (id === "") {
        mostrarAlerta("warning", "Producto no seleccionado", "Debe seleccionar un producto antes de eliminar.");
        return;
    }

    const confirmado = await confirmarEliminacion();

    if (!confirmado) {
        return;
    }

    const formData = new FormData();
    formData.append("accion", "Eliminar");
    formData.append("id", id);

    const respuesta = await enviarPeticion("Eliminar", formData);
    procesarRespuestaEliminar(respuesta);
}

async function eliminarProductoPorId(id) {
    const confirmado = await confirmarEliminacion();

    if (!confirmado) {
        return;
    }

    const formData = new FormData();
    formData.append("accion", "Eliminar");
    formData.append("id", id);

    const respuesta = await enviarPeticion("Eliminar", formData);
    procesarRespuestaEliminar(respuesta);
}

async function confirmarEliminacion() {
    const resultado = await Swal.fire({
        icon: "warning",
        title: "¿Eliminar producto?",
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc3545"
    });

    return resultado.isConfirmed;
}

function procesarRespuestaEliminar(respuesta) {
    if (!respuesta.success) {
        mostrarRespuestaError(respuesta);
        return;
    }

    mostrarAlerta("success", "Eliminado", respuesta.message);
    limpiarFormulario();
    ejecutarAccion("Listar");
}

async function buscarProducto() {
    const codigo = obtenerValor("codigo");

    if (codigo === "") {
        mostrarAlerta("warning", "Código obligatorio", "Debe ingresar un código para buscar.");
        enfocarCampo("codigo");
        return;
    }

    const formData = new FormData();
    formData.append("accion", "Buscar");
    formData.append("codigo", codigo);

    const respuesta = await enviarPeticion("Buscar", formData);
    procesarRespuestaBuscar(respuesta);
}

async function listarProductos() {
    const formData = new FormData();
    formData.append("accion", "Listar");

    const respuesta = await enviarPeticion("Listar", formData);
    procesarRespuestaListar(respuesta);
}

async function enviarPeticion(accion, formData) {
    try {
        const response = await fetch("registrar.php", {
            method: "POST",
            body: formData
        });

        return await convertirRespuestaJson(response);
    } catch (error) {
        return crearRespuestaErrorConexion(accion);
    }
}

async function convertirRespuestaJson(response) {
    const texto = await response.text();

    try {
        return JSON.parse(texto);
    } catch (error) {
        console.error("Respuesta no JSON del servidor:", texto);

        return {
            success: false,
            message: "El servidor no devolvió una respuesta JSON válida.",
            accion: "ErrorJSON",
            errors: {
                json: "Revise que registrar.php no tenga echo, print, var_dump o espacios fuera del JSON."
            },
            data: null
        };
    }
}

function crearFormData(accion) {
    const formulario = obtenerElemento("formProducto");
    const formData = new FormData(formulario);

    formData.append("accion", accion);

    return formData;
}

function validarFormulario(accion) {
    const errores = {};

    validarCodigo(errores);
    validarProducto(errores);
    validarPrecio(errores);
    validarCantidad(errores, accion);

    if (accion === "Modificar") {
        validarIdProducto(errores);
    }

    return errores;
}

function validarCodigo(errores) {
    const codigo = obtenerValor("codigo");

    if (codigo === "") {
        errores.codigo = "El código es obligatorio.";
        return;
    }

    if (codigo.length > 20) {
        errores.codigo = "El código no puede superar los 20 caracteres.";
    }
}

function validarProducto(errores) {
    const producto = obtenerValor("producto");

    if (producto === "") {
        errores.producto = "El nombre del producto es obligatorio.";
        return;
    }

    if (producto.length > 100) {
        errores.producto = "El nombre del producto no puede superar los 100 caracteres.";
    }
}

function validarPrecio(errores) {
    const precio = Number(obtenerValor("precio"));

    if (obtenerValor("precio") === "") {
        errores.precio = "El precio es obligatorio.";
        return;
    }

    if (Number.isNaN(precio) || precio <= 0) {
        errores.precio = "El precio debe ser mayor que 0.";
    }
}

function validarCantidad(errores, accion) {
    const cantidadTexto = obtenerValor("cantidad");
    const cantidad = Number(cantidadTexto);

    if (cantidadTexto === "") {
        errores.cantidad = "La cantidad es obligatoria.";
        return;
    }

    if (!Number.isInteger(cantidad)) {
        errores.cantidad = "La cantidad debe ser un número entero.";
        return;
    }

    if (accion === "Guardar" && cantidad < 1) {
        errores.cantidad = "Para registrar, la cantidad debe ser mínimo 1.";
        return;
    }

    if (accion === "Modificar" && cantidad < 0) {
        errores.cantidad = "Para modificar, la cantidad no puede ser negativa.";
    }
}

function validarIdProducto(errores) {
    const id = obtenerValor("idProducto");

    if (id === "") {
        errores.id = "Debe buscar o seleccionar un producto antes de modificar.";
    }
}

function procesarRespuestaGuardar(respuesta) {
    if (!respuesta.success) {
        mostrarRespuestaError(respuesta);
        return;
    }

    mostrarAlerta("success", "Guardado", respuesta.message);
    limpiarFormulario();
    ejecutarAccion("Listar");
}

function procesarRespuestaModificar(respuesta) {
    if (!respuesta.success) {
        mostrarRespuestaError(respuesta);
        return;
    }

    mostrarAlerta("success", "Modificado", respuesta.message);
    limpiarFormulario();
    ejecutarAccion("Listar");
}

function procesarRespuestaBuscar(respuesta) {
    if (!respuesta.success) {
        mostrarRespuestaError(respuesta);
        return;
    }

    cargarProductoEnFormulario(respuesta.data);
    mostrarAlerta("success", "Producto encontrado", respuesta.message);
}

function procesarRespuestaListar(respuesta) {
    if (!respuesta.success) {
        mostrarRespuestaError(respuesta);
        return;
    }

    renderizarTabla(respuesta.data);
}

function renderizarTabla(productos) {
    const tbody = obtenerElemento("tbodyProductos");

    if (!productos || productos.length === 0) {
        tbody.innerHTML = crearFilaVacia();
        return;
    }

    tbody.innerHTML = productos.map(crearFilaProducto).join("");
}

function crearFilaProducto(producto) {
    return `
        <tr>
            <td>${escaparHTML(producto.id)}</td>
            <td>${escaparHTML(producto.codigo)}</td>
            <td>${escaparHTML(producto.producto)}</td>
            <td>${formatearPrecio(producto.precio)}</td>
            <td>${escaparHTML(producto.cantidad)}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <button 
                        type="button" 
                        class="btn btn-sm btn-warning text-white btn-editar"
                        data-id="${escaparHTML(producto.id)}"
                        data-codigo="${escaparHTML(producto.codigo)}"
                        data-producto="${escaparHTML(producto.producto)}"
                        data-precio="${escaparHTML(producto.precio)}"
                        data-cantidad="${escaparHTML(producto.cantidad)}"
                    >
                        Editar
                    </button>

                    <button 
                        type="button" 
                        class="btn btn-sm btn-danger btn-eliminar"
                        data-id="${escaparHTML(producto.id)}"
                    >
                        Eliminar
                    </button>
                </div>
            </td>
        </tr>
    `;
}

function crearFilaVacia() {
    return `
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                No hay productos registrados.
            </td>
        </tr>
    `;
}

function manejarClickTabla(evento) {
    const botonEditar = evento.target.closest(".btn-editar");
    const botonEliminar = evento.target.closest(".btn-eliminar");

    if (botonEditar) {
        const producto = obtenerProductoDesdeBoton(botonEditar);
        cargarProductoEnFormulario(producto);
        return;
    }

    if (botonEliminar) {
        const id = botonEliminar.dataset.id;
        eliminarProductoPorId(id);
    }
}

function obtenerProductoDesdeBoton(boton) {
    return {
        id: boton.dataset.id,
        codigo: boton.dataset.codigo,
        producto: boton.dataset.producto,
        precio: boton.dataset.precio,
        cantidad: boton.dataset.cantidad
    };
}

function cargarProductoEnFormulario(producto) {
    establecerValor("idProducto", producto.id);
    establecerValor("codigo", producto.codigo);
    establecerValor("producto", producto.producto);
    establecerValor("precio", producto.precio);
    establecerValor("cantidad", producto.cantidad);

    activarModoModificar();
}

function activarModoModificar() {
    obtenerElemento("tituloFormulario").textContent = "Modificar producto";

    obtenerElemento("btnGuardar").classList.add("d-none");
    obtenerElemento("btnModificar").classList.remove("d-none");
    obtenerElemento("btnEliminar").classList.remove("d-none");
}

function activarModoGuardar() {
    obtenerElemento("tituloFormulario").textContent = "Registrar producto";

    obtenerElemento("btnGuardar").classList.remove("d-none");
    obtenerElemento("btnModificar").classList.add("d-none");
    obtenerElemento("btnEliminar").classList.add("d-none");
}

function limpiarFormulario() {
    obtenerElemento("formProducto").reset();
    establecerValor("idProducto", "");
    activarModoGuardar();
    enfocarCampo("codigo");
}

function mostrarRespuestaError(respuesta) {
    const mensaje = construirMensajeErrores(respuesta);

    mostrarAlerta("error", "No se pudo completar la acción", mensaje);
}

function mostrarErroresValidacion(errores) {
    const mensaje = Object.values(errores).join("<br>");
    mostrarAlerta("warning", "Revise los datos", mensaje);
    enfocarPrimerCampoConError(errores);
}

function construirMensajeErrores(respuesta) {
    if (!respuesta.errors || Object.keys(respuesta.errors).length === 0) {
        return respuesta.message;
    }

    const detalles = Object.values(respuesta.errors).join("<br>");

    return `${respuesta.message}<br><br>${detalles}`;
}

function mostrarAlerta(icono, titulo, mensaje) {
    Swal.fire({
        icon: icono,
        title: titulo,
        html: mensaje,
        confirmButtonText: "Aceptar"
    });
}

function hayErrores(errores) {
    return Object.keys(errores).length > 0;
}

function enfocarPrimerCampoConError(errores) {
    const campos = {
        id: "codigo",
        codigo: "codigo",
        producto: "producto",
        precio: "precio",
        cantidad: "cantidad"
    };

    const primerError = Object.keys(errores)[0];
    const campo = campos[primerError];

    if (campo) {
        enfocarCampo(campo);
    }
}

function obtenerElemento(id) {
    return document.getElementById(id);
}

function obtenerValor(id) {
    return obtenerElemento(id).value.trim();
}

function establecerValor(id, valor) {
    obtenerElemento(id).value = valor ?? "";
}

function enfocarCampo(id) {
    obtenerElemento(id).focus();
}

function formatearPrecio(precio) {
    const numero = Number(precio);

    if (Number.isNaN(numero)) {
        return "0.00";
    }

    return numero.toFixed(2);
}

function escaparHTML(valor) {
    const elemento = document.createElement("div");
    elemento.textContent = valor ?? "";

    return elemento.innerHTML;
}

function crearRespuestaErrorConexion(accion) {
    return {
        success: false,
        message: "No se pudo conectar con el servidor.",
        accion: accion,
        errors: {
            conexion: "Verifique que Apache esté encendido en XAMPP y que registrar.php exista."
        },
        data: null
    };
}