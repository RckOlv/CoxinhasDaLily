<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  products: Array,
  occupiedDates: { type: Array, default: () => [] },
})

const form = ref({
  client_name: '',
  client_whatsapp: '',
  event_date: '',
  quantity: 50,
  pickup_time: '',
  event_type: 'cumpleanos',
  color: '#EAB308',
  notes: '',
  products: [],
  payment_plan: 'deposit',
})

const errors = ref({})
const liveErrors = ref({})
const sending = ref(false)
const showPaymentModal = ref(false)

const quantityError = computed(() => {
  const q = form.value.quantity
  if (!q || q < 1) return 'Ingresá la cantidad de personas'
  if (q > 100) return 'Máximo 100 personas'
  return null
})

function validateName() {
  const v = form.value.client_name.trim()
  if (!v) return 'El nombre es obligatorio'
  if (v.length < 2) return 'El nombre debe tener al menos 2 caracteres'
  if (/[0-9]/.test(v)) return 'El nombre no puede contener números'
  return null
}

function validateWhatsApp() {
  const v = form.value.client_whatsapp.trim()
  if (!v) return 'El WhatsApp es obligatorio'
  const digits = v.replace(/\D/g, '')
  if (digits.length < 10) return 'El número debe tener al menos 10 dígitos'
  if (digits.length > 15) return 'El número no puede tener más de 15 dígitos'
  return null
}

const colorOptions = ref([])

const colorTranslations = {
  AliceBlue: 'Azul Alice', AntiqueWhite: 'Blanco Antiguo', Aqua: 'Aqua', Aquamarine: 'Aguamarina', Azure: 'Azul Cielo',
  Beige: 'Beige', Bisque: 'Bizcocho', Black: 'Negro', BlanchedAlmond: 'Almendra', Blue: 'Azul', BlueViolet: 'Azul Violeta',
  Brown: 'Marrón', BurlyWood: 'Madera', CadetBlue: 'Azul Cadete', Chartreuse: 'Chartreuse', Chocolate: 'Chocolate',
  Coral: 'Coral', CornflowerBlue: 'Azul Aciano', Cornsilk: 'Seda de Maíz', Crimson: 'Carmesí', Cyan: 'Cian',
  DarkBlue: 'Azul Oscuro', DarkCyan: 'Cian Oscuro', DarkGoldenRod: 'Dorado Oscuro', DarkGray: 'Gris Oscuro',
  DarkGreen: 'Verde Oscuro', DarkKhaki: 'Caqui Oscuro', DarkMagenta: 'Magenta Oscuro', DarkOliveGreen: 'Verde Oliva Oscuro',
  DarkOrange: 'Naranja Oscuro', DarkOrchid: 'Orquídea Oscura', DarkRed: 'Rojo Oscuro', DarkSalmon: 'Salmón Oscuro',
  DarkSeaGreen: 'Verde Mar Oscuro', DarkSlateBlue: 'Azul Pizarra Oscuro', DarkSlateGray: 'Gris Pizarra Oscuro',
  DarkTurquoise: 'Turquesa Oscuro', DarkViolet: 'Violeta Oscuro', DeepPink: 'Rosa Profundo', DeepSkyBlue: 'Azul Cielo Profundo',
  DimGray: 'Gris Tenue', DodgerBlue: 'Azul Dodger', FireBrick: 'Ladrillo', FloralWhite: 'Blanco Floral',
  ForestGreen: 'Verde Bosque', Fucsia: 'Fucsia', Gainsboro: 'Gainsboro', GhostWhite: 'Blanco Fantasma', Gold: 'Oro',
  GoldenRod: 'Vara de Oro', Gray: 'Gris', Green: 'Verde', GreenYellow: 'Verde Amarillo', HoneyDew: 'Rocío de Miel',
  HotPink: 'Rosa Intenso', IndianRed: 'Rojo Indio', Indigo: 'Índigo', Ivory: 'Marfil', Khaki: 'Caqui',
  Lavender: 'Lavanda', LavenderBlush: 'Rubor Lavanda', LawnGreen: 'Verde Cesped', LemonChiffon: 'Limón',
  LightBlue: 'Azul Claro', LightCoral: 'Coral Claro', LightCyan: 'Cian Claro', LightGoldenRodYellow: 'Dorado Claro',
  LightGray: 'Gris Claro', LightGreen: 'Verde Claro', LightPink: 'Rosa Claro', LightSalmon: 'Salmón Claro',
  LightSeaGreen: 'Verde Mar Claro', LightSkyBlue: 'Azul Cielo Claro', LightSlateGray: 'Gris Pizarra Claro',
  LightSteelBlue: 'Azul Acero Claro', LightYellow: 'Amarillo Claro', Lime: 'Lima', LimeGreen: 'Verde Lima',
  Linen: 'Lino', Magenta: 'Magenta', Maroon: 'Granate', MediumAquaMarine: 'Aguamarina Media',
  MediumBlue: 'Azul Medio', MediumOrchid: 'Orquídea Media', MediumPurple: 'Púrpura Media',
  MediumSeaGreen: 'Verde Mar Medio', MediumSlateBlue: 'Azul Pizarra Medio', MediumSpringGreen: 'Verde Primavera Medio',
  MediumTurquoise: 'Turquesa Medio', MediumVioletRed: 'Rojo Violeta Medio', MidnightBlue: 'Azul Medianoche',
  MintCream: 'Crema de Menta', MistyRose: 'Rosa Neblinoso', Moccasin: 'Mocasín', NavajoWhite: 'Blanco Navajo',
  Navy: 'Azul Marino', OldLace: 'Encaje Viejo', Olive: 'Oliva', OliveDrab: 'Verde Oliva Opaco',
  Orange: 'Naranja', OrangeRed: 'Rojo Naranja', Orchid: 'Orquídea', PaleGoldenRod: 'Dorado Pálido',
  PaleGreen: 'Verde Pálido', PaleTurquoise: 'Turquesa Pálido', PaleVioletRed: 'Rojo Violeta Pálido',
  PapayaWhip: 'Papaya', PeachPuff: 'Durazno', Peru: 'Perú', Pink: 'Rosa', Plum: 'Ciruela',
  PowderBlue: 'Azul Polvo', Purple: 'Púrpura', RebeccaBlue: 'Azul Rebecca', RosyBrown: 'Marrón Rosado',
  RoyalBlue: 'Azul Real', SaddleBrown: 'Marrón Montura', Salmon: 'Salmón', SandyBrown: 'Marrón Arenoso',
  SeaGreen: 'Verde Mar', SeaShell: 'Concha de Mar', Sienna: 'Siena', SkyBlue: 'Azul Cielo',
  SlateBlue: 'Azul Pizarra', SlateGray: 'Gris Pizarra', Snow: 'Nieve', SpringGreen: 'Verde Primavera',
  SteelBlue: 'Azul Acero', Tan: 'Tan', Teal: 'Verde Azulado', Thistle: 'Cardo', Tomato: 'Tomate',
  Turquoise: 'Turquesa', Violet: 'Violeta', Wheat: 'Trigo', White: 'Blanco', WhiteSmoke: 'Humo Blanco',
  Yellow: 'Amarillo', YellowGreen: 'Verde Amarillo',
}

onMounted(async () => {
  try {
    const res = await fetch('https://www.csscolorsapi.com/api/colors')
    const data = await res.json()
    colorOptions.value = data.colors.map(c => ({
      name: colorTranslations[c.name] || c.name.replace(/([A-Z])/g, ' $1').trim(),
      hex: '#' + c.hex,
    }))
  } catch {
    colorOptions.value = [
      { name: 'Dorado', hex: '#EAB308' },
      { name: 'Rosa', hex: '#F472B6' },
      { name: 'Rojo', hex: '#EF4444' },
      { name: 'Azul', hex: '#3B82F6' },
      { name: 'Verde', hex: '#22C55E' },
    ]
  }
})

const colorSearch = ref('')
const showColorDropdown = ref(false)
const colorInputRef = ref(null)
const nativeColorRef = ref(null)

function openNativePicker() {
  nativeColorRef.value?.click()
}

function onNativeColor(e) {
  const hex = e.target.value.toUpperCase()
  form.value.color = hex
  const match = colorOptions.value.find(c => c.hex.toUpperCase() === hex)
  colorSearch.value = match ? match.name : hex
}

const filteredColors = computed(() => {
  if (!colorSearch.value) return colorOptions.value.slice(0, 20)
  const q = colorSearch.value.toLowerCase()
  return colorOptions.value.filter(c => c.name.toLowerCase().includes(q))
})

function selectColor(color) {
  form.value.color = color.hex
  colorSearch.value = color.name
  showColorDropdown.value = false
}

function onColorInput() {
  showColorDropdown.value = true
  const match = colorOptions.value.find(c => c.name.toLowerCase() === colorSearch.value.toLowerCase())
  if (match) {
    form.value.color = match.hex
  } else if (/^#[0-9A-Fa-f]{6}$/.test(colorSearch.value) || /^#[0-9A-Fa-f]{3}$/.test(colorSearch.value)) {
    form.value.color = colorSearch.value
  }
}

function onColorBlur() {
  setTimeout(() => { showColorDropdown.value = false }, 200)
}

const selectedColorName = computed(() => {
  const c = colorOptions.value.find(o => o.hex === form.value.color)
  return c ? c.name : form.value.color
})

function toggleProduct(product) {
  const idx = form.value.products.findIndex(p => p.id === product.id)
  if (idx >= 0) {
    form.value.products.splice(idx, 1)
  } else {
    form.value.products.push({ id: product.id })
  }
}

function isSelected(productId) {
  return form.value.products.some(p => p.id === productId)
}

const groupedProducts = computed(() => {
  const groups = {}
  props.products.forEach(p => {
    const cat = p.category?.name || 'Otros'
    if (!groups[cat]) groups[cat] = []
    groups[cat].push(p)
  })
  return groups
})

const selectedCount = computed(() => form.value.products.length)

function validateDate() {
  errors.value.event_date = null
  dateWarning.value = null

  if (!form.value.event_date) return

  const selected = new Date(form.value.event_date + 'T12:00:00')
  const minDate = new Date()
  minDate.setDate(minDate.getDate() + 15)
  minDate.setHours(12, 0, 0, 0)

  if (selected < minDate) {
    const day = minDate.getDate()
    const month = minDate.toLocaleDateString('es-AR', { month: 'long' })
    const year = minDate.getFullYear()
    errors.value.event_date = `Los eventos se piden con 15 días de anticipación. Elegí una fecha posterior al ${day} de ${month} de ${year}`
    return
  }

  if (props.occupiedDates.includes(form.value.event_date)) {
    dateWarning.value = 'Esa fecha ya tiene un evento. Podemos igualmente recibirla y Lily te avisará si puede.'
  }
}

const dateWarning = ref(null)

function todayStr() {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

async function submit() {
  errors.value = {}

  const nameErr = validateName()
  const whatsappErr = validateWhatsApp()
  liveErrors.value = { client_name: nameErr, client_whatsapp: whatsappErr }
  if (nameErr || whatsappErr || quantityError.value) return

  validateDate()
  if (errors.value.event_date) return

  // Capture push subscription if available
  try {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
      const reg = await navigator.serviceWorker.ready
      const sub = await reg.pushManager.getSubscription()
      if (sub) {
        form.value.push_endpoint = sub.endpoint
      }
    }
  } catch {}

  sending.value = true

  router.post('/eventos', form.value, {
    onError: (err) => {
      errors.value = err
      sending.value = false
    },
    onFinish: () => {
      sending.value = false
    },
  })
}

</script>

<template>
  <AppLayout>
    <Head title="Solicitar Evento" />

    <div class="bg-cream min-h-screen pb-24 lg:pb-8">
      <!-- Header -->
      <div class="bg-secondary py-8 sm:py-10 text-center relative">
        <a href="/" class="absolute top-4 left-4 w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
          </svg>
        </a>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-white mb-1">
          ¿Te gustaría contratar nuestros servicios?
        </h1>
        <p class="text-white/60 text-sm max-w-md mx-auto">
          Llevamos el auténtico sabor de Brasil a cumpleaños, casamientos, eventos empresariales y celebraciones
        </p>
      </div>

      <form @submit.prevent="submit" class="max-w-2xl mx-auto px-5 py-6 space-y-6">
        <!-- Datos del cliente -->
        <div class="bg-white rounded-2xl border border-primary/10 p-5 space-y-4">
          <h2 class="font-display font-bold text-base text-secondary">
            Tus datos
          </h2>

          <!-- Nombre -->
          <div>
            <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Nombre completo *</label>
            <input
              v-model="form.client_name"
              type="text"
              required
              placeholder="Tu nombre"
              @input="form.client_name = $event.target.value.replace(/[0-9]/g, ''); liveErrors.client_name = validateName()"
              class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary placeholder-stone-300
                     transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
              :class="liveErrors.client_name || errors.client_name ? 'border-red-300' : 'border-primary/15'"
            />
            <p v-if="liveErrors.client_name" class="text-red-500 text-xs mt-1">{{ liveErrors.client_name }}</p>
            <p v-else-if="errors.client_name" class="text-red-500 text-xs mt-1">{{ errors.client_name }}</p>
          </div>

          <!-- WhatsApp -->
          <div>
            <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">WhatsApp *</label>
            <input
              v-model="form.client_whatsapp"
              type="tel"
              inputmode="numeric"
              pattern="[0-9+\-\s]*"
              required
              placeholder="Ej: +54 9 11 1234-5678"
              maxlength="18"
              @input="form.client_whatsapp = $event.target.value.replace(/[^0-9+\-\s]/g, '').slice(0, 18); liveErrors.client_whatsapp = validateWhatsApp()"
              class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary placeholder-stone-300
                     transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
              :class="liveErrors.client_whatsapp || errors.client_whatsapp ? 'border-red-300' : 'border-primary/15'"
            />
            <p v-if="liveErrors.client_whatsapp" class="text-red-500 text-xs mt-1">{{ liveErrors.client_whatsapp }}</p>
            <p v-else-if="errors.client_whatsapp" class="text-red-500 text-xs mt-1">{{ errors.client_whatsapp }}</p>
          </div>

          <!-- Fecha y Horario -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Fecha del evento *</label>
              <input
                v-model="form.event_date"
                type="date"
                required
                :min="todayStr()"
                @change="validateDate"
                class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary
                       transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
                :class="errors.event_date ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="errors.event_date" class="text-red-500 text-xs mt-1">{{ errors.event_date }}</p>
              <p v-else-if="dateWarning" class="text-amber-600 text-[11px] mt-1">{{ dateWarning }}</p>
              <p v-else class="text-secondary/40 text-[11px] mt-1">Mínimo 15 días de anticipación</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Horario de retiro *</label>
              <input
                v-model="form.pickup_time"
                type="time"
                required
                min="08:00"
                class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary
                       transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
                :class="errors.pickup_time ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="errors.pickup_time" class="text-red-500 text-xs mt-1">{{ errors.pickup_time }}</p>
            </div>
          </div>

          <!-- Cantidad y Tipo -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Personas (máx. 100) *</label>
              <input
                v-model.number="form.quantity"
                type="number"
                min="1"
                max="100"
                required
                class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary
                       transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
                :class="quantityError || errors.quantity ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="quantityError" class="text-red-500 text-xs mt-1">{{ quantityError }}</p>
              <p v-else-if="errors.quantity" class="text-red-500 text-xs mt-1">{{ errors.quantity }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Tipo de evento *</label>
              <select
                v-model="form.event_type"
                class="w-full px-4 py-3 rounded-xl bg-cream border-2 border-primary/15 text-sm text-secondary
                       transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
              >
                <option value="cumpleanos">Cumpleaños</option>
                <option value="casamiento">Casamiento</option>
                <option value="corporativo">Corporativo</option>
                <option value="otro">Otro</option>
              </select>
            </div>
          </div>

          <!-- Color -->
          <div>
            <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Color del evento *</label>
            <div class="relative">
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="openNativePicker"
                  class="w-10 h-10 rounded-xl border-2 border-primary/15 shrink-0 transition-all active:scale-95 hover:shadow-md"
                  :style="{ backgroundColor: form.color }"
                  title="Elegir color"
                >
                  <input
                    ref="nativeColorRef"
                    type="color"
                    :value="form.color"
                    @input="onNativeColor"
                    class="sr-only"
                  />
                </button>
                <input
                  ref="colorInputRef"
                  v-model="colorSearch"
                  type="text"
                  required
                  placeholder="Escribí un color..."
                  class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary placeholder-stone-300
                         transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
                  :class="errors.color ? 'border-red-300' : 'border-primary/15'"
                  @input="onColorInput"
                  @focus="showColorDropdown = true"
                  @blur="onColorBlur"
                />
              </div>
              <Transition
                enter-active-class="transition-all duration-150"
                leave-active-class="transition-all duration-100"
                enter-from-class="opacity-0 -translate-y-1"
                leave-to-class="opacity-0 -translate-y-1"
              >
                <div
                  v-if="showColorDropdown && filteredColors.length"
                  class="absolute z-50 left-0 right-0 mt-1 bg-white rounded-xl border border-primary/10 shadow-lg max-h-48 overflow-y-auto"
                >
                  <button
                    v-for="c in filteredColors"
                    :key="c.hex"
                    type="button"
                    @mousedown.prevent="selectColor(c)"
                    class="w-full flex items-center gap-3 px-3 py-2 hover:bg-cream transition-colors text-left"
                  >
                    <div class="w-6 h-6 rounded-lg border border-primary/10 shrink-0" :style="{ backgroundColor: c.hex }" />
                    <span class="text-sm text-secondary">{{ c.name }}</span>
                  </button>
                </div>
              </Transition>
            </div>
            <p class="text-[10px] text-secondary/40 mt-1 text-center">{{ selectedColorName }}</p>
            <p v-if="errors.color" class="text-red-500 text-xs mt-1">{{ errors.color }}</p>
          </div>

          <!-- Observaciones -->
          <div>
            <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Observaciones</label>
            <textarea
              v-model="form.notes"
              rows="3"
              placeholder="Algo que quieras contarnos..."
              class="w-full px-4 py-3 rounded-xl bg-cream border-2 border-primary/15 text-sm text-secondary placeholder-stone-300
                     transition-colors outline-none focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)] resize-none"
            />
          </div>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-2xl border border-primary/10 p-5">
          <h2 class="font-display font-bold text-base text-secondary mb-1">
            Elegí los productos
          </h2>
          <p class="text-secondary/50 text-xs mb-4">
            Seleccioná lo que querés para tu evento. Las cantidades las definimos después.
          </p>

          <div v-if="products.length === 0" class="text-center py-8 text-secondary/40 text-sm">
            No hay productos disponibles para eventos
          </div>

          <div v-else class="space-y-5">
            <div v-for="(items, category) in groupedProducts" :key="category">
              <h3 class="font-display font-semibold text-sm text-secondary/70 mb-2">{{ category }}</h3>
              <div class="space-y-2">
                <div
                  v-for="product in items"
                  :key="product.id"
                  class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer"
                  :class="isSelected(product.id)
                    ? 'border-primary bg-primary/5'
                    : 'border-primary/10 hover:border-primary/25'"
                  @click="toggleProduct(product)"
                >
                  <!-- Checkbox visual -->
                  <div
                    class="w-5 h-5 rounded-md border-2 flex items-center justify-center shrink-0 transition-colors"
                    :class="isSelected(product.id) ? 'bg-primary border-primary' : 'border-stone-300'"
                  >
                    <svg v-if="isSelected(product.id)" class="w-3 h-3 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>

                  <!-- Foto miniatura -->
                  <div v-if="product.image_path" class="shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-cream">
                    <img :src="product.image_path" :alt="product.name" loading="lazy" class="w-full h-full object-cover" />
                  </div>

                  <!-- Info -->
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-secondary truncate">{{ product.name }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <p v-if="errors.products" class="text-red-500 text-xs mt-3">{{ errors.products }}</p>
        </div>

        <!-- Resumen simple -->
        <div v-if="selectedCount > 0" class="bg-white rounded-2xl border border-primary/10 p-5">
          <h2 class="font-display font-bold text-base text-secondary mb-2">
            Elegiste {{ selectedCount }} producto{{ selectedCount > 1 ? 's' : '' }}
          </h2>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="p in form.products"
              :key="p.id"
              class="px-3 py-1 rounded-full bg-primary/10 text-xs font-semibold text-primary-dark"
            >
              {{ products.find(pr => pr.id === p.id)?.name }}
            </span>
          </div>
        </div>

        <!-- Modalidad de pago -->
        <div class="bg-white rounded-2xl border border-primary/10 p-5 space-y-3">
          <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-base text-secondary">
              Modalidad de pago
            </h2>
            <button
              type="button"
              @click="showPaymentModal = true"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-800
                     hover:bg-amber-100 active:scale-[0.98] transition-all"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="16" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12.01" y2="8" />
              </svg>
              ¿Cómo funciona?
            </button>
          </div>
          <p class="text-xs text-secondary/50">Elegí cómo querés abonar el pedido:</p>
          <label
            class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="form.payment_plan === 'deposit'
              ? 'border-primary bg-primary/5'
              : 'border-primary/10 hover:border-primary/25'"
          >
            <input
              v-model="form.payment_plan"
              type="radio"
              value="deposit"
              class="sr-only"
            />
            <span
              class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
              :class="form.payment_plan === 'deposit' ? 'border-primary bg-primary' : 'border-stone-300'"
            >
              <span v-if="form.payment_plan === 'deposit'" class="w-2 h-2 rounded-full bg-white" />
            </span>
            <div>
              <p class="text-sm font-semibold text-secondary">Seña del 50%</p>
              <p class="text-[11px] text-stone-400">Abonás la mitad al confirmar y el resto antes del evento</p>
            </div>
          </label>
          <label
            class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="form.payment_plan === 'full'
              ? 'border-primary bg-primary/5'
              : 'border-primary/10 hover:border-primary/25'"
          >
            <input
              v-model="form.payment_plan"
              type="radio"
              value="full"
              class="sr-only"
            />
            <span
              class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
              :class="form.payment_plan === 'full' ? 'border-primary bg-primary' : 'border-stone-300'"
            >
              <span v-if="form.payment_plan === 'full'" class="w-2 h-2 rounded-full bg-white" />
            </span>
            <div>
              <p class="text-sm font-semibold text-secondary">Pago total</p>
              <p class="text-[11px] text-stone-400">Abonás el 100% en un solo pago</p>
            </div>
          </label>
        </div>

        <button
          type="submit"
          :disabled="sending || selectedCount === 0"
          class="w-full py-4 rounded-2xl bg-[#25D366] text-white font-display font-bold text-base
                 shadow-lg shadow-[#25D366]/25 active:scale-[0.98] transition-all duration-300
                 disabled:opacity-50 disabled:cursor-not-allowed
                 hover:bg-[#20bd5a] flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
          </svg>
          {{ sending ? 'Enviando...' : 'Enviar por WhatsApp' }}
        </button>
      </form>

      <!-- Modal Cómo funciona la reserva -->
      <Teleport to="body">
        <Transition
          enter-active-class="transition-opacity duration-200"
          leave-active-class="transition-opacity duration-200"
          enter-from-class="opacity-0"
          leave-to-class="opacity-0"
        >
          <div
            v-if="showPaymentModal"
            class="fixed inset-0 z-[90] bg-secondary/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-4"
            @click="showPaymentModal = false"
          >
            <div
              class="bg-white rounded-3xl shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto border border-primary/10"
              @click.stop
            >
              <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-primary/5">
                <h2 class="text-lg font-display font-bold text-secondary">¿Cómo funciona la reserva?</h2>
                <button
                  @click="showPaymentModal = false"
                  class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-primary/10 transition-colors"
                >
                  <svg class="w-5 h-5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </button>
              </div>
              <div class="px-5 py-4 space-y-3">
                <p class="text-sm text-secondary/70">
                  Para confirmar tu pedido o evento trabajamos de la siguiente manera:
                </p>
                <div class="space-y-2.5">
                  <div class="flex items-start gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center shrink-0 text-green-600 text-xs font-bold">✓</span>
                    <p class="text-sm text-secondary">Podés reservar abonando una <strong>seña del 50%</strong>.</p>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center shrink-0 text-green-600 text-xs font-bold">✓</span>
                    <p class="text-sm text-secondary">El <strong>50% restante</strong> se paga al momento de la entrega o del evento.</p>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center shrink-0 text-green-600 text-xs font-bold">✓</span>
                    <p class="text-sm text-secondary">También podés pagar el <strong>100%</strong> si lo preferís.</p>
                  </div>
                </div>
                <p class="text-sm text-stone-400 italic bg-cream rounded-xl p-3">
                  La seña nos permite reservar la fecha y comenzar la preparación de tu pedido.
                </p>
              </div>
              <div class="px-5 pb-5">
                <button
                  @click="showPaymentModal = false"
                  class="w-full py-3 rounded-xl bg-secondary text-cream font-display font-bold text-sm
                         shadow-md shadow-secondary/20 hover:bg-secondary-dark active:scale-[0.98] transition-all"
                >
                  Entendido
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>
    </div>
  </AppLayout>
</template>
