import { ref, onMounted, onBeforeUnmount } from 'vue'

const prefersReducedMotion = ref(false)
let observer = null

function getObserver() {
  if (!observer) {
    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const el = entry.target
            el.classList.add('revealed')
            observer.unobserve(el)
          }
        })
      },
      { threshold: 0.2 }
    )
  }
  return observer
}

let reducedMotionMediaQuery = null

export function useReveal() {
  onMounted(() => {
    reducedMotionMediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')
    prefersReducedMotion.value = reducedMotionMediaQuery.matches
    reducedMotionMediaQuery.addEventListener('change', onMotionChange)
  })

  onBeforeUnmount(() => {
    if (reducedMotionMediaQuery) {
      reducedMotionMediaQuery.removeEventListener('change', onMotionChange)
    }
  })

  function onMotionChange(e) {
    prefersReducedMotion.value = e.matches
  }

  function observe(el) {
    if (!el) return
    if (prefersReducedMotion.value) {
      el.classList.add('revealed')
      return
    }
    getObserver().observe(el)
  }

  function unobserve(el) {
    if (!el || !observer) return
    observer.unobserve(el)
  }

  return { observe, unobserve, prefersReducedMotion }
}
