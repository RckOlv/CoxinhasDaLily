<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'

const props = defineProps({
  stats: Object,
})

const { appName, auth } = usePage().props

function formatDate(date) {
  if (!date) return ''
  const d = new Date(date + 'T12:00:00')
  return d.toLocaleDateString('es-AR', { day: 'numeric', month: 'long', timeZone: 'America/Argentina/Buenos_Aires' })
}

const statusColors = {
  pendiente: 'bg-yellow-100 text-yellow-700 border-yellow-200',
  confirmado: 'bg-green-100 text-green-700 border-green-200',
}

const statusLabels = {
  pendiente: 'Pendiente',
  confirmado: 'Confirmado',
}
</script>

<template>
  <AppLayout>
    <Head :title="`Panel - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <AdminHeader />

      <div class="bg-secondary py-4 px-5 text-center">
        <h1 class="font-display font-bold text-lg text-white">Panel</h1>
        <p class="text-white/50 text-xs mt-1">Bienvenida, {{ auth.user?.name }}</p>
      </div>

      <div class="px-5 pt-4 space-y-4">

        <!-- Cards -->
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 01-8 0" />
              </svg>
            </div>
            <p class="text-2xl font-display font-bold text-secondary">{{ stats.pendingOrders }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Pedidos pendientes</p>
          </div>

          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-primary-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
              </svg>
            </div>
            <p class="text-2xl font-display font-bold text-secondary">{{ stats.upcomingEvents.length }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Eventos próximos<br>(15 días)</p>
          </div>

          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
            </div>
            <p class="text-2xl font-display font-bold text-secondary">{{ stats.lowStockProducts.length + stats.outOfStockProducts.length }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Productos con<br>stock bajo</p>
          </div>

          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
              </svg>
            </div>
            <p class="text-2xl font-display font-bold text-secondary">{{ stats.eventsThisMonth }}<span class="text-sm text-secondary/30">/{{ stats.eventsLimit }}</span></p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Eventos este mes</p>
          </div>
        </div>

        <!-- Resumen -->
        <div class="bg-white rounded-2xl border border-primary/10 p-4">
          <p class="text-xs text-secondary/50 text-center leading-relaxed">
            <span class="font-semibold text-secondary">{{ stats.ordersToday }}</span> pedido{{ stats.ordersToday !== 1 ? 's' : '' }} hoy ·
            <span class="font-semibold text-secondary">{{ stats.upcomingEvents.length }}</span> evento{{ stats.upcomingEvents.length !== 1 ? 's' : '' }} próximos ·
            <span class="font-semibold text-secondary">{{ stats.lowStockProducts.length + stats.outOfStockProducts.length }}</span> producto{{ (stats.lowStockProducts.length + stats.outOfStockProducts.length) !== 1 ? 's' : '' }} sin stock
          </p>
        </div>

        <!-- Próximos eventos -->
        <div v-if="stats.upcomingEvents.length">
          <h2 class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-2 px-1">Próximos eventos</h2>
          <div class="space-y-2">
            <div
              v-for="event in stats.upcomingEvents.slice(0, 5)"
              :key="event.id"
              class="bg-white rounded-xl border border-primary/10 p-3 flex items-center justify-between"
            >
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-secondary truncate">{{ event.client_name }}</p>
                <p class="text-xs text-secondary/50">{{ formatDate(event.event_date) }} · {{ event.quantity }} personas · {{ event.products_count }} producto{{ event.products_count !== 1 ? 's' : '' }}</p>
              </div>
              <span class="px-2 py-0.5 rounded text-[10px] font-semibold border shrink-0 ml-2" :class="statusColors[event.status]">
                {{ statusLabels[event.status] }}
              </span>
            </div>
          </div>
        </div>

        <!-- Stock bajo -->
        <div v-if="stats.lowStockProducts.length || stats.outOfStockProducts.length">
          <h2 class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-2 px-1">Stock bajo</h2>
          <div class="space-y-1.5">
            <div
              v-for="p in stats.outOfStockProducts"
              :key="p.id"
              class="bg-red-50 border border-red-200 rounded-xl px-3 py-2 flex justify-between text-xs"
            >
              <span class="text-red-700 font-medium">{{ p.name }}</span>
              <span class="text-red-500 font-bold">Sin stock</span>
            </div>
            <div
              v-for="p in stats.lowStockProducts"
              :key="p.id"
              class="bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2 flex justify-between text-xs"
            >
              <span class="text-yellow-800 font-medium">{{ p.name }}</span>
              <span class="text-yellow-600 font-bold">{{ p.stock_quantity }} uds</span>
            </div>
          </div>
        </div>

        <!-- Accesos directos -->
        <div>
          <h2 class="text-xs font-semibold text-secondary/50 uppercase tracking-wide mb-2 px-1">Accesos directos</h2>
          <div class="grid grid-cols-2 gap-2">
            <Link
              href="/admin/productos"
              class="bg-white rounded-2xl border border-primary/10 p-4 flex flex-col items-center gap-2 active:scale-[0.98] transition-all hover:bg-primary/5"
            >
              <svg class="w-6 h-6 text-secondary/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" />
              </svg>
              <span class="text-xs font-semibold text-secondary">Productos</span>
            </Link>
            <Link
              href="/admin/eventos"
              class="bg-white rounded-2xl border border-primary/10 p-4 flex flex-col items-center gap-2 active:scale-[0.98] transition-all hover:bg-primary/5"
            >
              <svg class="w-6 h-6 text-secondary/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
              </svg>
              <span class="text-xs font-semibold text-secondary">Eventos</span>
            </Link>
            <Link
              href="/admin/pedidos"
              class="bg-white rounded-2xl border border-primary/10 p-4 flex flex-col items-center gap-2 active:scale-[0.98] transition-all hover:bg-primary/5"
            >
              <svg class="w-6 h-6 text-secondary/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                <rect x="9" y="3" width="6" height="4" rx="1" />
              </svg>
              <span class="text-xs font-semibold text-secondary">Pedidos</span>
            </Link>
            <Link
              href="/"
              class="bg-white rounded-2xl border border-primary/10 p-4 flex flex-col items-center gap-2 active:scale-[0.98] transition-all hover:bg-primary/5"
            >
              <svg class="w-6 h-6 text-secondary/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" /><line x1="15" y1="9" x2="9.707" y2="15.293" /><line x1="9" y1="9" x2="15.293" y2="9.707" />
              </svg>
              <span class="text-xs font-semibold text-secondary">Tienda</span>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
