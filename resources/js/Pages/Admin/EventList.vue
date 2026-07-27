<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  events: Array,
})

const { appName } = usePage().props

const statusColors = {
  pendiente: 'bg-yellow-100 text-yellow-700 border-yellow-200',
  confirmado: 'bg-green-100 text-green-700 border-green-200',
  entregado: 'bg-blue-100 text-blue-700 border-blue-200',
  cancelado: 'bg-red-100 text-red-700 border-red-200',
}

const statusLabels = {
  pendiente: 'Pendiente',
  confirmado: 'Confirmado',
  entregado: 'Entregado',
  cancelado: 'Cancelado',
}

const eventTypes = {
  cumpleanos: 'Cumpleaños',
  casamiento: 'Casamiento',
  corporativo: 'Corporativo',
  otro: 'Otro',
}

const filter = ref('todos')

const filteredEvents = computed(() => {
  if (filter.value === 'todos') return props.events
  return props.events.filter(e => e.status === filter.value)
})

function updateStatus(event, newStatus) {
  const labels = {
    confirmado: 'confirmar',
    entregado: 'marcar como entregado',
    cancelado: 'cancelar',
  }

  Swal.fire({
    title: `¿${labels[newStatus]} este evento?`,
    text: `${event.client_name} - ${formatDate(event.event_date)}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#EAB308',
    cancelButtonColor: '#78350F',
    confirmButtonText: 'Sí',
    cancelButtonText: 'No',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.events.update', event.id), {
        status: newStatus,
      }, {
        preserveScroll: true,
      })
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Evento actualizado',
        showConfirmButton: false,
        timer: 2000,
      })
    }
  })
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('es-AR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

function formatDateShort(date) {
  return new Date(date).toLocaleDateString('es-AR', {
    day: 'numeric',
    month: 'short',
  })
}
</script>

<template>
  <AppLayout>
    <Head :title="`Eventos - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <!-- Header -->
      <div class="bg-secondary py-6 px-5 text-center">
        <h1 class="font-display font-bold text-xl text-white">
          Eventos
        </h1>
        <p class="text-white/50 text-xs mt-1">
          {{ events.length }} evento{{ events.length !== 1 ? 's' : '' }} en total
        </p>
      </div>

      <!-- Filtros -->
      <div class="px-5 py-4 flex gap-2 overflow-x-auto">
        <button
          v-for="f in ['todos', 'pendiente', 'confirmado', 'entregado', 'cancelado']"
          :key="f"
          @click="filter = f"
          class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200"
          :class="filter === f
            ? 'bg-secondary text-cream'
            : 'bg-white text-secondary/60 border border-primary/10 hover:border-primary/25'"
        >
          {{ f === 'todos' ? 'Todos' : statusLabels[f] }}
          <span v-if="f !== 'todos'" class="ml-1 opacity-60">
            ({{ events.filter(e => e.status === f).length }})
          </span>
        </button>
      </div>

      <!-- Lista de eventos -->
      <div class="px-5 space-y-3">
        <div v-if="filteredEvents.length === 0" class="text-center py-12 text-secondary/40">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          <p class="text-sm">No hay eventos {{ filter !== 'todos' ? statusLabels[f] : '' }}</p>
        </div>

        <div
          v-for="event in filteredEvents"
          :key="event.id"
          class="bg-white rounded-2xl border border-primary/10 overflow-hidden"
        >
          <!-- Color bar -->
          <div class="h-1.5" :style="{ backgroundColor: event.color || '#EAB308' }" />

          <div class="p-4">
            <!-- Header row -->
            <div class="flex items-start justify-between mb-3">
              <div>
                <h3 class="font-display font-bold text-base text-secondary">{{ event.client_name }}</h3>
                <p class="text-xs text-secondary/50">
                  {{ eventTypes[event.event_type] || event.event_type }}
                </p>
              </div>
              <span
                class="px-3 py-1 rounded-lg text-xs font-semibold border"
                :class="statusColors[event.status]"
              >
                {{ statusLabels[event.status] }}
              </span>
            </div>

            <!-- Info grid -->
            <div class="grid grid-cols-2 gap-2 text-sm mb-3">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                <span class="text-secondary/70">{{ formatDate(event.event_date) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <span class="text-secondary/70">{{ event.pickup_time }} hs</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span class="text-secondary/70">{{ event.quantity }} personas</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                <span class="text-secondary/70">{{ event.client_whatsapp }}</span>
              </div>
            </div>

            <!-- Productos -->
            <div v-if="event.products?.length" class="bg-cream rounded-xl p-3 mb-3">
              <p class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-1.5">Productos</p>
              <div class="space-y-1">
                <div v-for="product in event.products" :key="product.id" class="flex justify-between text-sm">
                  <span class="text-secondary/70">{{ product.name }}</span>
                  <span class="text-secondary font-semibold">x{{ product.pivot.quantity }}</span>
                </div>
              </div>
            </div>

            <!-- Observaciones -->
            <div v-if="event.notes" class="bg-cream/50 rounded-xl p-3 mb-3">
              <p class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-1">Observaciones</p>
              <p class="text-sm text-secondary/70">{{ event.notes }}</p>
            </div>

            <!-- Acciones -->
            <div class="flex gap-2 mt-3">
              <button
                v-if="event.status === 'pendiente'"
                @click="updateStatus(event, 'confirmado')"
                class="flex-1 py-2.5 rounded-xl bg-green-500 text-white text-xs font-semibold
                       hover:bg-green-600 active:scale-[0.98] transition-all"
              >
                Confirmar
              </button>
              <button
                v-if="event.status === 'confirmado'"
                @click="updateStatus(event, 'entregado')"
                class="flex-1 py-2.5 rounded-xl bg-blue-500 text-white text-xs font-semibold
                       hover:bg-blue-600 active:scale-[0.98] transition-all"
              >
                Entregado
              </button>
              <button
                v-if="event.status !== 'cancelado' && event.status !== 'entregado'"
                @click="updateStatus(event, 'cancelado')"
                class="px-4 py-2.5 rounded-xl border border-red-200 text-red-500 text-xs font-semibold
                       hover:bg-red-50 active:scale-[0.98] transition-all"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
