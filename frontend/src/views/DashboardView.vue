<script setup>
import { onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const modulos = [
  {
    nombre: 'Gesti&oacute;n de Usuarios',
    descripcion: 'Administrar usuarios del sistema',
    ruta: '/usuarios',
    color: '#741102',
  },
  {
    nombre: 'Clientes y Veh&iacute;culos',
    descripcion: 'Registro de clientes y motocicletas',
    ruta: null,
    color: '#042D29',
  },
  {
    nombre: 'Taller y Reparaciones',
    descripcion: '&Oacute;rdenes de trabajo y seguimiento',
    ruta: null,
    color: '#042D29',
  },
  {
    nombre: 'Inventario',
    descripcion: 'Productos, repuestos y stock',
    ruta: null,
    color: '#042D29',
  },
  {
    nombre: 'Ventas y Caja',
    descripcion: 'Registro de ventas y arqueo de caja',
    ruta: null,
    color: '#042D29',
  },
  {
    nombre: 'Compras y Reportes',
    descripcion: 'Proveedores, reservas y empleados',
    ruta: null,
    color: '#042D29',
  },
]

onMounted(async () => {
  await nextTick()

  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } })

  tl.from('.dashboard-header', { y: -30, opacity: 0, duration: 0.5 })
    .from('.welcome-section', { y: 20, opacity: 0, duration: 0.4 }, '-=0.1')
    .from('.modulo-card', { y: 30, opacity: 0, duration: 0.4, stagger: 0.08 }, '-=0.1')
})

async function cerrarSesion() {
  await auth.logout()
  router.push('/login')
}

function cardEnter(e) {
  gsap.to(e.currentTarget, { y: -4, boxShadow: '0 12px 32px rgba(0,0,0,0.12)', duration: 0.25, ease: 'power2.out' })
}

function cardLeave(e) {
  gsap.to(e.currentTarget, { y: 0, boxShadow: '0 2px 12px rgba(0,0,0,0.06)', duration: 0.25, ease: 'power2.out' })
}
</script>

<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <div class="header-left">
        <!--
          LOGO PNG: Reemplazar este placeholder por:
          <img src="/ruta-del-logo.png" alt="NovaRider" class="header-logo-img" />
        -->
        <div class="header-logo-placeholder">
          <svg viewBox="0 0 48 48" fill="none" class="header-logo-svg">
            <circle cx="24" cy="24" r="22" stroke="#929079" stroke-width="2" fill="none" />
            <circle cx="24" cy="24" r="8" fill="#FFFFFF" opacity="0.2" />
            <path d="M24 4v6M24 38v6M4 24h6M38 24h6" stroke="#929079" stroke-width="1.5" stroke-linecap="round" />
            <circle cx="24" cy="24" r="3" fill="#741102" />
          </svg>
        </div>
        <span class="header-title">NovaRider</span>
      </div>

      <div class="header-right">
        <span class="header-rol-badge" :class="'rol-' + (auth.user?.rol || '').toLowerCase()">
          {{ auth.user?.rol }}
        </span>
        <button class="btn-logout" @click="cerrarSesion">Cerrar sesi&oacute;n</button>
      </div>
    </header>

    <main class="dashboard-content">
      <div class="welcome-section">
        <h1 class="welcome-title">Bienvenido, {{ auth.user?.username }}</h1>
        <p class="welcome-subtitle">Panel de administraci&oacute;n &mdash; NovaRider</p>
      </div>

      <section class="modulos-section">
        <div class="modulos-grid">
          <component
            :is="m.ruta ? 'router-link' : 'div'"
            v-for="m in modulos"
            :key="m.nombre"
            :to="m.ruta || undefined"
            class="modulo-card"
            :class="{ 'modulo-card-link': !!m.ruta }"
            @mouseenter="cardEnter"
            @mouseleave="cardLeave"
          >
            <div class="modulo-card-accent" :style="{ background: m.color }" />
            <div class="modulo-card-body">
              <div class="modulo-icon-wrapper">
                <svg v-if="m.ruta" viewBox="0 0 24 24" fill="none" class="modulo-icon">
                  <circle cx="12" cy="8" r="3.5" stroke="#042D29" stroke-width="1.5" />
                  <path d="M3 20c0-4.5 4-8 9-8s9 3.5 9 8" stroke="#042D29" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" class="modulo-icon">
                  <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="#929079" stroke-width="1.5" />
                  <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="#929079" stroke-width="1.5" />
                  <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="#929079" stroke-width="1.5" />
                  <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="#929079" stroke-width="1.5" />
                </svg>
              </div>
              <span class="modulo-nombre" v-html="m.nombre" />
              <span class="modulo-desc" v-html="m.descripcion" />
              <span v-if="m.ruta" class="modulo-flecha">&rarr;</span>
            </div>
          </component>
        </div>
      </section>
    </main>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: #F5F4F0;
}

.dashboard {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ── Header ── */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  background: #042D29;
  color: #FFFFFF;
  position: sticky;
  top: 0;
  z-index: 50;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-logo-placeholder {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  /* Reemplazar por:
  .header-logo-img { width: 48px; height: auto; }
  */
}

.header-logo-svg {
  width: 100%;
  height: 100%;
}

.header-title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: -0.3px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-rol-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.rol-administrador { background: rgba(116, 17, 2, 0.2); color: #FFFFFF; }
.rol-cajero { background: rgba(146, 144, 121, 0.25); color: #FFFFFF; }
.rol-mecanico { background: rgba(4, 45, 41, 0.3); color: #FFFFFF; }
.rol-recepcionista { background: rgba(146, 144, 121, 0.2); color: #FFFFFF; }

.btn-logout {
  padding: 6px 16px;
  background: transparent;
  color: #FFFFFF;
  border: 1.5px solid #929079;
  border-radius: 8px;
  font-size: 13px;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-logout:hover {
  background: #741102;
  border-color: #741102;
}

/* ── Content ── */
.dashboard-content {
  flex: 1;
  padding: 40px 32px;
  max-width: 1100px;
  margin: 0 auto;
  width: 100%;
}

.welcome-section {
  margin-bottom: 40px;
}

.welcome-title {
  font-size: 28px;
  font-weight: 700;
  color: #042D29;
  margin-bottom: 4px;
}

.welcome-subtitle {
  font-size: 15px;
  color: #929079;
  font-weight: 400;
}

/* ── Modulos Grid ── */
.modulos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.modulo-card {
  background: #FFFFFF;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  text-decoration: none;
  cursor: default;
  display: flex;
  flex-direction: column;
}

.modulo-card-link {
  cursor: pointer;
}

.modulo-card-accent {
  height: 4px;
  flex-shrink: 0;
}

.modulo-card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
  position: relative;
}

.modulo-icon-wrapper {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 4px;
}

.modulo-icon {
  width: 28px;
  height: 28px;
}

.modulo-nombre {
  font-size: 16px;
  font-weight: 600;
  color: #042D29;
  line-height: 1.3;
}

.modulo-desc {
  font-size: 13px;
  color: #929079;
  line-height: 1.4;
}

.modulo-flecha {
  position: absolute;
  bottom: 20px;
  right: 20px;
  font-size: 18px;
  color: #929079;
  transition: transform 0.2s ease;
}

.modulo-card-link:hover .modulo-flecha {
  transform: translateX(4px);
}
</style>
