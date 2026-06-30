# NovaRider — MODULO 2: Vehículos, Clientes e Historial

Este módulo gestiona la relación entre los clientes y sus motocicletas, manteniendo un registro histórico detallado de cada intervención técnica realizada en el taller.

## Funcionalidades Principales

- **Gestión de Clientes:** CRUD completo para clientes, incluyendo validación de CI/NIT único y gestión de estado (Activo/Inactivo).
- **Registro de Motocicletas:** Vinculación de vehículos a clientes con campos técnicos detallados (Nro. Chasis, Nro. Motor, Cilindrada, Color).
- **Historial de Servicios por Vehículo:** Línea de tiempo visual que muestra todas las Órdenes de Trabajo (OT) pasadas de una motocicleta específica.
- **Acceso Rápido a Reportes:** Botón "Reportes" integrado directamente en los módulos de Clientes y Motocicletas para una navegación fluida.
- **Reporte Global del Sistema:** Módulo centralizado con un Dashboard de métricas clave (Ingresos, Stock Crítico, Ventas del mes) y reportes detallados de Personal, Inventario y Ventas.
- **Exportación a PDF:** Generación de reportes profesionales tanto específicos por módulo como generales del sistema.

## Arquitectura Técnica

### Backend (Laravel)
- **Modelos:** 
  - `Cliente.php`, `Motocicleta.php`, `User.php`, `Producto.php`, `Venta.php`.
- **Controladores:**
  - `ReporteController.php`: Ahora incluye métodos `systemStats()` para el dashboard y `exportGlobalReport()` para el PDF resumido.
- **Vistas PDF:** 
  - `reportes/pdf.blade.php`: Plantilla dinámica para reportes tabulares.
  - `reportes/pdf_global.blade.php`: Plantilla de resumen ejecutivo con indicadores clave.

### Frontend (Vue 3)
- **Vistas:**
  - `reportes/ReportesView.vue`: Rediseñada como un Centro de Reportes Global con Dashboard y selectores para todos los módulos del sistema.
  - `clientes/ClientesView.vue` y `motocicletas/MotocicletasView.vue`: Actualizadas con botones de acceso directo al Centro de Reportes.
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
