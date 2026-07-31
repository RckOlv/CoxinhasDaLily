# Coxinhas da Lily

Plataforma web de venta y administración para **Coxinhas da Lily**, emprendimiento de coxinhas y salgados brasileños artesanales en Posadas, Misiones.

El sitio público permite conocer el catálogo, pedir por WhatsApp, reservar eventos y ver contenido (fotos y videos), mientras que el panel de administración centraliza productos, stock, pedidos, eventos, galería, videos y notificaciones push.

## Stack

- **Laravel 13** (PHP)
- **Inertia.js v2** + **Vue 3** (Composition API)
- **Tailwind CSS** + **Vite**
- **lottie-web** (animación del loader)
- **MySQL / SQLite**

## Funcionalidades

### Sitio público
- Home con hero, presentación, videos de producción, galería y sección de cursos.
- Catálogo de productos con categorías, indicador de stock y badges.
- Carrito de compras con checkout que envía el pedido por **WhatsApp**.
- Formulario de reservas para eventos (fecha, cantidad de personas, modalidad de pago) enviado por WhatsApp.
- Notificaciones push para avisar a los clientes sobre sus pedidos.

### Panel de administración (`/admin`)
- **Dashboard** con resumen general.
- **Productos**: alta, edición, baja y control de stock.
- **Categorías**: CRUD para organizar el catálogo.
- **Eventos**: gestión de reservas y productos por evento.
- **Pedidos**: visualización y actualización de estados.
- **Galería**: subida, orden y eliminación de fotos.
- **Videos**: subida y gestión de videos de producción.
- **Notificaciones push**: envío de avisos a los suscriptores.
