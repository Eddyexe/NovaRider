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
  fecha_nacimiento: '',
  telefono: '',
  nit: '',
  direccion: '',
})

const esEdicion = computed(() => !!props.cliente)

const calcularEdad = (fecha) => {
  if (!fecha) return 0
  const hoy = new Date()
  const nacimiento = new Date(fecha)
  let edad = hoy.getFullYear() - nacimiento.getFullYear()
  const mes = hoy.getMonth() - nacimiento.getMonth()
  if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
    edad--
  }
  return edad
}

const soloNumeros = (e) => {
  if (!/^\d$/.test(e.key)) {
    e.preventDefault()
  }
}

const soloLetras = (e) => {
  if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(e.key)) {
    e.preventDefault()
  }
}

const validarCampo = (campo, valor) => {
  switch (campo) {
    case 'ci':
      if (!valor) errores.value.ci = 'La cédula de identidad es obligatoria'
      else if (!/^\d+$/.test(valor)) errores.value.ci = 'La cédula debe contener solo números'
      else errores.value.ci = ''
      break
    case 'primer_nombre':
      if (!valor) errores.value.primer_nombre = 'El nombre es obligatorio'
      else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor)) errores.value.primer_nombre = 'Solo se permiten letras'
      else errores.value.primer_nombre = ''
      break
    case 'apellido_paterno':
      if (!valor) errores.value.apellido_paterno = 'El apellido paterno es obligatorio'
      else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor)) errores.value.apellido_paterno = 'Solo se permiten letras'
      else errores.value.apellido_paterno = ''
      break
    case 'apellido_materno':
      if (valor && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor)) {
        errores.value.apellido_materno = 'Solo se permiten letras'
      } else {
        errores.value.apellido_materno = ''
      }
      break
    case 'fecha_nacimiento':
      if (!valor) {
        errores.value.fecha_nacimiento = 'La fecha de nacimiento es obligatoria'
      } else {
        const edad = calcularEdad(valor)
        if (edad < 18) {
          errores.value.fecha_nacimiento = 'El cliente debe ser mayor de 18 años'
        } else {
          errores.value.fecha_nacimiento = ''
        }
      }
      break
    case 'telefono':
      if (valor && !/^\d{8}$/.test(valor)) {
        errores.value.telefono = 'El teléfono debe tener exactamente 8 dígitos'
      } else {
        errores.value.telefono = ''
      }
      break
    case 'nit':
      if (valor) {
        if (!/^\d+$/.test(valor)) {
          errores.value.nit = 'El NIT debe contener solo números'
        } else if (valor.length < 3) {
          errores.value.nit = 'El NIT debe tener al menos 3 dígitos'
        } else if (valor.length > 15) {
          errores.value.nit = 'El NIT no puede exceder los 15 dígitos'
        } else {
          errores.value.nit = ''
        }
      } else {
        errores.value.nit = ''
      }
      break
    case 'direccion':
      if (valor && valor.length > 100) {
        errores.value.direccion = 'La dirección no puede exceder los 100 caracteres'
      } else {
        errores.value.direccion = ''
      }
      break
  }
}

watch(
  () => form.value,
  (nuevoForm) => {
    Object.keys(nuevoForm).forEach(campo => {
      validarCampo(campo, nuevoForm[campo])
    })
  },
  { deep: true }
)

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
  Object.keys(form.value).forEach(campo => {
    validarCampo(campo, form.value[campo])
  })

  const hayErrores = Object.values(errores.value).some(error => error !== '')
  if (hayErrores) {
    const listaErrores = Object.values(errores.value).filter(e => e !== '').join('\n')
    alert('Por favor corrija los siguientes errores:\n\n' + listaErrores)
    return false
  }

  return true
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
            <input id="ci" v-model="form.ci" type="text" @keypress="soloNumeros" maxlength="15" />
            <p v-if="errores.ci" class="field-error">{{ errores.ci }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.primer_nombre }">
            <label for="primer_nombre">Primer Nombre</label>
            <input id="primer_nombre" v-model="form.primer_nombre" type="text" @keypress="soloLetras" />
            <p v-if="errores.primer_nombre" class="field-error">{{ errores.primer_nombre }}</p>
          </div>
          <div class="campo">
            <label for="segundo_nombre">Segundo Nombre</label>
            <input id="segundo_nombre" v-model="form.segundo_nombre" type="text" @keypress="soloLetras" />
          </div>
          <div class="campo" :class="{ 'has-error': errores.apellido_paterno }">
            <label for="apellido_paterno">Apellido Paterno</label>
            <input id="apellido_paterno" v-model="form.apellido_paterno" type="text" @keypress="soloLetras" />
            <p v-if="errores.apellido_paterno" class="field-error">{{ errores.apellido_paterno }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.apellido_materno }">
            <label for="apellido_materno">Apellido Materno</label>
            <input id="apellido_materno" v-model="form.apellido_materno" type="text" @keypress="soloLetras" />
            <p v-if="errores.apellido_materno" class="field-error">{{ errores.apellido_materno }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.fecha_nacimiento }">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input id="fecha_nacimiento" v-model="form.fecha_nacimiento" type="date" />
            <p v-if="errores.fecha_nacimiento" class="field-error">{{ errores.fecha_nacimiento }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.telefono }">
            <label for="telefono">Teléfono</label>
            <input id="telefono" v-model="form.telefono" type="text" @keypress="soloNumeros" maxlength="8" />
            <p v-if="errores.telefono" class="field-error">{{ errores.telefono }}</p>
          </div>
          <div class="campo" :class="{ 'has-error': errores.nit }">
            <label for="nit">NIT</label>
            <input id="nit" v-model="form.nit" type="text" @keypress="soloNumeros" maxlength="15" />
            <p v-if="errores.nit" class="field-error">{{ errores.nit }}</p>
          </div>
          <div class="campo campo-doble" :class="{ 'has-error': errores.direccion }">
            <div class="label-with-counter">
              <label for="direccion">Dirección</label>
              <span class="counter" :class="{ 'counter-error': form.direccion?.length > 100 }">
                {{ form.direccion?.length || 0 }}/100
              </span>
            </div>
            <textarea id="direccion" v-model="form.direccion" rows="3" maxlength="100"></textarea>
            <p v-if="errores.direccion" class="field-error">{{ errores.direccion }}</p>
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

.label-with-counter {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.counter {
  font-size: 11px;
  color: #6B7280;
}

.counter-error {
  color: #741102;
  font-weight: bold;
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
  margin-top: 28px;
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
