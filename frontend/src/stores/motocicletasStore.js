import { ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useMotocicletasStore = defineStore('motocicletas', () => {
  const motocicletas = ref([])
  const motocicletasInactivas = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function listar(id_cliente = null) {
    loading.value = true
    error.value = null
    try {
      const url = id_cliente ? `/motocicletas?id_cliente=${id_cliente}` : '/motocicletas'
      const res = await api.get(url)
      motocicletas.value = res.data.motocicletas
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al cargar motocicletas'
    } finally {
      loading.value = false
    }
  }

  async function listarInactivas() {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/motocicletas?inactivos=1')
      motocicletasInactivas.value = res.data.motocicletas
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al cargar motocicletas inactivas'
    } finally {
      loading.value = false
    }
  }

  async function crear(data) {
    const res = await api.post('/motocicletas', data)
    motocicletas.value.push(res.data.motocicleta)
    return res.data
  }

  async function actualizar(id, data) {
    const res = await api.put(`/motocicletas/${id}`, data)
    const idx = motocicletas.value.findIndex((m) => m.id_motocicleta === id)
    if (idx !== -1) {
      motocicletas.value[idx] = res.data.motocicleta
    }
    return res.data
  }

  async function eliminar(id) {
    await api.delete(`/motocicletas/${id}`)
    const moto = motocicletas.value.find((m) => m.id_motocicleta === id)
    if (moto) {
      motocicletas.value = motocicletas.value.filter((m) => m.id_motocicleta !== id)
      motocicletasInactivas.value.unshift({ ...moto, estadoA: false })
    }
  }

  async function reactivar(id) {
    await api.put(`/motocicletas/${id}/reactivar`)
    const moto = motocicletasInactivas.value.find((m) => m.id_motocicleta === id)
    if (moto) {
      motocicletasInactivas.value = motocicletasInactivas.value.filter((m) => m.id_motocicleta !== id)
      motocicletas.value.unshift({ ...moto, estadoA: true })
    }
  }

  return {
    motocicletas,
    motocicletasInactivas,
    loading,
    error,
    listar,
    listarInactivas,
    crear,
    actualizar,
    eliminar,
    reactivar,
  }
})
