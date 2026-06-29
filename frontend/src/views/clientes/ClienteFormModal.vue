<script setup>
import { ref, watch, computed } from 'vue'
import { useClientesStore } from '@/stores/clientesStore'

const props = defineProps({
  cliente: { type: Object, default: null },
})
const emit = defineEmits(['cerrar'])

const store = useClientesStore()
const enviando = ref(false)
const mensajeError = ref('')

const form = ref({
  ci: props.cliente?.ci || '',
  primer_nombre: props.cliente?.primer_nombre || '',
  segundo_nombre: props.cliente?.segundo_nombre || '',
  apellido_paterno: props.cliente?.apellido_paterno || '',
  apellido_materno: props.cliente?.apellido_materno || '',
  fecha_nacimiento: props.cliente?.fecha_nacimiento || '',
  telefono: props.cliente?.telefono || '',
  nit: props.cliente?.nit || '',
  direccion: props.cliente?.direccion || '',
})

const errores = ref({
  ci: '',
  primer_nombre: '',
  apellido_paterno: '',
  telefono: '',
  nit: '',
  direccion: '',
})

const esEdicion = computed(() => !!props.cliente)

watch(
  () => props.cliente,
  (nuevo) => {
    form.value = {
      ci: nuevo?.ci || '',
      primer_nombre: nuevo?.primer_nombre || '',
      segundo_nombre: nuevo?.segundo_nombre || '',
      apellido_paterno: nuevo?.apellido_paterno || '',
      apellido_materno: nuevo?.apellido_materno || '',
      fecha_nacimiento: nuevo?.fecha_nacimiento || '',
      telefono: nuevo?.telefono || '',
      nit: nuevo?.nit || '',
      direccion: nuevo?.direccion || '',
    }
    limpiarErrores()
    mensajeError.value = ''
  },
  { immediate: true }
)

function limpiarErrores() {
  Object.keys(errores.value).forEach((key) => {
    errores.value[key] = ''
  })
}

function validar() {
  let valido = true
  limpiarErrores()

  if (!form.value.ci) {
    errores.value.ci = 'La cédula de identidad es obligatoria'
    valido = false
  } else if (!/^\d+$/.test(form.value.ci)) {
    errores.value.ci = 'Debe contener solo números'
    valido = false
  } else if (form.value.ci.length < 5 || form.value.ci.length > 9) {
    errores.value.ci = 'Debe tener entre 5 y 9 dígitos'
    valido = false
  }

  if (!form.value.primer_nombre || form.value.primer_nombre.length < 2) {
    errores.value.primer_nombre = 'Mínimo 2 caracteres'
    valido = false
  } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.value.primer_nombre)) {
    errores.value.primer_nombre = 'Solo debe contener letras'
    valido = false
  }

  if (form.value.segundo_nombre && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.value.segundo_nombre)) {
    mensajeError.value = 'El segundo nombre solo debe contener letras'
    valido = false
  }

  if (!form.value.apellido_paterno || form.value.apellido_paterno.length < 2) {
    errores.value.apellido_paterno = 'Mínimo 2 caracteres'
    valido = false
  } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.value.apellido_paterno)) {
    errores.value.apellido_paterno = 'Solo debe contener letras'
    valido = false
  }

  if (form.value.apellido_materno && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.value.apellido_materno)) {
    mensajeError.value = 'El apellido materno solo debe contener letras'
    valido = false
  }

  if (form.value.telefono && !/^\d{8}$/.test(form.value.telefono)) {
    errores.value.telefono = 'Debe tener 8 dígitos'
    valido = false
  }

  if (form.value.nit) {
    if (!/^\d+$/.test(form.value.nit)) {
      errores.value.nit = 'Solo debe contener números'
      valido = false
    } else if (form.value.nit.length < 3) {
      errores.value.nit = 'El NIT debe tener al menos 3 caracteres'
      valido = false
    }
  }

  if (form.value.direccion && form.value.direccion.length > 500) {
    errores.value.direccion = 'Máximo 500 caracteres'
    valido = false
  }

  return valido
}

async function guardar() {
  mensajeError.value = ''

  if (!validar()) {
    return
  }

  enviando.value = true

  try {
    if (esEdicion.value) {
      await store.actualizar(props.cliente.id_cliente, form.value)
    } else {
      await store.crear(form.value)
    }
    emit('cerrar')
  } catch (error) {
    mensajeError.value = error.response?.data?.message || 'Error al guardar cliente'
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <div class="modal-overlay" @click.self="emit('cerrar')">
    <div class="modal-card">
      <div class="modal-header">
        <h2>{{ esEdicion ? 'Editar Cliente' : 'Nuevo Cliente' }}</h2>
        <button class="btn-cerrar" @click="emit('cerrar')">×</button>
      </div>

      <form @submit.prevent="guardar" class="modal-form">
        <p v-if="mensajeError" class="mensaje-error">{{ mensajeError }}</p>

        <div class="form-grid">
          <div class="campo" :class="{ 'has-error': errores.ci }">
            <label for="ci">Cédula de Identidad</label>
            <input id="ci" v-model="form.ci" type="text" />
            <p v-if="errores.ci" class="field-error">{{ errores.ci }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.primer_nombre }">
            <label for="primer_nombre">Primer Nombre</label>
            <input id="primer_nombre" v-model="form.primer_nombre" type="text" />
            <p v-if="errores.primer_nombre" class="field-error">{{ errores.primer_nombre }}</p>
          </div>
          <div class="campo">
            <label for="segundo_nombre">Segundo Nombre</label>
            <input id="segundo_nombre" v-model="form.segundo_nombre" type="text" />
          </div>
          <div class="campo" :class="{ 'has-error': errores.apellido_paterno }">
            <label for="apellido_paterno">Apellido Paterno</label>
            <input id="apellido_paterno" v-model="form.apellido_paterno" type="text" />
            <p v-if="errores.apellido_paterno" class="field-error">{{ errores.apellido_paterno }}</p>
          </div>
          <div class="campo">
            <label for="apellido_materno">Apellido Materno</label>
            <input id="apellido_materno" v-model="form.apellido_materno" type="text" />
          </div>
          <div class="campo">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input id="fecha_nacimiento" v-model="form.fecha_nacimiento" type="date" />
          </div>
          <div class="campo" :class="{ 'has-error': errores.telefono }">
            <label for="telefono">Teléfono</label>
            <input id="telefono" v-model="form.telefono" type="text" />
            <p v-if="errores.telefono" class="field-error">{{ errores.telefono }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.nit }">
            <label for="nit">NIT</label>
            <input id="nit" v-model="form.nit" type="text" />
            <p v-if="errores.nit" class="field-error">{{ errores.nit }}</p>
          </div>
          <div class="campo campo-doble">
            <label for="direccion">Dirección</label>
            <textarea id="direccion" v-model="form.direccion" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancelar" @click="emit('cerrar')">Cancelar</button>
          <button type="submit" class="btn-guardar" :disabled="enviando">
            {{ enviando ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 110;
  padding: 16px;
}

.modal-card {
  width: 100%;
  max-width: 620px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 18px 48px rgba(0, 0, 0, 0.12);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 22px 24px;
  border-bottom: 1px solid #E5E7EB;
}

.modal-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #042D29;
}

.btn-cerrar {
  background: transparent;
  border: none;
  font-size: 26px;
  color: #6B7280;
  cursor: pointer;
}

.modal-form {
  padding: 24px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.campo {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.campo-doble {
  grid-column: span 2;
}

.campo label {
  font-size: 13px;
  font-weight: 600;
  color: #042D29;
}

.campo input,
.campo textarea {
  border: 1.5px solid #D1D5DB;
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 14px;
  color: #1F2937;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.campo input:focus,
.campo textarea:focus {
  border-color: #042D29;
  box-shadow: 0 0 0 4px rgba(4, 45, 41, 0.08);
}

.field-error {
  color: #741102;
  font-size: 13px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 0 24px 24px;
}

.btn-cancelar,
.btn-guardar {
  border: none;
  border-radius: 10px;
  padding: 12px 18px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cancelar {
  background: #F5F4F0;
  color: #042D29;
}

.btn-guardar {
  background: #042D29;
  color: #FFFFFF;
}

.btn-guardar:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.mensaje-error {
  margin-bottom: 16px;
  padding: 14px 16px;
  background: #FFF5F5;
  border: 1px solid #F1A8A8;
  color: #741102;
  border-radius: 12px;
}
</style>
