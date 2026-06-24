<script setup>
defineProps({
  cliente: { type: Object, required: true },
})
const emit = defineEmits(['confirmar', 'cancelar'])
</script>

<template>
  <div class="modal-overlay" @click.self="emit('cancelar')">
    <div class="modal-confirmar">
      <h3>Desactivar Cliente</h3>
      <p>
        ¿Estás seguro de desactivar al cliente
        <strong>{{ cliente?.primer_nombre }} {{ cliente?.apellido_paterno }}</strong>?
      </p>
      <p class="nota">El cliente no podrá registrarse nuevamente hasta que sea reactivado.</p>
      <div class="acciones">
        <button class="btn-cancelar" @click="emit('cancelar')">Cancelar</button>
        <button class="btn-confirmar" @click="emit('confirmar')">Desactivar</button>
      </div>
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

.modal-confirmar {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  border-radius: 16px;
  padding: 26px;
  box-shadow: 0 18px 48px rgba(0, 0, 0, 0.12);
  text-align: center;
}

.modal-confirmar h3 {
  font-size: 20px;
  font-weight: 700;
  color: #042D29;
  margin-bottom: 12px;
}

.modal-confirmar p {
  color: #6B7280;
  font-size: 14px;
  margin-bottom: 10px;
  line-height: 1.6;
}

.nota {
  font-style: italic;
  margin-bottom: 20px;
}

.acciones {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.btn-cancelar,
.btn-confirmar {
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

.btn-confirmar {
  background: #741102;
  color: #FFFFFF;
}

.btn-confirmar:hover {
  background: #8C1503;
}
</style>
