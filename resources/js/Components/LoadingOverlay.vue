<script setup>
import { onBeforeUnmount, ref, watch } from 'vue'
import lottie from 'lottie-web'

const props = defineProps({
  loading: { type: Boolean, default: false },
  mainText: { type: String, default: 'Subiendo archivos...' },
  subText: { type: String, default: 'Preparando todo...' },
})

const animationRef = ref(null)
let animation = null

const ensureAnimation = () => {
  if (animation || !animationRef.value) return
  animation = lottie.loadAnimation({
    container: animationRef.value,
    renderer: 'svg',
    loop: true,
    autoplay: true,
    path: '/img/Beef-Burger.json',
  })
}

watch(
  () => props.loading,
  (val) => {
    if (val) {
      ensureAnimation()
      animation?.play()
    } else {
      animation?.stop()
    }
  },
  { flush: 'post' },
)

onBeforeUnmount(() => {
  animation?.destroy()
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="overlay-fade-enter-active"
      leave-active-class="overlay-fade-leave-active"
      enter-from-class="overlay-fade-enter-from"
      leave-to-class="overlay-fade-leave-to"
    >
      <div
        v-if="loading"
        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center"
        style="background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
      >
        <div ref="animationRef" class="loader-animation" />
        <p class="loader-text-main">{{ mainText }}</p>
        <p class="loader-text-sub">{{ subText }}</p>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.25s ease;
}
.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

.loader-animation {
  width: 240px;
  height: 240px;
}

.loader-text-main {
  margin-top: 16px;
  color: #fff;
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
}

.loader-text-sub {
  margin-top: 6px;
  color: rgba(255, 255, 255, 0.65);
  font-size: 0.8rem;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
}
</style>
