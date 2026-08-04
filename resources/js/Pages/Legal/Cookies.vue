<script setup>
import LegalLayout from '@/Components/LegalLayout.vue'
</script>

<template>
  <LegalLayout title="Política de Cookies" updated="01 de agosto de 2026">
    <section>
      <h2 class="font-display font-bold text-lg text-secondary mb-2">1. ¿Qué son las cookies?</h2>
      <p>
        Las cookies son pequeños archivos de texto que el sitio guarda en tu dispositivo para que el
        sitio funcione correctamente o para recopilar información sobre el uso. También utilizamos
        almacenamiento local del navegador (localStorage) para funciones como el carrito de compras.
      </p>
    </section>

    <section>
      <h2 class="font-display font-bold text-lg text-secondary mb-2">2. Cookies que utilizamos</h2>

      <h3 class="font-display font-bold text-base text-secondary mt-4 mb-1">Cookies técnicas (imprescindibles)</h3>
      <p>
        Necesarias para el funcionamiento del sitio y la seguridad: cookie de sesión de Laravel y token
        de protección CSRF. Se instalan automáticamente y no requieren consentimiento.
      </p>

      <h3 class="font-display font-bold text-base text-secondary mt-4 mb-1">Cookies de analítica (Google Analytics)</h3>
      <p>
        Si aceptás, utilizamos Google Analytics para conocer cómo se usa el sitio (páginas visitadas,
        tiempo de permanencia, origen del tráfico) con datos agregados y anónimos. Estas cookies
        (<code>_ga</code>, <code>_gid</code>, <code>_ga_*</code>) <strong>solo se instalan con tu
        consentimiento</strong>.
      </p>

      <h3 class="font-display font-bold text-base text-secondary mt-4 mb-1">Almacenamiento local</h3>
      <p>
        Guardamos en el almacenamiento local de tu navegador el contenido de tu carrito y tus
        preferencias de consentimiento. Estos datos no se envían a terceros y podés borrarlos desde la
        configuración de tu navegador.
      </p>
    </section>

    <section>
      <h2 class="font-display font-bold text-lg text-secondary mb-2">3. Cómo administrar las cookies</h2>
      <ul class="list-disc pl-5 space-y-2">
        <li>
          Al ingresar al sitio verás un aviso donde podés <strong>aceptar</strong> o <strong>rechazar</strong>
          las cookies de analítica. Si rechazás, no se instalan cookies de Google Analytics.
        </li>
        <li>
          También podés configurar o bloquear las cookies desde la configuración de tu navegador
          (Chrome, Firefox, Safari, Edge).
        </li>
        <li>
          Podés consultar la política de privacidad de Google sobre sus cookies en
          <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="text-primary underline">
            policies.google.com/privacy
          </a>.
        </li>
      </ul>
    </section>

    <section>
      <h2 class="font-display font-bold text-lg text-secondary mb-2">4. Cambios en esta política</h2>
      <p>
        Podemos actualizar esta política cuando cambien los servicios o tecnologías utilizadas. Los
        cambios se publicarán en esta página.
      </p>
    </section>

    <section>
      <h2 class="font-display font-bold text-lg text-secondary mb-2">5. Contacto</h2>
      <p>
        Ante cualquier consulta sobre el uso de cookies, escribinos a <strong>correo@ejemplo.com</strong>.
      </p>
    </section>
  </LegalLayout>
</template>
