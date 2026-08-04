<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  title: { type: String, required: true },
  updated: { type: String, required: true },
})
</script>

<template>
  <AppLayout>
    <section class="bg-cream py-12 sm:py-16 min-h-screen">
      <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8">
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-secondary mb-1">
          {{ title }}
        </h1>
        <p class="text-xs text-secondary/40 mb-8">Última actualización: {{ updated }}</p>
        <div class="space-y-6 text-secondary/75 text-sm sm:text-base leading-relaxed">
          <slot />
        </div>
      </div>
    </section>
  </AppLayout>
</template>
