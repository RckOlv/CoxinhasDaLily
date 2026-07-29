# Alcance Funcional — Coxinhas da Lily

## Sistema de Ventas y Eventos

---

## 1. Resumen

Sistema web para la venta de productos gastronómicos artesanales (coxinhas, churros, empanadas, salgados) y gestión de eventos (cumpleaños, casamientos, corporativos). Incluye tienda virtual, carrito de compras, pedidos por WhatsApp, y un panel privado para administrar todo.

---

## 2. Funcionalidades Públicas (Lo que ve el cliente)

### 2.1 Página Principal
- Carrusel de imágenes promocionales
- Sección "Quién soy" con la historia del emprendimiento
- Videos automáticos (se suben desde el panel)
- Galería de fotos (se suben desde el panel)
- Eventos destacados
- Botón directo a WhatsApp

### 2.2 Catálogo de Productos
- Productos ordenados por categoría
- Foto, nombre, precio, descripción y etiqueta (ej: "Apto freezer", "Nuevo")
- Stock disponible
- Unidades por paquete
- Botón "Agregar al carrito"

### 2.3 Carrito de Compras
- Panel lateral con los productos elegidos
- Sumar o restar cantidades
- Precio total calculado al instante
- El carrito guarda lo seleccionado aunque navegues por la página

### 2.4 Proceso de Pedido (WhatsApp)
- Formulario: nombre, WhatsApp, cómo retira (pasa a buscar / envío), forma de pago
- Validación: el nombre no acepta números, el teléfono no acepta letras
- Al confirmar, el pedido queda guardado
- Te redirige a WhatsApp con el mensaje listo con todos los detalles

### 2.5 Formulario de Eventos
- Elegir fecha (mínimo 15 días antes)
- Verifica que la fecha no esté ocupada
- Tipo de evento: cumpleaños, casamiento, corporativo, otro
- Cantidad de personas (mínimo 100)
- Horario de retiro
- Elegir color del evento (selector con búsqueda de 148 colores con nombre)
- Elegir productos del catálogo
- Observaciones (texto libre)
- Límite: 2 eventos por fin de semana, 12 por mes
- Al confirmar, el evento queda guardado y te redirige a WhatsApp

---

## 3. Panel Privado (Lo que ves vos)

### 3.1 Pantalla Principal (después de iniciar sesión)
- Resumen visual con tarjetas:
  - Pedidos pendientes del día
  - Próximos eventos (15 días)
  - Productos con stock bajo o sin stock
  - Eventos del mes (vs límite de 12)
- Lista de próximos eventos
- Alertas de stock bajo
- Accesos directos a las secciones principales

### 3.2 Administrar Productos
- Crear, editar y eliminar productos
- Campos: nombre, descripción, precio, foto, stock, etiqueta, unidades por paquete
- Editar el stock directamente desde la lista
- Eliminar con un botón desde la tarjeta del producto
- Asignar categoría a cada producto

### 3.3 Administrar Categorías
- Crear, editar y eliminar categorías
- Los productos se agrupan por categoría en la tienda

### 3.4 Administrar Eventos
- Lista con filtros (pendiente, confirmado, completado, cancelado)
- Filtro por fecha (hoy, semana, mes)
- Vista de lista que se expande para ver detalles
- Vista de calendario con puntos en los días ocupados
- Editar cantidades de productos por evento (con precios estimados)
- Cambiar estado del evento
- Color del evento visible + nombre del color
- Observaciones visibles sin expandir

### 3.5 Administrar Pedidos
- Lista con filtros (pendiente, confirmado, entregado, cancelado)
- Filtro por fecha (hoy, semana, mes)
- Vista de lista que se expande (productos, total, método de pago, dirección)
- Vista de calendario con puntos en los días con pedidos
- Cambiar estado: pendiente → confirmado (descuenta stock) → entregado → cancelado
- Alertas de confirmación antes de cada cambio

### 3.6 Administrar Galería
- Subir fotos para la página principal
- Vista previa
- Activar/desactivar si se muestra
- Eliminar fotos

### 3.7 Administrar Videos
- Subir videos con título personalizado
- Vista previa
- Activar/desactivar si se muestra
- Eliminar videos

---

## 4. Notificaciones

### 4.1 Notificaciones al Celular (por la web)
- El cliente puede suscribirse al hacer un pedido
- El cliente recibe una notificación cuando su pedido se confirma
- El cliente recibe una notificación cuando su pedido se entrega
- Cada cliente recibe solo sus propias notificaciones

---

## 5. Diseño

- **Diseñado para celulares** principalmente
- **Menú inferior** en el celular para acceso rápido
- **Menú superior** en computadora
- **Colores personalizados**: amarillo, marrón y crema
- Letras modernas y fáciles de leer
- Se ve bien en celulares, tablets y computadoras
- Animaciones suaves
- Alertas visuales para confirmar acciones importantes
- Estadísticas de Google (se activa cuando quieras)

---

## 6. Presupuesto

### Desarrollo
- **Sistema completo puesto en marcha**: $250.000 ARS
- Incluye: todas las funcionalidades detalladas, instalación en el hosting, configuración del dominio y certificado de seguridad (SSL)

### Hosting
- **DonWeb por 48 meses**: $120.000 ARS
- Incluye: hosting, dominio, certificado de seguridad, soporte técnico del proveedor

### Mantenimiento mensual (opcional)
- **$10.000 ARS por mes**
- Incluye: corrección de errores, copias de seguridad, soporte técnico

---

*Documento generado el 29 de Julio de 2026*
