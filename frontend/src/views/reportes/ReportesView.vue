<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/services/api'

const tipo = ref('clientes')
const data = ref([])
const loading = ref(false)
const error = ref(null)

async function cargarDatos() {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(`/reportes/data?tipo=${tipo.value}`)
    data.value = res.data.data
    animarTabla()
  } catch (err) {
    error.value = 'Error al cargar los datos del reporte'
  } finally {
    loading.value = false
  }
}

function animarTabla() {
  gsap.fromTo('.tabla-wrapper', 
    { opacity: 0, y: 10 }, 
    { opacity: 1, y: 0, duration: 0.4 }
  )
}

function exportarPdf() {
  window.open(`${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/reportes/pdf?tipo=${tipo.value}`, '_blank')
}

onMounted(cargarDatos)

watch(tipo, cargarDatos)
</script>

<template>
  <div class="reportes-page">
    <header class="page-header">
      <div>
        <h1>Reportes</h1>
        <p class="subtitle">Visualiza y exporta información clave de clientes y motocicletas.</p>
      </div>
    </header>

    <div class="control-card">
      <div class="selector-tipo">
        <button 
          class="btn-tab" 
          :class="{ active: tipo === 'clientes' }"
          @click="tipo = 'clientes'"
        >
          Clientes y Motos
        </button>
        <button 
          class="btn-tab" 
          :class="{ active: tipo === 'motos' }"
          @click="tipo = 'motos'"
        >
          Registro de Motocicletas
        </button>
      </div>
      
      <button class="btn-export" @click="exportarPdf" :disabled="loading || data.length === 0">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon">
          <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Exportar PDF
      </button>
    </div>

    <div v-if="error" class="mensaje-error">{{ error }}</div>

    <div class="content-card">
      <div v-if="loading" class="cargando">Generando vista previa...</div>
      
      <div v-else class="tabla-wrapper">
        <table class="tabla-reporte">
          <thead>
            <tr v-if="tipo === 'clientes'">
              <th>Cliente</th>
              <th>Documento</th>
              <th>Teléfono</th>
              <th class="txt-centro">Motos registradas</th>
            </tr>
            <tr v-else>
              <th>Placa</th>
              <th>Vehículo</th>
              <th>Propietario</th>
              <th>Características</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" :key="item.id_cliente || item.id_motocicleta">
              <template v-if="tipo === 'clientes'">
                <td><strong>{{ item.primer_nombre }} {{ item.apellido_paterno }}</strong></td>
                <td>{{ item.ci }} {{ item.nit ? `(NIT: ${item.nit})` : '' }}</td>
                <td>{{ item.telefono || '—' }}</td>
                <td class="txt-centro">
                  <span class="badge-motos">{{ item.motocicletas_count }}</span>
                </td>
              </template>
              <template v-else>
                <td><span class="placa">{{ item.placa }}</span></td>
                <td>{{ item.marca }} {{ item.modelo }} ({{ item.anio }})</td>
                <td>{{ item.cliente ? `${item.cliente.primer_nombre} ${item.cliente.apellido_paterno}` : '—' }}</td>
                <td>{{ item.color }} / {{ item.cilindrada || '—' }}</td>
              </template>
            </tr>
            <tr v-if="data.length === 0">
              <td colspan="4" class="sin-datos">No hay datos para mostrar</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.reportes-page {
  padding: 32px;
  max-width: 1100px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 28px;
}

.page-header h1 {
  font-size: 26px;
  color: #042D29;
  margin-bottom: 6px;
}

.subtitle {
  color: #929079;
}

.control-card {
  background: #fff;
  border-radius: 16px;
  padding: 16px 24px;
  margin-bottom: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.selector-tipo {
  display: flex;
  background: #f5f4f0;
  padding: 4px;
  border-radius: 12px;
}

.btn-tab {
  border: none;
  background: transparent;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  color: #929079;
  transition: all 0.2s;
}

.btn-tab.active {
  background: #fff;
  color: #042D29;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-export {
  background: #042D29;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-export:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.icon {
  width: 18px;
  height: 18px;
}

.content-card {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.tabla-reporte {
  width: 100%;
  border-collapse: collapse;
}

.tabla-reporte th {
  text-align: left;
  color: #929079;
  font-size: 13px;
  padding: 12px;
  border-bottom: 2px solid #f5f4f0;
}

.tabla-reporte td {
  padding: 16px 12px;
  border-bottom: 1px solid #f5f4f0;
  color: #1F2937;
  font-size: 14px;
}

.txt-centro { text-align: center; }

.badge-motos {
  background: #042D29;
  color: #fff;
  padding: 4px 12px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 12px;
}

.placa {
  background: #FEF3C7;
  color: #92400E;
  font-weight: 800;
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid #FCD34D;
  font-family: monospace;
}

.cargando, .sin-datos {
  text-align: center;
  padding: 40px;
  color: #929079;
}

.mensaje-error {
  background: #FFF5F5;
  color: #741102;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  border-left: 4px solid #741102;
}
</style>
