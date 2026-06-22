# 🧪 Laboratorio CRUD con Fetch API + PHP + MySQL + Bootstrap

## 📌 Descripción del proyecto

Este proyecto es un sistema CRUD (Create, Read, Update, Delete) desarrollado como laboratorio académico para la asignatura Desarrollo de Software VII.

Permite la gestión de productos mediante una interfaz web dinámica utilizando HTML, Bootstrap, JavaScript con Fetch API, PHP orientado a objetos y MySQL, manejando respuestas en formato JSON y alertas con SweetAlert2.

---

## ⚙️ Tecnologías utilizadas

- HTML5
- CSS3 / Bootstrap 5
- JavaScript (Fetch API)
- PHP 8+ (Programación orientada a objetos)
- MySQL (XAMPP / phpMyAdmin)
- SweetAlert2

---

## 🗄️ Base de datos

Nombre de la base de datos:

productosdb

Tabla productos:

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL
);

---

## 🚀 Funcionalidades

✔ Registrar productos  
✔ Listar productos dinámicamente  
✔ Buscar producto por código  
✔ Modificar productos  
✔ Eliminar productos  
✔ Validaciones en cliente y servidor  
✔ Respuestas JSON estructuradas  
✔ Interfaz responsive con Bootstrap  
✔ Alertas con SweetAlert2  

---

## 🔁 Flujo del sistema

Formulario (HTML + Bootstrap)
→ JavaScript (Fetch API)
→ registrar.php (switch de acciones)
→ Clases PHP (Producto + Conexion + Sanitizador)
→ Base de datos MySQL
→ Respuesta JSON
→ Frontend (SweetAlert2 + actualización dinámica)

---

## 📡 Acciones soportadas

- Guardar
- Modificar
- Buscar
- Listar
- Eliminar

---

## 🎨 Uso de Bootstrap

Bootstrap se utiliza para el diseño visual del sistema mediante:

- Botones estilizados (btn btn-primary, btn-danger, etc.)
- Sistema de grid (container, row, col)
- Formularios (form-control, form-label)
- Tablas responsivas (table, table-striped, table-hover)
- Tarjetas (card, card-header, card-body)
- Espaciado y utilidades (mt-3, py-5, gap-2)

---

## 📝 Notas importantes

- El sistema utiliza arquitectura modular en PHP.
- Todas las respuestas del servidor están en formato JSON.
- Se aplica validación en frontend y backend.
- El proyecto debe ejecutarse en servidor local (XAMPP).
- No abrir el proyecto con doble clic, siempre usar localhost.

---
<div align="center">

### 📌 Información del Laboratorio

🎓 **Universidad Tecnológica de Panamá**

| Estudiante | Correo |
|------------|---------|
| Jeremias Donoso | jeremias.donoso@utp.ac.pa |
| Luis De Los Ríos | luis.delosrios@utp.ac.pa |

📚 **Curso:** Desarrollo de Software 7  
👩‍🏫 **Instructora:** Irina Fong  

📅 **Fecha Final de Entrega:** 22 de junio de 2026

</div>
