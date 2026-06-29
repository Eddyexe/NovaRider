# NovaRider — MODULO 3: Taller y Certificación de Calidad

Este módulo centraliza la ejecución, seguimiento y validación técnica de las intervenciones mecánicas, garantizando estándares de calidad mediante una lista de verificación obligatoria antes de la entrega del vehículo.

## Funcionalidades Principales

- **Gestión de Órdenes de Trabajo (OT):** Registro, seguimiento y control de estado de las motocicletas en el taller (Pendiente, En Proceso, Listo, Entregado).
- **Checklist de Calidad (HU-12):** Sistema de verificación técnica obligatoria que valida frenos, componentes eléctricos, pruebas de funcionamiento y evaluación dinámica.
- **Control de Kilometraje:** Registro preciso del odómetro antes y después de la intervención para el historial de mantenimiento.
- **Certificación de Entrega:** Validación de calidad que dispara el cambio de estado del vehículo a "Listo para entrega".
- **Exportación a PDF:** Generación de OTs profesionales con detalle de trabajos realizados y checklist de seguridad.

## Arquitectura Técnica

### Backend (Laravel)
- **Modelos:** - `OrdenTrabajo.php`, `DetalleOrdenTrabajo.php`, `ListaVerificacion.php`.
- **Controladores:**
  - `OrdenController.php`: Gestión de estados, persistencia de checklist y generación de reportes de taller.
- **Vistas PDF:** - `reportes/ordenes_pdf.blade.php`: Plantilla dinámica para la formalización de la OT al cliente.

### Frontend (Vue 3)
- **Vistas:**
  - `taller/TallerView.vue`: Listado centralizado con filtros de estado y acceso a acciones rápidas.
  - `taller/ChecklistCalidad.vue`: Componente interactivo (formulario con switches) para la validación técnica.
- **Servicios:**
  - `tallerService.js`: Encapsula la comunicación con la API para órdenes, cambios de estado y persistencia del checklist.
- **Estado (Pinia):** - `tallerStore.js`: Gestión reactiva de las órdenes activas y sincronización en tiempo real con el servidor.

## Base de Datos (Tablas Involucradas)

| Tabla | Propósito |
|---|---|
| `TOrdenesTrabajo` | Cabecera: ID, Cliente, Mecánico, Fechas, Estado actual. |
| `TDetallesOrdenTrabajo` | Servicios específicos y mano de obra aplicada en cada orden. |
| `tlistasverificacion` | Checklist técnico: Valores de revisión, kilometraje y fecha. |
| `TServicios` | Catálogo de precios y tiempos estándar por tipo de servicio. |

## Auditoría y Seguridad
- **Trazabilidad:** Cada cambio de estado queda registrado en `TAuditoriaGeneral` mediante el `AuditoriaTrait`, identificando al responsable.
- **Validación:** El sistema restringe la entrega si el `Checklist de Calidad` no está completado satisfactoriamente.
- **Roles:** El módulo es accesible para mecánicos (gestión operativa) y administradores (gestión total y reportes).