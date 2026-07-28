<script setup>
import { ref, computed, nextTick } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  events: Array,
  occupiedDates: Array,
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
const expandedEvent = ref(null)
const editingEventId = ref(null)
const editingProducts = ref([])
const savingProducts = ref(false)

function startEditingProducts(event) {
  editingEventId.value = event.id
  editingProducts.value = event.products.map(p => ({
    id: p.id,
    name: p.name,
    price: parseFloat(p.price),
    units_per_package: p.units_per_package || 1,
    quantity: p.pivot.quantity,
  }))
}

function cancelEditingProducts() {
  editingEventId.value = null
  editingProducts.value = []
}

function updateEditingQuantity(productId, newQty) {
  const product = editingProducts.value.find(p => p.id === productId)
  if (product) {
    product.quantity = Math.max(0, parseInt(newQty) || 0)
  }
}

function saveProductQuantities(eventId) {
  savingProducts.value = true
  router.put(route('admin.events.products', eventId), {
    products: editingProducts.value.map(p => ({ id: p.id, quantity: p.quantity })),
  }, {
    preserveScroll: true,
    onSuccess: () => {
      editingEventId.value = null
      editingProducts.value = []
      savingProducts.value = false
    },
    onFinish: () => { savingProducts.value = false },
  })
}

function editingEstimatedTotal() {
  return editingProducts.value.reduce((sum, p) => sum + (p.quantity * p.price), 0)
}

function eventEstimatedTotal(event) {
  return event.products.reduce((sum, p) => sum + (p.pivot.quantity * parseFloat(p.price)), 0)
}

const dateRanges = [
  { value: 'all', label: 'Todas' },
  { value: 'today', label: 'Hoy' },
  { value: 'week', label: 'Semana' },
  { value: 'month', label: 'Mes' },
]

const filteredEvents = computed(() => {
  let result = props.events

  if (statusFilter.value !== 'todos') {
    result = result.filter(e => e.status === statusFilter.value)
  }

  if (dateFilter.value !== 'all') {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    result = result.filter(e => {
      const d = new Date(e.created_at)
      if (dateFilter.value === 'today') return d >= today
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
    const dayEvents = props.events.filter(e => e.event_date === dateStr)
    const isOccupied = props.occupiedDates.includes(dateStr)
    days.push({ day: d, date: dateStr, events: dayEvents, isOccupied })
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

const calendarDayEvents = computed(() => {
  if (!selectedCalendarDate.value) return []
  return props.events.filter(e => e.event_date === selectedCalendarDate.value)
})

function toggleExpand(event) {
  expandedEvent.value = expandedEvent.value === event.id ? null : event.id
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

function formatEventDate(dateStr) {
  const d = new Date(dateStr + 'T12:00:00')
  return d.toLocaleDateString('es-AR', {
    day: 'numeric',
    month: 'long',
  })
}

function formatShortDate(dateStr) {
  const d = new Date(dateStr + 'T12:00:00')
  return d.toLocaleDateString('es-AR', { day: 'numeric', month: 'short' })
}

function updateStatus(event, newStatus) {
  const labels = {
    confirmado: 'confirmar',
    entregado: 'marcar como completado',
    cancelado: 'cancelar',
  }

  Swal.fire({
    title: `¿${labels[newStatus]} este evento?`,
    html: `${event.client_name} — ${formatEventDate(event.event_date)} — ${formatPrice(event.total)}`,
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
    }
  })
}

function timeAgo(date) {
  const now = new Date()
  const d = new Date(date)
  const diff = Math.floor((now - d) / 1000)
  if (diff < 60) return 'hace un momento'
  if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`
  if (diff < 86400) return `hace ${Math.floor(diff / 3600)}h`
  return `hace ${Math.floor(diff / 86400)}d`
}

function getColorName(hex) {
  if (!hex) return ''
  const map = {
    '#F472B6': 'Rosa', '#FBCFE8': 'Rosa claro', '#BE185D': 'Rosa viejo',
    '#EF4444': 'Rojo', '#991B1B': 'Rojo oscuro', '#7F1D1D': 'Bordó',
    '#F97316': 'Naranja', '#FDBA74': 'Durazno', '#EAB308': 'Dorado',
    '#FACC15': 'Amarillo', '#22C55E': 'Verde', '#6EE7B7': 'Verde menta',
    '#166534': 'Verde oscuro', '#65A30D': 'Oliva', '#14B8A6': 'Turquesa',
    '#22D3EE': 'Aqua', '#93C5FD': 'Azul claro', '#3B82F6': 'Azul',
    '#1E3A5F': 'Azul marino', '#7DD3FC': 'Celeste', '#A855F7': 'Morado',
    '#C084FC': 'Lila', '#7C3AED': 'Violeta', '#DDD6FE': 'Lavanda',
    '#D946EF': 'Fucsia', '#DB2777': 'Magenta', '#FFFFFF': 'Blanco',
    '#E5E7EB': 'Gris claro', '#9CA3AF': 'Gris', '#C0C0C0': 'Plateado',
    '#1C1917': 'Negro', '#78350F': 'Chocolate', '#F5F0E1': 'Beige',
    '#FFFDF5': 'Crema', '#F5F5F4': 'Gris claro', '#000000': 'Negro',
    '#FF0000': 'Rojo', '#00FF00': 'Verde', '#0000FF': 'Azul',
    '#FFFF00': 'Amarillo', '#FF00FF': 'Fucsia', '#00FFFF': 'Aqua',
  }
  const upper = hex.toUpperCase()
  return map[upper] || hex
}
</script>

<template>
  <AppLayout>
    <Head :title="`Eventos - ${appName}`" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <AdminHeader />

      <div class="bg-secondary py-4 px-5 text-center">
        <h1 class="font-display font-bold text-lg text-white">Eventos</h1>
        <p class="text-white/50 text-xs mt-1">
          {{ filteredEvents.length }} de {{ events.length }} evento{{ events.length !== 1 ? 's' : '' }}
        </p>
      </div>

      <!-- Filtros + View toggle -->
      <div class="px-5 pt-4 pb-2">
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
              ({{ f === 'todos' ? events.length : events.filter(e => e.status === f).length }})
            </span>
          </button>
        </div>

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
        <div v-if="filteredEvents.length === 0" class="text-center py-12 text-secondary/40">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          <p class="text-sm">No hay eventos</p>
        </div>

        <div
          v-for="event in filteredEvents"
          :key="event.id"
          class="bg-white rounded-2xl border border-primary/10 overflow-hidden transition-all"
        >
          <!-- Color bar -->
          <div class="h-1.5" :class="{
            'bg-yellow-400': event.status === 'pendiente',
            'bg-green-400': event.status === 'confirmado',
            'bg-blue-400': event.status === 'entregado',
            'bg-red-400': event.status === 'cancelado',
          }" />

          <!-- Compact header -->
          <button
            @click="toggleExpand(event)"
            class="w-full p-3 flex items-center gap-3 text-left"
          >
            <div class="flex items-center gap-1.5 shrink-0">
              <div
                class="w-2 h-2 rounded-full"
                :style="{ backgroundColor: event.color || '#EAB308' }"
              />
              <span class="text-[10px] text-secondary/40">{{ getColorName(event.color) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-0.5">
                <h3 class="font-display font-bold text-sm text-secondary truncate">{{ event.client_name }}</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border shrink-0" :class="statusColors[event.status]">
                  {{ statusLabels[event.status] }}
                </span>
              </div>
              <div class="flex items-center gap-3 text-xs text-secondary/50">
                <span>{{ formatEventDate(event.event_date) }}</span>
                <span>{{ event.quantity }} personas</span>
              </div>
              <div class="flex items-center gap-3 text-xs mt-0.5">
                <span class="font-semibold text-primary-dark">{{ formatPrice(eventEstimatedTotal(event)) }}</span>
                <span v-if="event.notes" class="text-secondary/30 truncate max-w-[140px]">{{ event.notes }}</span>
              </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <svg class="w-4 h-4 text-secondary/30 transition-transform" :class="{ 'rotate-180': expandedEvent === event.id }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </div>
          </button>

          <!-- Expanded details -->
          <Transition
            enter-active-class="transition-all duration-200"
            leave-active-class="transition-all duration-150"
            enter-from-class="opacity-0 max-h-0"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="expandedEvent === event.id" class="overflow-hidden">
              <div class="px-3 pb-3 space-y-3 border-t border-primary/5 pt-3">
                <!-- Info -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                    <span class="text-secondary/70">{{ event.client_whatsapp }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span class="text-secondary/70">{{ event.pickup_time || '—' }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
                    </svg>
                    <span class="text-secondary/70">{{ event.event_type === 'cumpleanos' ? 'Cumpleaños' : event.event_type }}</span>
                  </div>
                </div>

                <!-- Notes -->
                <div v-if="event.notes" class="bg-cream rounded-xl p-3">
                  <p class="text-[10px] font-semibold text-secondary/40 mb-1 uppercase tracking-wide">Observaciones</p>
                  <p class="text-xs text-secondary/70 leading-relaxed">{{ event.notes }}</p>
                </div>

                <!-- Products -->
                <div v-if="event.products?.length" class="bg-cream rounded-xl p-3">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-semibold text-secondary/40 uppercase tracking-wide">Productos</p>
                    <button
                      v-if="editingEventId !== event.id"
                      @click.stop="startEditingProducts(event)"
                      class="text-[10px] text-primary-dark font-semibold hover:underline"
                    >
                      Editar cantidades
                    </button>
                    <div v-else class="flex gap-2">
                      <button
                        @click.stop="cancelEditingProducts"
                        class="text-[10px] text-secondary/40 font-semibold hover:underline"
                      >
                        Cancelar
                      </button>
                      <button
                        @click.stop="saveProductQuantities(event.id)"
                        :disabled="savingProducts"
                        class="text-[10px] text-green-600 font-semibold hover:underline disabled:opacity-50"
                      >
                        {{ savingProducts ? 'Guardando...' : 'Guardar' }}
                      </button>
                    </div>
                  </div>

                  <!-- View mode -->
                  <div v-if="editingEventId !== event.id" class="space-y-1.5">
                    <div v-for="item in event.products" :key="item.id" class="flex justify-between text-xs">
                      <span class="text-secondary/70">
                        {{ item.pivot.quantity }}x {{ item.name }}
                        <span v-if="item.units_per_package && item.units_per_package > 1" class="text-secondary/40">
                          ({{ item.pivot.quantity * item.units_per_package }} uds)
                        </span>
                      </span>
                      <span class="text-secondary/50">{{ formatPrice(item.pivot.quantity * parseFloat(item.price)) }}</span>
                    </div>
                    <div class="border-t border-primary/10 pt-1.5 mt-1.5 flex justify-between">
                      <span class="text-[10px] font-bold text-secondary/40">Estimado</span>
                      <span class="font-display font-bold text-primary-dark text-xs">{{ formatPrice(eventEstimatedTotal(event)) }}</span>
                    </div>
                  </div>

                  <!-- Edit mode -->
                  <div v-else class="space-y-2">
                    <div v-for="item in editingProducts" :key="item.id" class="flex items-center justify-between gap-2">
                      <span class="text-xs text-secondary/70 flex-1 min-w-0 truncate">{{ item.name }}</span>
                      <div class="flex items-center gap-1.5 shrink-0">
                        <button
                          @click.stop="updateEditingQuantity(item.id, item.quantity - 1)"
                          class="w-6 h-6 rounded bg-white border border-primary/15 flex items-center justify-center text-secondary/40 active:bg-primary/10"
                        >−</button>
                        <input
                          type="number"
                          :value="item.quantity"
                          @input="updateEditingQuantity(item.id, $event.target.value)"
                          @click.stop
                          min="0"
                          class="w-10 h-6 text-center text-xs rounded border border-primary/15 bg-white text-secondary font-semibold [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        />
                        <button
                          @click.stop="updateEditingQuantity(item.id, item.quantity + 1)"
                          class="w-6 h-6 rounded bg-white border border-primary/15 flex items-center justify-center text-secondary/40 active:bg-primary/10"
                        >+</button>
                      </div>
                      <span class="text-[10px] text-secondary/40 w-16 text-right">{{ formatPrice(item.quantity * item.price) }}</span>
                    </div>
                    <div class="border-t border-primary/10 pt-1.5 mt-1.5 flex justify-between">
                      <span class="text-[10px] font-bold text-secondary/40">Estimado</span>
                      <span class="font-display font-bold text-primary-dark text-xs">{{ formatPrice(editingEstimatedTotal()) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Deposit paid badge -->
                <div v-if="event.deposit_paid" class="bg-green-50 border border-green-200 rounded-xl p-2">
                  <p class="text-[10px] text-green-600 font-medium text-center">Señal pagada</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                  <button
                    v-if="event.status === 'pendiente'"
                    @click.stop="updateStatus(event, 'confirmado')"
                    class="flex-1 py-2 rounded-xl bg-green-500 text-white text-xs font-semibold hover:bg-green-600 active:scale-[0.98] transition-all"
                  >
                    Confirmar
                  </button>
                  <button
                    v-if="event.status === 'confirmado'"
                    @click.stop="updateStatus(event, 'entregado')"
                    class="flex-1 py-2 rounded-xl bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 active:scale-[0.98] transition-all"
                  >
                    Completado
                  </button>
                  <button
                    v-if="event.status !== 'cancelado' && event.status !== 'entregado'"
                    @click.stop="updateStatus(event, 'cancelado')"
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
          <div class="flex items-center justify-between mb-4">
            <button @click="prevMonth" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-cream transition-colors">
              <svg class="w-4 h-4 text-secondary/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6" /></svg>
            </button>
            <h3 class="font-display font-bold text-sm text-secondary capitalize">{{ calendarMonthLabel }}</h3>
            <button @click="nextMonth" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-cream transition-colors">
              <svg class="w-4 h-4 text-secondary/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6" /></svg>
            </button>
          </div>

          <div class="grid grid-cols-7 gap-1 mb-2">
            <div v-for="d in ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']" :key="d" class="text-center text-[10px] font-semibold text-secondary/40 py-1">
              {{ d }}
            </div>
          </div>

          <div class="grid grid-cols-7 gap-1">
            <div
              v-for="(dayObj, i) in calendarDays"
              :key="i"
              class="flex flex-col items-center justify-center rounded-lg text-xs transition-all cursor-pointer relative h-9 sm:h-11"
              :class="{
                'text-secondary/20': !dayObj.day,
                'hover:bg-cream': dayObj.day && !dayObj.events?.length && !dayObj.isOccupied,
                'bg-primary/10 font-bold': dayObj.day && dayObj.events?.length > 0,
                'bg-red-50': dayObj.day && dayObj.isOccupied && !dayObj.events?.length,
                'ring-2 ring-primary': selectedCalendarDate === dayObj.date,
              }"
              @click="selectCalendarDay(dayObj)"
            >
              <span v-if="dayObj.day">{{ dayObj.day }}</span>
              <div v-if="dayObj.events?.length" class="flex gap-0.5 mt-0.5">
                <span
                  v-for="(event, ei) in dayObj.events.slice(0, 3)"
                  :key="ei"
                  class="w-1 h-1 rounded-full"
                  :class="{
                    'bg-yellow-400': event.status === 'pendiente',
                    'bg-green-400': event.status === 'confirmado',
                    'bg-blue-400': event.status === 'entregado',
                    'bg-red-400': event.status === 'cancelado',
                  }"
                />
              </div>
              <div v-else-if="dayObj.isOccupied" class="w-1.5 h-1.5 rounded-full bg-red-300 mt-0.5" />
            </div>
          </div>
        </div>

        <!-- Selected day events -->
        <div v-if="selectedCalendarDate" id="calendar-results" class="mb-4 scroll-mt-4">
          <p class="text-xs font-semibold text-secondary/50 mb-2 px-1">
            {{ formatShortDate(selectedCalendarDate) }} — {{ calendarDayEvents.length }} evento{{ calendarDayEvents.length !== 1 ? 's' : '' }}
          </p>
          <div class="space-y-2">
            <div
              v-for="event in calendarDayEvents"
              :key="event.id"
              class="bg-white rounded-xl border border-primary/10 p-3"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-semibold text-secondary">{{ event.client_name }}</p>
                  <p class="text-xs text-secondary/50">{{ event.pickup_time || '—' }} · {{ event.quantity }} personas · {{ formatPrice(event.total) }}</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border" :class="statusColors[event.status]">
                  {{ statusLabels[event.status] }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
