<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useCart } from '@/Composables/useCart'
import CartOffcanvas from '@/Components/CartOffcanvas.vue'

const { itemCount } = useCart()
const showCart = ref(false)

const page = usePage()
const isHome = computed(() => page.url === '/')

const scrolled = ref(false)

function onScroll() {
  scrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  onScroll()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

<template>
  <div class="min-h-screen bg-cream">
    <!-- Header -->
    <header
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="
        isHome
          ? (scrolled
              ? 'lg:bg-cream/95 lg:border-b lg:border-primary/10 lg:shadow-sm lg:backdrop-blur-sm bg-transparent'
              : 'bg-transparent')
          : 'bg-cream/95 border-b border-primary/10 shadow-sm backdrop-blur-sm'
      "
    >
      <div class="flex items-center justify-between h-16 px-4 sm:px-6 max-w-7xl mx-auto">
        <!-- Logo (solo en páginas que no son Home) -->
        <Link v-if="!isHome" href="/" class="flex items-center gap-2.5">
          <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-10 w-auto" />
        </Link>
        <div v-else />

        <!-- Nav Desktop -->
        <nav class="hidden lg:flex items-center gap-1">
          <Link
            href="/"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url === '/' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url === '/' ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url === '/' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
          >
            Inicio
          </Link>
          <Link
            href="/productos"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url === '/productos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url === '/productos' ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url === '/productos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
          >
            Productos
          </Link>
        </nav>

        <!-- Carrito -->
        <button
          @click="showCart = true"
          class="hidden lg:relative lg:flex items-center justify-center w-10 h-10 rounded-xl transition-colors active:scale-95"
          :class="isHome
            ? (scrolled ? 'hover:bg-primary/10 text-secondary' : 'hover:bg-white/10 text-white')
            : 'hover:bg-primary/10 text-secondary'"
        >
          <svg
            class="w-6 h-6"
            :class="isHome
              ? (scrolled ? 'text-secondary' : 'text-white')
              : 'text-secondary'"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          >
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
          <span
            v-if="itemCount > 0"
            class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[20px] h-5 px-1 text-[11px] font-bold text-secondary bg-primary rounded-full shadow-md shadow-primary/30 ring-2"
            :class="isHome ? (scrolled ? 'ring-cream' : 'ring-black/20') : 'ring-cream'"
          >
            {{ itemCount > 99 ? '99+' : itemCount }}
          </span>
        </button>
      </div>
    </header>

    <!-- Contenido principal -->
    <main class="pb-24 lg:pb-8" :class="isHome ? '' : 'pt-16'">
      <slot />
    </main>

    <!-- Bottom Nav Bar (solo mobile/tablet) -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-cream/95 border-t border-primary/10 shadow-[0_-4px_20px_rgba(120,53,15,0.06)] backdrop-blur-sm lg:hidden">
      <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
        <Link
          href="/"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
          </svg>
          Inicio
        </Link>
        <Link
          href="/productos"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/productos' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
          </svg>
          Productos
        </Link>
        <button
          @click="showCart = true"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors relative"
          :class="showCart ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
          <span
            v-if="itemCount > 0"
            class="absolute top-0 right-2 flex items-center justify-center min-w-[16px] h-4 px-1 text-[10px] font-bold text-secondary bg-primary rounded-full"
          >
            {{ itemCount > 99 ? '99+' : itemCount }}
          </span>
          Carrito
        </button>
      </div>
    </nav>

    <!-- Cart Offcanvas -->
    <CartOffcanvas :open="showCart" @close="showCart = false" />
  </div>
</template>
