# Alcance Funcional — Coxinhas da Lily

## Sistema de E-commerce y Gestión de Eventos

---

## 1. Resumen

Sistema web completo para la venta de productos gastronómicos artesanales (coxinhas, churros, empanadas, salgados) y gestión de eventos sociales (cumpleaños, casamientos, corporativos). Incluye catálogo público, carrito de compras, checkout vía WhatsApp, panel de administración y sistema de pedidos con reserva de eventos.

---

## 2. Funcionalidades Públicas (Tienda)

### 2.1 Landing Page
- Hero con carrusel de imágenes promocionales
- Sección "Quién soy" con descripción del emprendimiento
- Carrusel de videos dinámicos (desde panel admin)
- Carrusel de galería de fotos (desde panel admin)
- Sección de eventos destacados
- Llamada a la acción para pedir por WhatsApp
- Botón flotante de WhatsApp directo

### 2.2 Catálogo de Productos
- Listado de productos agrupados por categoría
- Imagen, nombre, precio, descripción y badge (ej: "Apto freezer", "Nuevo")
- Indicador de stock disponible
- Vista de unidades por paquete
- Botón "Agregar al carrito" por producto

### 2.3 Carrito de Compras
- Offcanvas lateral con productos seleccionados
- Sumar/restar cantidades desde el carrito
- Precio total calculado en tiempo real
- Persistencia entre páginas

### 2.4 Checkout (WhatsApp)
- Formulario: nombre, WhatsApp, método de entrega (retiro/dirección), método de pago
- Validación de campos (nombre sin números, teléfono numérico, etc.)
- Al confirmar, crea el pedido en la base de datos
- Redirige a WhatsApp con mensaje pre-armado con todos los detalles del pedido

### 2.5 Formulario de Eventos
- Selección de fecha (mínimo 15 días de anticipación)
- Verificación de fechas ocupadas
- Tipos de evento: cumpleaños, casamiento, corporativo, otro
- Cantidad de personas (mínimo 100)
- Horario de retiro
- Color del evento (selector con búsqueda + 148 colores CSS + selector nativo)
- Selección de productos desde el catálogo (agrupados por categoría)
- Observaciones (texto libre)
- Límite: 2 eventos por fin de semana, 12 por mes
- Al confirmar, crea el evento en la BD y redirige a WhatsApp

---

## 3. Panel de Administración

### 3.1 Dashboard (Pantalla principal post-login)
- Resumen visual con tarjetas de:
  - Pedidos pendientes del día
  - Próximos eventos (ventana de 15 días)
  - Productos con stock bajo / sin stock
  - Eventos del mes (vs límite de 12)
- Lista de próximos eventos con fechas y estados
- Alertas de stock bajo
- Accesos directos a las secciones principales

### 3.2 Gestión de Productos
- CRUD completo: crear, editar, eliminar productos
- Campos: nombre, descripción, precio, imagen, stock, badge, unidades por paquete
- Edición inline de stock desde la lista
- Eliminación directa desde la tarjeta del producto
- Categorización de productos

### 3.3 Gestión de Categorías
- CRUD completo de categorías
- Los productos se agrupan por categoría en el catálogo

### 3.4 Gestión de Eventos
- Lista con filtros por estado (todos, pendiente, confirmado, completado, cancelado)
- Filtro por fecha (hoy, semana, mes)
- Vista de lista con acordeón expandible
- Vista de calendario con indicadores de días ocupados
- Edición de cantidades de productos por evento (con precios estimados)
- Cambio de estado del evento
- Indicador visual de color del evento + nombre del color
- Observaciones visibles en vista compacta

### 3.5 Gestión de Pedidos
- Lista con filtros por estado (todos, pendiente, confirmado, entregado, cancelado)
- Filtro por fecha (hoy, semana, mes)
- Vista de lista con acordeón expandible (detalles: productos, total, método de pago, dirección)
- Vista de calendario con indicadores de pedidos por día
- Cambio de estado: pendiente → confirmado (descuenta stock) → entregado → cancelado
- Confirmación con SweetAlert2 antes de cada cambio

### 3.6 Gestión de Galería
- Subida de imágenes para la landing page
- Vista previa en el admin
- Activar/desactivar visibilidad
- Eliminación de imágenes

### 3.7 Gestión de Videos
- Subida de videos con título personalizado
- Vista previa en el admin
- Activar/desactivar visibilidad
- Eliminación de videos

---

## 4. Notificaciones

### 4.1 Push Notifications (Por Web)
- Suscripción de clientes desde el checkout
- Envío de notificación al cliente cuando su pedido es confirmado
- Envío de notificación al cliente cuando su pedido es entregado
- Notificación por cliente (no broadcast)

---

## 5. Diseño y UX

- **Mobile-first**: Diseñado prioritariamente para celulares
- **Navegación inferior** en mobile con acceso rápido a secciones principales
- **Navegación superior** en desktop con tabs
- **Paleta de colores personalizada**: primary (amarillo), secondary (marrón), cream (fondo)
- **Tipografía**: Fredoka (títulos), Inter (texto)
- **Responsive**: Adaptado a celulares, tablets y desktop
- **Transiciones y animaciones** suaves
- **SweetAlert2** para confirmaciones y toasts
- **Google Analytics** preparado para activar (solo falta el ID)

---

## 6. Stack Técnico

| Componente | Tecnología |
|-----------|-----------|
| Backend | Laravel 11 (PHP 8.4) |
| Frontend | Vue 3 con Inertia.js |
| CSS | Tailwind CSS v4 |
| Compilación | Vite 8 |
| Base de datos | SQLite (MySQL-ready) |
| Autenticación | Laravel Breeze |
| Push notifications | Web Push API + VAPID |

---

## 7. Presupuesto Sugerido

### Desarrollo
- **Sistema completo + puesta en marcha**: $250.000 ARS
- **Incluye**: todas las funcionalidades detalladas arriba, instalación en hosting, configuración de dominio y SSL

### Hosting
- **DonWeb 48 meses**: $120.000 ARS
- **Incluye**: hosting compartido, dominio, SSL, soporte del proveedor

### Mantenimiento mensual (opcional)
- **$10.000 ARS/mes**
- **Incluye**: corrección de bugs, backups, soporte técnico, 1 hora de consulta mensual

---

*Documento generado el 29 de Julio de 2026*
