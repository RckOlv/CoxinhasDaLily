<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  orders: Array,
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

const filter = ref('todos')

const filteredOrders = computed(() => {
  if (filter.value === 'todos') return props.orders
  return props.orders.filter(o => o.status === filter.value)
})

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true,
})

function updateStatus(order, newStatus) {
  const labels = {
    confirmado: 'confirmar',
    entregado: 'marcar como entregado',
    cancelado: 'cancelar',
  }

  const extraMsg = newStatus === 'confirmado' && !order.stock_decremented
    ? '<br><small class="text-secondary/50">Se descontará el stock automáticamente</small>'
    : ''

  Swal.fire({
    title: `¿${labels[newStatus]} este pedido?`,
    html: `${order.client_name} — ${formatPrice(order.total)}${extraMsg}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#EAB308',
    cancelButtonColor: '#78350F',
    confirmButtonText: 'Sí',
    cancelButtonText: 'No',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.orders.update', order.id), {
        status: newStatus,
      }, {
        preserveScroll: true,
      })
      Toast.fire({ icon: 'success', title: 'Pedido actualizado' })
    }
  })
}

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 2,
  }).format(value)
}

function formatDate(date) {
  const d = new Date(date)
  return d.toLocaleDateString('es-AR', {
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'America/Argentina/Buenos_Aires',
  })
}
</script>

<template>
  <AppLayout>
    <Head :title="`Pedidos - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <AdminHeader />

      <!-- Sub-header -->
      <div class="bg-secondary py-4 px-5 text-center">
        <h1 class="font-display font-bold text-lg text-white">
          Pedidos
        </h1>
        <p class="text-white/50 text-xs mt-1">
          {{ orders.length }} pedido{{ orders.length !== 1 ? 's' : '' }} en total
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
            ({{ orders.filter(o => o.status === f).length }})
          </span>
        </button>
      </div>

      <!-- Lista de pedidos -->
      <div class="px-5 space-y-3">
        <div v-if="filteredOrders.length === 0" class="text-center py-12 text-secondary/40">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 01-8 0" />
          </svg>
          <p class="text-sm">No hay pedidos</p>
        </div>

        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="bg-white rounded-2xl border border-primary/10 overflow-hidden"
        >
          <!-- Status bar -->
          <div
            class="h-1.5"
            :class="{
              'bg-yellow-400': order.status === 'pendiente',
              'bg-green-400': order.status === 'confirmado',
              'bg-blue-400': order.status === 'entregado',
              'bg-red-400': order.status === 'cancelado',
            }"
          />

          <div class="p-4">
            <!-- Header -->
            <div class="flex items-start justify-between mb-3">
              <div>
                <h3 class="font-display font-bold text-base text-secondary">{{ order.client_name }}</h3>
                <p class="text-xs text-secondary/50">
                  {{ order.delivery_method === 'pickup' ? 'Retiro en domicilio' : 'Envío' }}
                </p>
              </div>
              <span
                class="px-3 py-1 rounded-lg text-xs font-semibold border"
                :class="statusColors[order.status]"
              >
                {{ statusLabels[order.status] }}
              </span>
            </div>

            <!-- Info -->
            <div class="grid grid-cols-2 gap-2 text-sm mb-3">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                <span class="text-secondary/70">{{ order.client_whatsapp }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="1" y="3" width="15" height="13" />
                  <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                  <circle cx="5.5" cy="18.5" r="2.5" />
                  <circle cx="18.5" cy="18.5" r="2.5" />
                </svg>
                <span class="text-secondary/70">
                  {{ order.delivery_method === 'pickup' ? 'Retira en domicilio' : order.delivery_address || 'Sin dirección' }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                  <line x1="1" y1="10" x2="23" y2="10" />
                </svg>
                <span class="text-secondary/70 capitalize">{{ order.payment_method }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <span class="text-secondary/70">{{ formatDate(order.created_at) }}</span>
              </div>
            </div>

            <!-- Items -->
            <div v-if="order.items?.length" class="bg-cream rounded-xl p-3 mb-3">
              <p class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-2">Productos</p>
              <div class="space-y-1.5">
                <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
                  <span class="text-secondary/70">{{ item.quantity }}x {{ item.product?.name || 'Producto' }}</span>
                  <span class="font-semibold text-secondary">{{ formatPrice(item.price * item.quantity) }}</span>
                </div>
              </div>
              <div class="border-t border-primary/10 pt-2 mt-2 flex justify-between">
                <span class="text-sm font-bold text-secondary/60">Total</span>
                <span class="font-display font-bold text-primary-dark">{{ formatPrice(order.total) }}</span>
              </div>
            </div>

            <!-- Stock badge -->
            <div v-if="order.stock_decremented" class="bg-green-50 border border-green-200 rounded-xl p-2 mb-3">
              <p class="text-xs text-green-600 font-medium text-center">Stock descontado</p>
            </div>

            <!-- Acciones -->
            <div class="flex gap-2">
              <button
                v-if="order.status === 'pendiente'"
                @click="updateStatus(order, 'confirmado')"
                class="flex-1 py-2.5 rounded-xl bg-green-500 text-white text-xs font-semibold
                       hover:bg-green-600 active:scale-[0.98] transition-all"
              >
                Confirmar
              </button>
              <button
                v-if="order.status === 'confirmado'"
                @click="updateStatus(order, 'entregado')"
                class="flex-1 py-2.5 rounded-xl bg-blue-500 text-white text-xs font-semibold
                       hover:bg-blue-600 active:scale-[0.98] transition-all"
              >
                Entregado
              </button>
              <button
                v-if="order.status !== 'cancelado' && order.status !== 'entregado'"
                @click="updateStatus(order, 'cancelado')"
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
