<script setup>
import { ref, computed, nextTick } from 'vue'
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

const statusFilter = ref('todos')
const dateFilter = ref('all')
const viewMode = ref('list')
const expandedOrder = ref(null)

const dateRanges = [
  { value: 'all', label: 'Todas' },
  { value: 'today', label: 'Hoy' },
  { value: 'week', label: 'Semana' },
  { value: 'month', label: 'Mes' },
]

const filteredOrders = computed(() => {
  let result = props.orders

  if (statusFilter.value !== 'todos') {
    result = result.filter(o => o.status === statusFilter.value)
  }

  if (dateFilter.value !== 'all') {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    result = result.filter(o => {
      const d = new Date(o.created_at)
      if (dateFilter.value === 'today') {
        return d >= today
      }
      if (dateFilter.value === 'week') {
        const weekAgo = new Date(today)
        weekAgo.setDate(weekAgo.getDate() - 7)
        return d >= weekAgo
      }
      if (dateFilter.value === 'month') {
        return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
      }
      return true
    })
  }

  return result
})

// Calendar
const calendarDate = ref(new Date())

const calendarDays = computed(() => {
  const year = calendarDate.value.getFullYear()
  const month = calendarDate.value.getMonth()
  const firstDay = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()

  const days = []
  for (let i = 0; i < firstDay; i++) {
    days.push({ day: null, date: null })
  }
  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    const dayOrders = props.orders.filter(o => o.created_at.startsWith(dateStr))
    days.push({ day: d, date: dateStr, orders: dayOrders })
  }
  return days
})

const calendarMonthLabel = computed(() => {
  return calendarDate.value.toLocaleDateString('es-AR', { month: 'long', year: 'numeric' })
})

function prevMonth() {
  const d = new Date(calendarDate.value)
  d.setMonth(d.getMonth() - 1)
  calendarDate.value = d
}

function nextMonth() {
  const d = new Date(calendarDate.value)
  d.setMonth(d.getMonth() + 1)
  calendarDate.value = d
}

const selectedCalendarDate = ref(null)

function selectCalendarDay(dayObj) {
  if (!dayObj || !dayObj.day) return
  selectedCalendarDate.value = selectedCalendarDate.value === dayObj.date ? null : dayObj.date
  if (selectedCalendarDate.value) {
    nextTick(() => {
      document.getElementById('calendar-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    })
  }
}

const calendarDayOrders = computed(() => {
  if (!selectedCalendarDate.value) return []
  return props.orders.filter(o => o.created_at.startsWith(selectedCalendarDate.value))
})

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true,
})

function toggleExpand(order) {
  expandedOrder.value = expandedOrder.value === order.id ? null : order.id
}

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

function formatShortDate(dateStr) {
  const d = new Date(dateStr + 'T12:00:00')
  return d.toLocaleDateString('es-AR', { day: 'numeric', month: 'short' })
}
</script>

<template>
  <AppLayout>
    <Head :title="`Pedidos - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <AdminHeader />

      <!-- Sub-header -->
      <div class="bg-secondary py-4 px-5 text-center">
        <h1 class="font-display font-bold text-lg text-white">Pedidos</h1>
        <p class="text-white/50 text-xs mt-1">
          {{ filteredOrders.length }} de {{ orders.length }} pedido{{ orders.length !== 1 ? 's' : '' }}
        </p>
      </div>

      <!-- Filtros + View toggle -->
      <div class="px-5 pt-4 pb-2">
        <!-- Status filters -->
        <div class="flex gap-2 overflow-x-auto mb-3">
          <button
            v-for="f in ['todos', 'pendiente', 'confirmado', 'entregado', 'cancelado']"
            :key="f"
            @click="statusFilter = f"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all"
            :class="statusFilter === f
              ? 'bg-secondary text-cream'
              : 'bg-white text-secondary/60 border border-primary/10'"
          >
            {{ f === 'todos' ? 'Todos' : statusLabels[f] }}
            <span class="ml-1 opacity-60">
              ({{ f === 'todos' ? orders.length : orders.filter(o => o.status === f).length }})
            </span>
          </button>
        </div>

        <!-- Date filters + View toggle -->
        <div class="flex items-center gap-2">
          <div class="flex gap-1.5 flex-1 overflow-x-auto">
            <button
              v-for="d in dateRanges"
              :key="d.value"
              @click="dateFilter = d.value"
              class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all"
              :class="dateFilter === d.value
                ? 'bg-primary text-secondary'
                : 'bg-white text-secondary/50 border border-primary/10'"
            >
              {{ d.label }}
            </button>
          </div>
          <div class="flex bg-white rounded-lg border border-primary/10 overflow-hidden shrink-0">
            <button
              @click="viewMode = 'list'"
              class="px-2.5 py-1.5 text-xs transition-colors"
              :class="viewMode === 'list' ? 'bg-secondary text-cream' : 'text-secondary/40'"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="8" y1="6" x2="21" y2="6" /><line x1="8" y1="12" x2="21" y2="12" /><line x1="8" y1="18" x2="21" y2="18" />
                <line x1="3" y1="6" x2="3.01" y2="6" /><line x1="3" y1="12" x2="3.01" y2="12" /><line x1="3" y1="18" x2="3.01" y2="18" />
              </svg>
            </button>
            <button
              @click="viewMode = 'calendar'"
              class="px-2.5 py-1.5 text-xs transition-colors"
              :class="viewMode === 'calendar' ? 'bg-secondary text-cream' : 'text-secondary/40'"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- LIST VIEW -->
      <div v-if="viewMode === 'list'" class="px-5 space-y-3">
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
          class="bg-white rounded-2xl border border-primary/10 overflow-hidden transition-all"
        >
          <!-- Color bar -->
          <div class="h-1.5" :class="{
            'bg-yellow-400': order.status === 'pendiente',
            'bg-green-400': order.status === 'confirmado',
            'bg-blue-400': order.status === 'entregado',
            'bg-red-400': order.status === 'cancelado',
          }" />

          <!-- Compact header (always visible) -->
          <button
            @click="toggleExpand(order)"
            class="w-full p-3 flex items-center gap-3 text-left"
          >
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-0.5">
                <h3 class="font-display font-bold text-sm text-secondary truncate">{{ order.client_name }}</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border shrink-0" :class="statusColors[order.status]">
                  {{ statusLabels[order.status] }}
                </span>
              </div>
              <div class="flex items-center gap-3 text-xs text-secondary/50">
                <span>{{ formatPrice(order.total) }}</span>
                <span>{{ order.items?.length || 0 }} producto{{ (order.items?.length || 0) !== 1 ? 's' : '' }}</span>
                <span>{{ formatDate(order.created_at).split(',')[0] }}</span>
              </div>
            </div>
            <svg class="w-4 h-4 text-secondary/30 shrink-0 transition-transform" :class="{ 'rotate-180': expandedOrder === order.id }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </button>

          <!-- Expanded details -->
          <Transition
            enter-active-class="transition-all duration-200"
            leave-active-class="transition-all duration-150"
            enter-from-class="opacity-0 max-h-0"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="expandedOrder === order.id" class="overflow-hidden">
              <div class="px-3 pb-3 space-y-3 border-t border-primary/5 pt-3">
                <!-- Info -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                    <span class="text-secondary/70">{{ order.client_whatsapp }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="1" y="3" width="15" height="13" /><polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                      <circle cx="5.5" cy="18.5" r="2.5" /><circle cx="18.5" cy="18.5" r="2.5" />
                    </svg>
                    <span class="text-secondary/70">{{ order.delivery_method === 'pickup' ? 'Retiro' : order.delivery_address || '—' }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="1" y="4" width="22" height="16" rx="2" ry="2" /><line x1="1" y1="10" x2="23" y2="10" />
                    </svg>
                    <span class="text-secondary/70 capitalize">{{ order.payment_method }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span class="text-secondary/70">{{ formatDate(order.created_at) }}</span>
                  </div>
                </div>

                <!-- Products -->
                <div v-if="order.items?.length" class="bg-cream rounded-xl p-3">
                  <div class="space-y-1.5">
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between text-xs">
                      <span class="text-secondary/70">{{ item.quantity }}x {{ item.product?.name || 'Producto' }}</span>
                      <span class="font-semibold text-secondary">{{ formatPrice(item.price * item.quantity) }}</span>
                    </div>
                  </div>
                  <div class="border-t border-primary/10 pt-2 mt-2 flex justify-between">
                    <span class="text-xs font-bold text-secondary/60">Total</span>
                    <span class="font-display font-bold text-primary-dark text-sm">{{ formatPrice(order.total) }}</span>
                  </div>
                </div>

                <!-- Stock badge -->
                <div v-if="order.stock_decremented" class="bg-green-50 border border-green-200 rounded-xl p-2">
                  <p class="text-[10px] text-green-600 font-medium text-center">Stock descontado</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                  <button
                    v-if="order.status === 'pendiente'"
                    @click.stop="updateStatus(order, 'confirmado')"
                    class="flex-1 py-2 rounded-xl bg-green-500 text-white text-xs font-semibold hover:bg-green-600 active:scale-[0.98] transition-all"
                  >
                    Confirmar
                  </button>
                  <button
                    v-if="order.status === 'confirmado'"
                    @click.stop="updateStatus(order, 'entregado')"
                    class="flex-1 py-2 rounded-xl bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 active:scale-[0.98] transition-all"
                  >
                    Entregado
                  </button>
                  <button
                    v-if="order.status !== 'cancelado' && order.status !== 'entregado'"
                    @click.stop="updateStatus(order, 'cancelado')"
                    class="px-4 py-2 rounded-xl border border-red-200 text-red-500 text-xs font-semibold hover:bg-red-50 active:scale-[0.98] transition-all"
                  >
                    Cancelar
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>

      <!-- CALENDAR VIEW -->
      <div v-if="viewMode === 'calendar'" class="px-5">
        <div class="bg-white rounded-2xl border border-primary/10 p-4 mb-4">
          <!-- Month nav -->
          <div class="flex items-center justify-between mb-4">
            <button @click="prevMonth" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-cream transition-colors">
              <svg class="w-4 h-4 text-secondary/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6" /></svg>
            </button>
            <h3 class="font-display font-bold text-sm text-secondary capitalize">{{ calendarMonthLabel }}</h3>
            <button @click="nextMonth" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-cream transition-colors">
              <svg class="w-4 h-4 text-secondary/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6" /></svg>
            </button>
          </div>

          <!-- Day headers -->
          <div class="grid grid-cols-7 gap-1 mb-2">
            <div v-for="d in ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']" :key="d" class="text-center text-[10px] font-semibold text-secondary/40 py-1">
              {{ d }}
            </div>
          </div>

          <!-- Calendar grid -->
          <div class="grid grid-cols-7 gap-1">
            <div
              v-for="(dayObj, i) in calendarDays"
              :key="i"
              class="flex flex-col items-center justify-center rounded-lg text-xs transition-all cursor-pointer relative h-9 sm:h-11"
              :class="{
                'text-secondary/20': !dayObj.day,
                'hover:bg-cream': dayObj.day && dayObj.orders?.length === 0,
                'bg-primary/10 font-bold': dayObj.day && dayObj.orders?.length > 0,
                'ring-2 ring-primary': selectedCalendarDate === dayObj.date,
              }"
              @click="selectCalendarDay(dayObj)"
            >
              <span v-if="dayObj.day">{{ dayObj.day }}</span>
              <div v-if="dayObj.orders?.length" class="flex gap-0.5 mt-0.5">
                <span
                  v-for="(order, oi) in dayObj.orders.slice(0, 3)"
                  :key="oi"
                  class="w-1 h-1 rounded-full"
                  :class="{
                    'bg-yellow-400': order.status === 'pendiente',
                    'bg-green-400': order.status === 'confirmado',
                    'bg-blue-400': order.status === 'entregado',
                    'bg-red-400': order.status === 'cancelado',
                  }"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Selected day orders -->
        <div v-if="selectedCalendarDate" id="calendar-results" class="mb-4 scroll-mt-4">
          <p class="text-xs font-semibold text-secondary/50 mb-2 px-1">
            {{ formatShortDate(selectedCalendarDate) }} — {{ calendarDayOrders.length }} pedido{{ calendarDayOrders.length !== 1 ? 's' : '' }}
          </p>
          <div class="space-y-2">
            <div
              v-for="order in calendarDayOrders"
              :key="order.id"
              class="bg-white rounded-xl border border-primary/10 p-3"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-semibold text-secondary">{{ order.client_name }}</p>
                  <p class="text-xs text-secondary/50">{{ formatPrice(order.total) }} · {{ order.items?.length || 0 }} productos</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border" :class="statusColors[order.status]">
                  {{ statusLabels[order.status] }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
