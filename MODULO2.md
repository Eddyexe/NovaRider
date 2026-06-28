# NovaRider — MODULO 2: Vehículos, Clientes e Historial

Este módulo gestiona la relación entre los clientes y sus motocicletas, manteniendo un registro histórico detallado de cada intervención técnica realizada en el taller.

## Funcionalidades Principales

- **Gestión de Clientes:** CRUD completo para clientes, incluyendo validación de CI/NIT único y gestión de estado (Activo/Inactivo).
- **Registro de Motocicletas:** Vinculación de vehículos a clientes con campos técnicos detallados (Nro. Chasis, Nro. Motor, Cilindrada, Color).
- **Historial de Servicios por Vehículo:** Línea de tiempo visual que muestra todas las Órdenes de Trabajo (OT) pasadas de una motocicleta específica.
- **Reportes Estadísticos:** Vista previa de datos clave como el conteo de motos por cliente y listados generales de vehículos.
- **Exportación a PDF:** Generación de reportes profesionales utilizando la librería `dompdf` para impresión o auditoría.

## Arquitectura Técnica

### Backend (Laravel)
- **Modelos:** 
  - `Cliente.php`: Relación `hasMany` con Motocicletas.
  - `Motocicleta.php`: Relación `hasMany` con Ordenes de Trabajo.
  - `OrdenTrabajo.php`, `DetalleOrdenTrabajo.php`, `Servicio.php`: Estructura para el historial técnico.
- **Controladores:**
  - `Api/ClienteController.php`: Lógica de clientes y validaciones.
  - `Api/MotocicletaController.php`: CRUD de motos y endpoint de `historial($id)`.
  - `ReporteController.php`: Lógica de filtrado de datos y generación de PDF via ` Barryvdh\DomPDF`.
- **Rutas:** Protegidas por middleware `role:1,3,4` según el nivel de acceso requerido.

### Frontend (Vue 3)
- **Vistas:**
  - `clientes/ClientesView.vue`: Listado y gestión de propietarios.
  - `motocicletas/MotocicletasView.vue`: Listado de vehículos con acceso rápido al historial.
  - `reportes/ReportesView.vue`: Interfaz de filtrado y botón de exportación.
- **Componentes:**
  - `HistorialVehiculoModal.vue`: Modal dinámico con línea de tiempo y animaciones GSAP para visualizar servicios pasados.
- **Estado (Pinia):** 
  - `clientesStore.js` y `motocicletasStore.js` para la gestión reactiva de los datos.

## Base de Datos (Tablas Involucradas)

| Tabla | Propósito |
|---|---|
| `TClientes` | Información personal, NIT y dirección del cliente. |
| `TMotocicletas` | Datos técnicos del vehículo vinculados al `id_cliente`. |
| `TOrdenesTrabajo` | Registro de cabecera de cada ingreso al taller. |
| `TDetallesOrdenTrabajo` | Servicios específicos realizados en una orden. |
| `TServicios` | Catálogo de servicios disponibles (Mantenimiento, frenos, etc). |

## Auditoría y Seguridad
- Todas las acciones (creación, edición, desactivación) se registran automáticamente en `TAuditoriaGeneral` mediante el `AuditoriaTrait`.
- El acceso al historial técnico está limitado a Administradores y Mecánicos para proteger la privacidad de los datos del cliente.
- El módulo de Reportes es exclusivo para el rol **Administrador (1)**.
