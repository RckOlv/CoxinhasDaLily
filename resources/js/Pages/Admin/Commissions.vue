<script setup>
import { computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'

const props = defineProps({
  monthLabel: String,
  month: Number,
  year: Number,
  totalOrders: Number,
  totalEvents: Number,
  totalSales: Number,
  commissionRate: Number,
  commission: Number,
})

const { appName } = usePage().props

const selectedMonth = computed(() => {
  const mm = String(props.month).padStart(2, '0')
  return `${props.year}-${mm}`
})

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 2,
  }).format(value)
}

function filterByMonth(event) {
  const [year, month] = event.target.value.split('-')

  router.get(
    '/admin/comisiones',
    { month: Number(month), year: Number(year) },
    { preserveState: true, preserveScroll: true, replace: true }
  )
}
</script>

<template>
  <AppLayout>
    <Head :title="`Comisiones - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <AdminHeader />

      <div class="bg-secondary py-4 px-5 text-center">
        <h1 class="font-display font-bold text-lg text-white">Mis Comisiones</h1>
        <p class="text-white/50 text-xs mt-1">{{ monthLabel }}</p>
      </div>

      <div class="px-5 pt-4 space-y-3">

        <!-- Filtro de mes -->
        <div class="bg-white rounded-2xl border border-primary/10 p-4 flex items-center justify-center gap-3">
          <svg class="w-5 h-5 text-secondary/40 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          <input
            type="month"
            :value="selectedMonth"
            @change="filterByMonth"
            class="w-48 max-w-full bg-transparent text-sm font-semibold text-secondary focus:outline-none"
          />
        </div>

        <!-- Comisión destacada -->
        <div class="rounded-2xl bg-green-600 text-white p-6 shadow-lg shadow-green-600/25">
          <div class="flex items-center gap-2 mb-1">
            <svg class="w-5 h-5 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="9" />
              <path d="M12 7v10M9.5 9.5c0-1 1.1-1.8 2.5-1.8s2.5.8 2.5 1.8-1.1 1.6-2.5 1.9-2.5.9-2.5 1.9 1.1 1.8 2.5 1.8 2.5-.8 2.5-1.8" />
            </svg>
            <p class="text-xs font-semibold uppercase tracking-wide text-white/80">
              Tu Comisión ({{ commissionRate * 100 }}%)
            </p>
          </div>
          <p class="text-4xl font-display font-bold">{{ formatPrice(commission) }}</p>
          <p class="text-[11px] text-white/60 mt-1">
            Sobre {{ formatPrice(totalSales) }} de ventas entregadas en {{ monthLabel }}
          </p>
        </div>

        <!-- Desglose -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 01-8 0" />
              </svg>
            </div>
            <p class="text-xl font-display font-bold text-secondary">{{ formatPrice(totalOrders) }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Total Pedidos</p>
          </div>

          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-primary-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
              </svg>
            </div>
            <p class="text-xl font-display font-bold text-secondary">{{ formatPrice(totalEvents) }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Total Eventos</p>
          </div>

          <div class="bg-white rounded-2xl border border-primary/10 p-4">
            <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center mb-2">
              <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23" /><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
              </svg>
            </div>
            <p class="text-xl font-display font-bold text-secondary">{{ formatPrice(totalSales) }}</p>
            <p class="text-[10px] text-secondary/40 mt-0.5 leading-tight">Ventas Totales</p>
          </div>
        </div>

        <p class="text-[11px] text-secondary/40 text-center leading-relaxed px-4">
          Sólo se cuentan pedidos y eventos marcados como <strong>entregados</strong> en {{ monthLabel }}.
        </p>
      </div>
    </div>
  </AppLayout>
</template>