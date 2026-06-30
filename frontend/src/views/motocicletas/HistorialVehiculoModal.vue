<script setup>
import { ref, onMounted } from 'vue'
import { useMotocicletasStore } from '@/stores/motocicletasStore'

const props = defineProps({
  motocicleta: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['cerrar'])

const store = useMotocicletasStore()
const historial = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const data = await store.obtenerHistorial(props.motocicleta.id_motocicleta)
    historial.value = data.historial
    
    // Animación de entrada
    gsap.fromTo('.modal-content', 
      { scale: 0.9, opacity: 0 }, 
      { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.7)' }
    )
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

function cerrar() {
  gsap.to('.modal-content', {
    scale: 0.9,
    opacity: 0,
    duration: 0.2,
    onComplete: () => emit('cerrar')
  })
}

function formatearFecha(fecha) {
  if (!fecha) return '—'
  return new Date(fecha).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

function formatearMoneda(valor) {
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: 'BOB'
  }).format(valor)
}
</script>

<template>
  <div class="modal-overlay" @click.self="cerrar">
    <div class="modal-content">
      <header class="modal-header">
        <div>
          <h2>Historial de Servicios</h2>
          <p class="subtitle">{{ motocicleta.marca }} {{ motocicleta.modelo }} — {{ motocicleta.placa }}</p>
        </div>
        <button class="btn-cerrar" @click="cerrar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>
      </header>

      <div class="modal-body">
        <div v-if="loading" class="cargando">
          Cargando historial...
        </div>
        
        <div v-else-if="historial.length === 0" class="sin-historial">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="icon-empty">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <p>No se encontraron servicios previos para esta motocicleta.</p>
        </div>

        <div v-else class="linea-tiempo">
          <div v-for="orden in historial" :key="orden.id_orden" class="evento">
            <div class="evento-marker"></div>
            <div class="evento-contenido">
              <div class="evento-header">
                <span class="fecha">{{ formatearFecha(orden.fecha_ingreso) }}</span>
                <span class="nro-orden">OT #{{ orden.nro_orden }}</span>
                <span class="estado" :class="orden.estado.toLowerCase()">{{ orden.estado }}</span>
              </div>
              
              <div class="detalles-orden">
                <p v-if="orden.condicion_entrada" class="condicion">
                  <strong>Condición:</strong> {{ orden.condicion_entrada }}
                </p>
                
                <table class="tabla-servicios">
                  <thead>
                    <tr>
                      <th>Servicio</th>
                      <th>Descripción</th>
                      <th class="txt-derecha">Precio</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="detalle in orden.detalles" :key="detalle.id_detalle_ot">
                      <td>{{ detalle.servicio?.nombre || 'General' }}</td>
                      <td>{{ detalle.descripcion || '—' }}</td>
                      <td class="txt-derecha">{{ formatearMoneda(detalle.subtotal) }}</td>
                    </tr>
                  </tbody>
                </table>
                
                <div class="evento-footer">
                  <span>Atendido por: {{ orden.empleado?.primer_nombre }} {{ orden.empleado?.apellido_paterno }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #fff;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  border-radius: 18px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
  border-top: 4px solid #042D29;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  color: #042D29;
}

.subtitle {
  margin: 4px 0 0;
  color: #929079;
  font-size: 14px;
}

.btn-cerrar {
  background: #f5f4f0;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #929079;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.cargando, .sin-historial {
  text-align: center;
  padding: 40px;
  color: #929079;
}

.icon-empty {
  width: 48px;
  height: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

/* Timeline style */
.linea-tiempo {
  position: relative;
  padding-left: 24px;
}

.linea-tiempo::before {
  content: '';
  position: absolute;
  left: 7px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #E5E7EB;
}

.evento {
  position: relative;
  margin-bottom: 32px;
}

.evento-marker {
  position: absolute;
  left: -24px;
  top: 6px;
  width: 16px;
  height: 16px;
  background: #042D29;
  border: 4px solid #fff;
  border-radius: 50%;
  box-shadow: 0 0 0 2px #042D29;
  z-index: 1;
}

.evento-contenido {
  background: #F9FAFB;
  border-radius: 12px;
  padding: 16px;
  border: 1px solid #E5E7EB;
}

.evento-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.fecha {
  font-weight: 700;
  color: #042D29;
}

.nro-orden {
  color: #929079;
  font-size: 13px;
  background: #fff;
  padding: 2px 8px;
  border-radius: 6px;
  border: 1px solid #E5E7EB;
}

.estado {
  font-size: 11px;
  text-transform: uppercase;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 4px;
}

.estado.entregado, .estado.finalizado { background: #DEF7EC; color: #03543F; }
.estado.pendiente { background: #FEF3C7; color: #92400E; }
.estado.proceso { background: #E1EFFE; color: #1E429F; }

.condicion {
  font-size: 13px;
  color: #4B5563;
  margin-bottom: 12px;
  padding: 8px;
  background: #fff;
  border-radius: 6px;
}

.tabla-servicios {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  margin-bottom: 12px;
}

.tabla-servicios th {
  text-align: left;
  color: #929079;
  font-weight: 600;
  padding: 4px 8px;
}

.tabla-servicios td {
  padding: 8px;
  border-top: 1px solid #E5E7EB;
}

.txt-derecha { text-align: right; }

.evento-footer {
  font-size: 12px;
  color: #929079;
  border-top: 1px dashed #D1D5DB;
  padding-top: 8px;
  display: flex;
  justify-content: flex-end;
}
</style>
