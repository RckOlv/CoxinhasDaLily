<script setup>
defineProps({
  loading: { type: Boolean, default: false },
  mainText: { type: String, default: 'Subiendo archivos...' },
  subText: { type: String, default: 'Preparando todo...' },
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
        <video
          src="/videos/coxinha-loader.mp4"
          autoplay
          loop
          muted
          playsinline
          class="loader-video"
        />
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

.loader-video {
  width: 140px;
  height: 140px;
  object-fit: contain;
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
