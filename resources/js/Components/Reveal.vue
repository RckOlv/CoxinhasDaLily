<script setup>
import { ref, computed, onMounted } from 'vue'
import { useReveal } from '@/Composables/useReveal'

const props = defineProps({
  direction: { type: String, default: 'up', validator: v => ['up', 'down', 'left', 'right'].includes(v) },
  delay: { type: Number, default: 0 },
  duration: { type: Number, default: 550 },
  distance: { type: Number, default: 30 },
  as: { type: String, default: 'div' },
  threshold: { type: Number, default: 0.2 },
})

const el = ref(null)
const { observe, prefersReducedMotion } = useReveal()

const style = computed(() => {
  if (prefersReducedMotion.value) return {}
  const transforms = {
    up: `translateY(${props.distance}px)`,
    down: `translateY(${-props.distance}px)`,
    left: `translateX(${props.distance}px)`,
    right: `translateX(${-props.distance}px)`,
  }
  return {
    transition: `opacity ${props.duration}ms ease-out, transform ${props.duration}ms ease-out`,
    transitionDelay: `${props.delay}ms`,
    opacity: 0,
    transform: transforms[props.direction],
  }
})

onMounted(() => {
  observe(el.value)
})
</script>

<template>
  <component :is="as" ref="el" class="reveal" :style="style">
    <slot />
  </component>
</template>
