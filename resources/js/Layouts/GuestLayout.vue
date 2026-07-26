<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-cream flex flex-col items-center justify-center px-4">
    <Link href="/" class="mb-8">
      <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-20 w-auto" />
    </Link>

    <div class="w-full max-w-sm">
      <slot />
    </div>
  </div>
</template>
