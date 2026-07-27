<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const tabs = [
  { href: '/admin/productos', label: 'Productos', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  { href: '/admin/categorias', label: 'Categorías', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
  { href: '/admin/eventos', label: 'Eventos', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { href: '/admin/pedidos', label: 'Pedidos', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
]

function isActive(href) {
  return page.url.startsWith(href)
}
</script>

<template>
  <div class="sticky top-0 z-40 bg-cream/95 backdrop-blur-sm border-b border-primary/10">
    <div class="flex items-center justify-between h-14 px-4 sm:px-6 max-w-5xl mx-auto">
      <!-- Logo izquierda -->
      <Link href="/" class="shrink-0">
        <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-10 w-auto" />
      </Link>

      <!-- Tabs centro -->
      <nav class="hidden sm:flex items-center gap-1">
        <Link
          v-for="tab in tabs"
          :key="tab.href"
          :href="tab.href"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
          :class="isActive(tab.href)
            ? 'bg-secondary text-cream'
            : 'text-secondary/50 hover:text-secondary hover:bg-secondary/10'"
        >
          {{ tab.label }}
        </Link>
      </nav>

      <!-- Tabs mobile (scrollable) -->
      <nav class="flex sm:hidden items-center gap-1 overflow-x-auto">
        <Link
          v-for="tab in tabs"
          :key="tab.href"
          :href="tab.href"
          class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold whitespace-nowrap transition-all"
          :class="isActive(tab.href)
            ? 'bg-secondary text-cream'
            : 'text-secondary/50 hover:text-secondary hover:bg-secondary/10'"
        >
          {{ tab.label }}
        </Link>
      </nav>

      <!-- Derecha -->
      <div class="flex items-center gap-2">
        <Link
          href="/"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-secondary/10 text-secondary/70 hover:bg-secondary/20 transition-all"
        >
          Ver tienda
        </Link>
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-500 hover:bg-red-100 transition-all"
        >
          Salir
        </Link>
      </div>
    </div>
  </div>
</template>
