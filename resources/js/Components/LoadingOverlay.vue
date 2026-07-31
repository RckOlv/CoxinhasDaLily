<script setup>
defineProps({
  loading: { type: Boolean, default: false },
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
        <div class="loader-coxinha">
          <img src="/img/coxinha-loader.png" alt="Cargando" class="loader-coxinha-img" />
        </div>
        <p class="loader-text-main">Subiendo archivos...</p>
        <p class="loader-text-sub">Preparando todo...</p>
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

.loader-coxinha {
  position: relative;
  width: 96px;
  height: 96px;
  animation: coxinha-bounce 1.2s cubic-bezier(0.28, 0.84, 0.42, 1) infinite;
}

.loader-coxinha-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  animation: coxinha-rock 1.2s ease-in-out infinite;
  will-change: transform;
}

.loader-coxinha::after {
  content: '';
  position: absolute;
  bottom: -14px;
  left: 50%;
  transform: translateX(-50%);
  width: 72px;
  height: 12px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.35);
  filter: blur(2px);
  animation: coxinha-shadow 1.2s cubic-bezier(0.28, 0.84, 0.42, 1) infinite;
}

.loader-text-main {
  margin-top: 32px;
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

@keyframes coxinha-bounce {
  0% {
    transform: translateY(0) scale(1, 1);
  }
  40% {
    transform: translateY(-46px) scale(1.02, 0.98);
  }
  55% {
    transform: translateY(-48px) scale(1.04, 0.96);
  }
  100% {
    transform: translateY(0) scale(0.96, 1.04);
  }
}

@keyframes coxinha-rock {
  0% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(5deg);
  }
  50% {
    transform: rotate(0deg);
  }
  75% {
    transform: rotate(-5deg);
  }
  100% {
    transform: rotate(0deg);
  }
}

@keyframes coxinha-shadow {
  0% {
    transform: translateX(-50%) scale(1);
    opacity: 0.9;
  }
  40% {
    transform: translateX(-50%) scale(0.72);
    opacity: 0.5;
  }
  55% {
    transform: translateX(-50%) scale(0.66);
    opacity: 0.45;
  }
  100% {
    transform: translateX(-50%) scale(1);
    opacity: 0.9;
  }
}

@media (prefers-reduced-motion: reduce) {
  .loader-coxinha,
  .loader-coxinha-img,
  .loader-coxinha::after {
    animation: none;
  }
}
</style>
