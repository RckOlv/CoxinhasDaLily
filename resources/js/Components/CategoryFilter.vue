<script setup>
defineProps({
  categories: { type: Array, required: true },
  selected: { type: [Number, null], default: null },
})

const emit = defineEmits(['select'])
</script>

<template>
  <div class="relative">
    <!-- Fade edges -->
    <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-6 bg-gradient-to-r from-cream to-transparent z-10" />
    <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-6 bg-gradient-to-l from-cream to-transparent z-10" />

    <div class="flex gap-2 overflow-x-auto px-4 sm:px-6 lg:px-8 py-2 scrollbar-none" style="-ms-overflow-style:none; scrollbar-width:none;">
      <!-- Botón "Todos" -->
      <button
        @click="emit('select', null)"
        class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all duration-150 whitespace-nowrap"
        :class="
          selected === null
            ? 'bg-primary text-secondary border-primary shadow-md shadow-primary/20'
            : 'bg-white text-stone-400 border-primary/10 hover:border-primary/30'"
        "
      >
        Todos
      </button>

      <button
        v-for="cat in categories"
        :key="cat.id"
        @click="emit('select', cat.id)"
        class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all duration-150 whitespace-nowrap"
        :class="
          selected === cat.id
            ? 'bg-primary text-secondary border-primary shadow-md shadow-primary/20'
            : 'bg-white text-stone-400 border-primary/10 hover:border-primary/30'"
        "
      >
        {{ cat.name }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
</style>
