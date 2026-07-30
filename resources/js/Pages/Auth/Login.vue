<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const showPassword = ref(false)

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Iniciar sesión" />

    <div class="bg-white rounded-2xl shadow-sm border border-primary/10 p-6">
      <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 mb-4">
        <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
          <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        <p class="text-xs text-amber-800 font-medium">Acceso privado · Solo para administradores</p>
      </div>
      <h1 class="font-display font-bold text-xl text-secondary text-center mb-1">Panel de Lily</h1>
      <p class="text-sm text-secondary/50 text-center mb-6">Ingresá para gestionar tus productos</p>

      <div v-if="status" class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 text-center">
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email -->
        <div>
          <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            autofocus
            autocomplete="username"
            placeholder="admin@lily.com"
            class="w-full px-4 py-3 rounded-xl bg-cream border-2 text-sm text-secondary placeholder-stone-300
                   transition-colors outline-none
                   focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]
                   invalid:border-red-400"
            :class="form.errors.email ? 'border-red-300' : 'border-primary/15'"
          />
          <InputError class="mt-1.5" :message="form.errors.email" />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Contraseña</label>
          <div class="relative">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full px-4 py-3 pr-12 rounded-xl bg-cream border-2 text-sm text-secondary placeholder-stone-300
                     transition-colors outline-none
                     focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]
                     invalid:border-red-400"
              :class="form.errors.password ? 'border-red-300' : 'border-primary/15'"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-secondary transition-colors"
            >
              <svg v-if="!showPassword" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                <line x1="1" y1="1" x2="23" y2="23" />
              </svg>
            </button>
          </div>
          <InputError class="mt-1.5" :message="form.errors.password" />
        </div>

        <!-- Recordarme -->
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            v-model="form.remember"
            type="checkbox"
            class="w-4 h-4 rounded border-stone-300 text-primary focus:ring-primary/30 cursor-pointer"
          />
          <span class="text-sm text-secondary/60">Recordarme</span>
        </label>

        <!-- Botón -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 rounded-xl bg-primary text-secondary font-display font-bold text-sm
                 shadow-md shadow-primary/25 active:scale-[0.98] transition-all
                 disabled:opacity-50 disabled:cursor-not-allowed
                 hover:bg-primary-dark hover:text-white"
        >
          {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </form>

      <div class="mt-4 text-center">
        <p class="text-xs text-secondary/40">
          ¿Olvidaste tu contraseña?
          <a
            :href="`https://wa.me/5493755550471?text=Hola%20Ricky%2C%20necesito%20cambiar%20mi%20contrase%C3%B1a%20del%20panel`"
            target="_blank"
            rel="noopener noreferrer"
            class="text-primary hover:text-primary-dark transition-colors font-medium"
          >
            Contactate conmigo
          </a>
        </p>
      </div>
    </div>

    <Link href="/" class="mt-6 w-full flex items-center justify-center gap-2 py-3 rounded-xl border-2 border-primary/15 text-sm font-semibold text-secondary
                  hover:bg-primary/5 hover:border-primary/30 active:scale-[0.98] transition-all">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6" />
      </svg>
      Volver al inicio
    </Link>
  </GuestLayout>
</template>
