# Plan de implementación: migración visual a UIkit 3 + dark-first

## 1. Objetivo
Migrar la interfaz de la aplicación de inventario/repuestos al sistema de diseño especificado, manteniendo la lógica de negocio actual y mejorando la experiencia visual en un enfoque dark-first con UIkit 3.16.22, jQuery y DataTables.

La migración debe hacerse sin romper CRUDs, autenticación, filtros, modales, notificaciones y operativa de inventario. El enfoque será incremental, compatible y verificable.

---

## 2. Alcance del rediseño

### 2.1 Framework base
- UIkit 3.16.22 como base de componentes visuales.
- Uso de `uikit.css`, `uikit.js` y `uikit-icons.js`.
- jQuery como dependencia complementaria para DataTables y scripts actuales.
- DataTables con responsive habilitado.
- Estilos propios cargados después de `uikit.css`.

### 2.2 Sistema de color
- Dark-first base: `html`, `body` y contenedor principal en `#111`.
- Superficies principales con `uk-background-secondary` y texto `uk-light`.
- Modo claro persistido con `localStorage` usando la clave `btnColor`.
- En modo claro:
  - Fondo principal: `#fff`
  - `uk-background-secondary` se reemplaza por `uk-background-muted`
  - `uk-light` se reemplaza por `uk-dark`
  - Si existen variantes de logo o imágenes, usar sufijo `NewColor`

### 2.3 Paleta esperada
- Primario verde: `#007D35`
- Verde secundario: `rgb(0,150,64)`, `#129740`, `#106733`
- OK: `#2ABB00` / `#7CCE00`
- Éxito: `#15ff00`
- Peligro: `#ff0000`
- Advertencia: `#ff0`
- Acento púrpura: `#9800b3`
- Neutros oscuros: `#333`, borde `rgba(170,170,170,0.24)`, texto `#999`

### 2.4 Tipografía y UX
- Stack: `-apple-system, Segoe UI, Roboto, sans-serif`
- Navegación con enlaces en `Segoe UI`, bold, 17px, uppercase
- Botones y estados con clases nuevas: `.succesc` (verde) y `.danger` (rojo)

---

## 3. Estructura de trabajo por fases

### Fase 1: Base técnica y preparación del frontend
Objetivo: dejar la app lista para soportar el nuevo diseño sin romper la carga actual.

Tareas:
1. Revisar la carga de assets en [src/Views/complements/head.php](src/Views/complements/head.php) y [src/Views/complements/footer.php](src/Views/complements/footer.php).
2. Incluir la versión UIkit 3.16.22 localmente.
3. Confirmar que jQuery y DataTables siguen cargándose después del framework base.
4. Mantener los estilos personalizados cargados después de `uikit.css`.
5. Asegurar compatibilidad con los archivos JavaScript actuales.

Entregables:
- Build visual base con UIkit activado
- Sin errores de carga en páginas con layout común

Validación:
- Verificar que la app carga sin JS errors críticos
- Comprobar que DataTables sigue funcionando

---

### Fase 2: Sistema visual dark-first y tema claro/oscuro
Objetivo: preparar tokens y lógica del tema visual.

Tareas:
1. Definir variables CSS para:
   - fondo base
   - superficies
   - botones
   - bordes
   - texto
   - estado OK/alerta/peligro
2. Crear selector o botón de cambio de tema con persistencia en `localStorage` usando la clave `btnColor`.
3. Implementar los cambios de clase visual en `body` o `html` para alternar entre modo oscuro y claro.
4. Configurar swapped classes:
   - `uk-background-secondary` ↔ `uk-background-muted`
   - `uk-light` ↔ `uk-dark`
5. Configurar manejo de imágenes alternas con sufijo `NewColor` si aplica.

Entregables:
- Tema dark-first funcionando
- Persistencia del tema por navegador
- Sin regresiones en navegación y formularios

Validación:
- Recargar la app y comprobar que el tema se conserva
- Verificar que la paleta y contrastes cumplen con la guía visual

---

### Fase 3: Layout global, header y comunicación visual
Objetivo: recrear la estructura del shell principal según la nueva identidad visual.

Tareas:
1. Rediseñar el header desktop:
   - banner de fondo con cover y posición right
   - logo a la izquierda (~260px)
   - campana de notificaciones con `uk-dropnav`, `uk-badge`, `uk-dropdown`
   - menú de usuario con avatar circular
2. Aplicar barra informativa tipo marquee con:
   - estado de caja (ABIERTA/CERRADA)
   - tasa BCV
   - iconos y texto blanco bold
3. Rediseñar navbar de módulos con:
   - `uk-background-secondary uk-light`
   - elementos tipo `uk-button uk-button-text`
   - icono + etiqueta
   - item activo según `window.location.pathname`
4. Estilizar estados `.succesc` y `.danger` para botones de acción.

Entregables:
- Shell completo del sistema con layout dark-first
- Navegación consistente en todas las páginas

Validación:
- Navegar por cada módulo para confirmar que el item activo se destaca
- Verificar dropdowns y badges

---

### Fase 4: Migración de componentes principales
Objetivo: adaptar tablas, formularios, tabs, cards y modales al lenguaje visual UIkit.

Tareas:
1. Actualizar tabla base a:
   - `uk-table uk-table-hover uk-table-divider`
   - `thead` con `uk-background-secondary`
   - `uk-pagination` en paginación
2. Migrar filtros y navegación a `uk-filter`, `uk-subnav` y `uk-dropnav`.
3. Rediseñar cards con `uk-border-rounded`.
4. Botones de acción con iconos circulares de 35–40px, fondo verde y borde redondeado.
5. Reemplazar modales Bootstrap por `uk-modal` + `uk-toggle` + `uk-modal-close-outside`.
6. Reemplazar tratamientos de tabs por `uk-tab`/`uk-switcher` según el caso y mantener comportamiento of contenido anidado.

Entregables:
- CRUDs y pantallas principales con look consistente
- Modales funcionales y compatibles con la lógica actual

Validación:
- Probar alta/edición/eliminación en clientes, proveedores, usuarios, roles, categorías y productos
- Verificar que los formularios mantienen validación HTML y envío de datos

---

### Fase 5: Ajustes de formularios, estados y acciones de negocio
Objetivo: mantener funcionalidad sin perder el nuevo estilo.

Tareas:
1. Ajustar inputs, selects, radios y checkboxes para el nuevo estilo dark-first.
2. Mantener validación de campos por formulario.
3. Rediseñar toasts y alertas para contexto visual de éxito, advertencia y error.
4. Estilar badges, estados de stock y etiquetas de pago/venta.
5. Revisión de iconografía con `uk-icon` / `uikit-icons` (bell, cog, sign-out, thumbnails, users, file-pdf, etc.)

Entregables:
- Formularios con diseño consistente y funcional
- Alertas/estado de validación alineados a la UI

Validación:
- Probar las acciones de registro, edición, baja y confirmaciones
- Verificar que los estados de stock y notificaciones sigan siendo legibles

---

### Fase 6: Preloader y overlays
Objetivo: agregar la capa visual de carga y mejorar la percepción de fluidez.

Tareas:
1. Crear overlay preloader con SVG animado.
2. Ocultar el preloader en `window.load`.
3. Integrarlo con layout general y pantallas principales.
4. Confirmar que no interfiera con navegación ni modales.

Entregables:
- Preloader funcional
- Carga inicial más profesional y consistente

Validación:
- Verificar que aparece al cargar la app y desaparece correctamente

---

### Fase 7: Responsive y breakpoints
Objetivo: garantizar respuesta correcta en mobile y tablets.

Breakpoints planificados:
- 350–425
- 375–565
- 768–939
- 1024–1290
- 1300–1404

Tareas:
1. En 1023px o menos:
   - ocultar header desktop
   - mostrar header móvil
   - banner reducido (~110px)
   - navbar centrada con iconos
   - menú de usuario en `uk-offcanvas`
2. Ajustar logotipos y dimensiones según ancho de pantalla.
3. Reducir largos de tarjetas y wraps en formularios de factura.
4. Ajustar alturas de tablas para pantallas pequeñas.
5. Revisar espaciado de cards y elementos de acción.

Entregables:
- UI responsive con comportamiento correcto en mobile/desktop

Validación:
- Inspeccionar con navegador en varios tamaños
- Probar que navbars y tablas siguen legibles

---

### Fase 8: QA final, rendimiento y limpieza
Objetivo: dejar la UI estable y lista para producción.

Tareas:
1. QA de flujo principal: login, dashboard, productos, ventas, entradas, clientes, proveedores, roles, usuarios.
2. Verificar modales, toasts, tabs y dropdowns.
3. Revisión de scripts de DataTables y de validación de precios/stock.
4. Limpieza de estilos Bootstrap residuales y referencias obsoletas.
5. Verificar que la implementación no rompe acciones que dependen de `window.location.pathname` ni de formularios con AJAX.

Entregables:
- UI migrada y estable
- Código limpio y compatible

Validación:
- Validación PHP y JS
- Pruebas manuales de los módulos críticos
- Confirmación final visual y funcional

---

## 4. Orden recomendado de ejecución
1. Base técnica
2. Tema visual y dark/light
3. Header + navbar
4. Layout general
5. Modales y formularios
6. Tablas, filtros y cards
7. Responsive
8. Preloader
9. QA final y limpieza

---

## 5. Riesgos esperados y mitigación

### Riesgo 1: incompatibilidad de modales Bootstrap/UIkit
Mitigación:
- migrar todos los `data-bs-*` y eventos `show.bs.modal` a la API de UIkit
- dejar compatibilidad temporal solo durante transición

### Riesgo 2: rotura de DataTables o Select2
Mitigación:
- probar al cargar las tablas y los modales de creación/edición
- utilizar `dropdownParent` correcto en selects dentro de modales

### Riesgo 3: tema claro/oscuro con imágenes o logos
Mitigación:
- versionar assets con sufijo `NewColor` y condicionarlos por clase del body

### Riesgo 4: navegación activa por ruta
Mitigación:
- calcular el item activo con `window.location.pathname`
- mantener navegación consistente al recargar la página

### Riesgo 5: responsive en tamaños intermedios
Mitigación:
- definir breakpoints de 350–425, 375–565, 768–939, 1024–1290 y 1300–1404
- revisar mobile first y desktop refinado

---

## 6. Criterios de aceptación
- La aplicación carga sin errores de JS importantes.
- El sistema se ve en dark-first y se alterna correctamente a claro.
- El header, navbar y banner cumplen con la nueva identidad visual.
- Modales, tabs, tablas y filtros funcionan correctamente.
- El responsive se comporta bien en desktop y móvil.
- No se rompe la lógica de negocio ni los flujos de ventas/entradas/productos.

---

## 7. Recomendación de implementación
Se recomienda ejecutar este plan por fases, con validación posterior a cada fase, para evitar los fallos que normalmente aparecen al remover Bootstrap de golpe. La estrategia ideal es:

- mantener compatibilidad técnica durante la migración
- convertir la capa visual incrementalmente
- hacer validación funcional luego de cada bloque estructural
- dejar limpieza final solo cuando la UI esté completamente migrada

Este enfoque minimiza riesgo, acelera la entrega y mantiene la estabilidad del sistema.
