# Especificación de UI para replicar el módulo de productos

## Propósito

Este documento describe el patrón visual y de interacción aplicado en Productos para que se replique de forma coherente en Clientes, Proveedores, Usuarios, Categorías y otros módulos. La interfaz debe seguir usando UIkit y los tokens globales existentes; no se deben recrear estilos o comportamientos aislados por módulo.

## Uso con OpenCode

Este archivo es una especificación de proyecto que puede adjuntarse como contexto al pedir una migración en OpenCode. Desde la raíz del repositorio:

1. Abre una sesión de OpenCode en este proyecto.
2. Incluye `docs/ESPECIFICACION_UI_MODULOS.md` como contexto mediante la mención de archivo de OpenCode (`@docs/ESPECIFICACION_UI_MODULOS.md`) o pega la sección **Prompt detallado para migrar otro módulo**.
3. Reemplaza `[NOMBRE DEL MÓDULO]` por el módulo a migrar y especifica cualquier alcance adicional (por ejemplo, “solo clientes activos/inactivos”).
4. Pide que inspeccione el código existente y ejecute la migración y sus validaciones en la misma tarea; no debe terminar después de proponer un plan.
5. Revisa el resumen final y cualquier prueba de navegador marcada como pendiente antes de dar por concluida la migración.

El documento no depende de herramientas o nombres de agentes particulares. Si se desea que estas reglas se carguen automáticamente en futuras sesiones, se pueden resumir en las instrucciones del proyecto de OpenCode y apuntar a este archivo como fuente canónica; evita mantener copias divergentes de toda la especificación.

## Referencias de implementación

- Vista de referencia: [src/Views/productos.php](src/Views/productos.php)
- Estilos compartidos: [public/assets/css/style.css](public/assets/css/style.css)
- Estructura UIkit: UIkit 3, usando componentes `uk-tab`, `uk-switcher`, `uk-card`, `uk-table`, `uk-modal`, `uk-pagination`, `uk-icon` y `uk-toggle`.

## Principios de diseño

1. **Dark-first, con tema claro compatible.** El tema activo se aplica globalmente; el módulo no debe fijar colores de texto/fondo que anulen el tema.
2. **Jerarquía sencilla.** Título y acciones principales arriba, navegación secundaria debajo, búsqueda y controles en una barra, contenido en cards o tabla.
3. **Acciones reconocibles.** Usar botones con texto para operaciones principales y botones circulares con iconos para acciones por registro.
4. **Responsive por defecto.** Cards se reorganizan con la grilla de UIkit; tablas anchas se alojan en un contenedor con desplazamiento horizontal.
5. **Consistencia funcional.** La búsqueda, el orden, la paginación, las acciones CRUD y los modales operan sobre los mismos datos con cualquiera de las vistas disponibles.
6. **No cambiar la lógica de negocio al migrar la presentación.** Mantener controladores, rutas, nombres de campos y validaciones existentes, salvo que se solicite un cambio funcional explícito.

## Sistema visual

### Tokens compartidos

Usar las variables declaradas en `:root` de la hoja global. Valores vigentes:

| Token | Dark (predeterminado) | Light |
|---|---:|---:|
| `--app-bg` | `#111111` | `#ffffff` |
| `--app-bg-soft` | `#1a1a1a` | `#f3f4f6` |
| `--app-surface` | `#1f1f1f` | `#f7f7f7` |
| `--app-surface-strong` | `#2b2b2b` | `#ececec` |
| `--app-border` | `rgba(170, 170, 170, 0.24)` | `rgba(21, 21, 21, 0.12)` |
| `--app-text` | `#f4f7fb` | `#1b2430` |
| `--app-muted` | `#c4ccd6` | `#4b5563` |
| `--app-primary` | `#007D35` | compartido |
| `--app-success` | `#2ABB00` | compartido |
| `--app-success-2` | `#129740` | compartido |
| `--app-warning` | `#ffff00` | compartido |
| `--app-danger` | `#ff0000` | compartido |
| `--app-purple` | `#9800b3` | compartido |
| `--app-shadow` | `rgba(0, 0, 0, 0.26)` | `rgba(15, 23, 42, 0.08)` |

- Fuente: `-apple-system, "Segoe UI", Roboto, sans-serif`.
- Texto principal: `var(--app-text)`; etiquetas y metadatos secundarios: `var(--app-muted)`.
- Bordes, separadores y sombras deben derivarse de sus tokens.
- Estados de inventario tienen significado estable: verde = activo/normal, ámbar = bajo stock/advertencia, rojo = sin stock/error. No depender solo del color: incluir etiqueta textual.
- Evitar `text-white`, `text-dark` o colores de texto hardcodeados en contenido que cambia con el tema; aplicar tokens o clases UIkit cuya apariencia esté verificada en ambos temas.

### Espaciado y superficies

- Separación entre controles de toolbar: alrededor de `1rem`, permitiendo wrap.
- Contenedor de listado: superficie sutil, borde temático y radio amplio (aprox. `22px`), con padding de `1rem`.
- Card de registro: borde temático, fondo de superficie sutil, radio de `18px` y altura coherente dentro de la grilla.
- Botones de acción circular: alrededor de `32px`, con foco/hover visible y tooltip/`aria-label`.
- Tabla de filas: envolver en `uk-overflow-auto`, borde temático y radio de `14px`; mantener un ancho mínimo razonable para que el contenido no quede ilegible y permitir scroll horizontal en móvil.

## Estructura de pantalla

### 1. Encabezado del módulo

- Título de módulo alineado a la izquierda (`uk-heading-bullet` o patrón equivalente).
- Acciones frecuentes agrupadas a la derecha, por ejemplo “Nuevo”, “Registrar entrada” o “Registrar venta”.
- En pantallas estrechas, permitir que acciones y título hagan wrap sin solaparse.
- La acción primaria debe ser visualmente dominante; las secundarias usan variantes `uk-button-default` o de estado según su efecto.

### 2. Tabs UIkit

Cuando el módulo tenga sub-secciones, usar `uk-tab` y su `uk-switcher` asociado:

- Un `ul.uk-tab[uk-tab]` con enlaces dirigidos a paneles identificables.
- Un `div.uk-switcher` hermano inmediato con paneles en el mismo orden de las tabs.
- Mantener una única tab activa inicial (`uk-active`).
- No mezclar `nav-tabs`, `data-bs-toggle="tab"`, `tab-pane` ni clases de transición Bootstrap con UIkit.
- Cada panel debe conservar búsqueda/acciones/contexto propios y no duplicar IDs.

### 3. Toolbar de colección

Orden visual recomendado:

1. Búsqueda por texto con icono (`uk-search`).
2. Orden ascendente/descendente, con controles accesibles e indicación del sentido.
3. Toggle de visualización **Tarjetas / Filas** con botones UIkit e `aria-pressed`.

Comportamiento esperado:

- Toolbar flex con wrap; búsqueda ocupa hasta unos `320px` y se ajusta al viewport.
- La selección de vista se conserva por usuario/navegador en `localStorage` con una clave propia del módulo, por ejemplo `productosView`.
- Añadir un selector accesible de tamaño de página junto a los controles de paginación (por ejemplo, 6, 12, 24 y 48 elementos por página). Su valor debe controlar cards y filas por igual; puede persistirse en `localStorage` con una clave del módulo, por ejemplo `productosPageSize`.
- El control activo debe distinguirse visualmente y exponer `aria-pressed="true"`.
- Búsqueda, orden, cambio de vista y tamaño de página reinician la paginación a la primera página.
- Orden alfabético sin distinguir mayúsculas/minúsculas y con configuración regional española.

### 4. Vista de tarjetas

- Grilla UIkit de tres columnas en pantallas amplias, dos en intermedias y una en móvil; ajustar breakpoints al contenido del módulo.
- En productos: imagen, nombre, marca, acciones editar/eliminar, estado y metadatos de precio, stock y categoría.
- Colocar las acciones de operación (por ejemplo, entrada/venta) al final y alineadas consistentemente.
- Imagen con tamaño limitado, `object-fit: cover`, borde redondeado y fallback si no existe.
- Altura pareja de cards; el contenido no debe saltar entre filas por diferencias menores en texto.
- No ocultar información esencial que sí aparece en la vista de filas.

### 5. Vista de filas (tabla UIkit)

- Una fila por entidad y encabezados concisos.
- En productos: identidad (miniatura, nombre y marca), categoría, estado, precio, stock y acciones.
- Usar `uk-table uk-table-divider uk-table-hover uk-table-middle` o variantes UIkit apropiadas.
- Las acciones de fila deben conservar las mismas capacidades que la card: editar, eliminar y operaciones relacionadas.
- En móvil, permitir scroll horizontal dentro del wrapper de la tabla en vez de aplastar columnas o romper acciones.

### 6. Vacío y paginación

- Mostrar estado vacío claro cuando no hay registros o no hay coincidencias: icono, mensaje explicativo y, si corresponde, una acción para crear el primer registro.
- La paginación debe usar `uk-pagination`, indicar la página actual, ofrecer navegación a página anterior/siguiente y primera/última cuando sea útil, y deshabilitar los controles fuera de rango. Los enlaces no deben provocar navegación al inicio de página.
- Calcular páginas a partir del total después de aplicar búsqueda y orden: `totalPages = ceil(filteredItems.length / pageSize)`, garantizando como mínimo una página para el cálculo aunque el estado vacío oculte la navegación.
- Renderizar solo el segmento actual de la colección (`slice((currentPage - 1) * pageSize, currentPage * pageSize)`) tanto para cards como para filas; no mantener dos implementaciones de paginación que puedan divergir.
- Si un filtro reduce el total de páginas por debajo de la página activa, ajustar la página a la última página válida; al cambiar búsqueda, orden o tamaño, volver a la primera página.
- El selector de tamaño de página debe estar etiquetado, usar `uk-select`, ofrecer opciones finitas adecuadas al módulo y permanecer sincronizado con la cantidad renderizada.
- En Productos, las opciones son 6, 12, 24 y 48 registros por página (6 por defecto); el valor se persiste en `localStorage` y se aplica igual en cards y filas. Otros módulos pueden ajustar las opciones según la densidad, documentándolas y manteniendo el mismo resultado entre vistas.
- Persistir el tamaño de página solo si resulta apropiado; usar una clave específica por módulo, validar el valor guardado contra las opciones permitidas y usar el valor por defecto si está ausente o no es válido.
- Mostrar el estado vacío cuando la colección filtrada no tiene resultados y ocultar/limpiar controles de navegación obsoletos; no mostrar páginas fantasma ni registros fuera del filtro actual.
- La página actual debe reflejarse con `uk-active`; controles inaccesibles deben usar `uk-disabled` y evitar cambiar la página al activarlos.
- Al cambiar entre cards y filas se conserva el tamaño de página seleccionado y la colección visible debe representar la misma página y los mismos registros.

## Formularios, acciones y modales

- Formularios y modales usan componentes UIkit (`uk-modal`, `uk-modal-dialog`, `uk-modal-body`, `uk-input`, `uk-select`, `uk-textarea`, `uk-button`).
- Mantener etiquetas visibles asociadas a los controles, mensajes de validación y acciones “Cancelar”/“Guardar” claramente separadas.
- Una acción de registro debe abrir su modal UIkit; cancelar/cerrar no debe enviar el formulario.
- Select2 dentro de un modal UIkit debe inicializarse usando el elemento `.uk-modal-dialog` de ese modal como `dropdownParent` (no el wrapper exterior `[uk-modal]`). Así el menú queda dentro del área del diálogo y los clics en sus opciones no se interpretan como clics fuera del modal.
- Elegir una opción Select2 **no debe cerrar el modal**. Solo se cierra con el control de cierre, cancelar, una acción explícita o el comportamiento deliberado de UIkit.
- Confirmaciones destructivas requieren modal/confirmación claramente identificada.
- Evitar IDs duplicados; los IDs generados por registro deben ser únicos y corresponder a los targets de `uk-toggle`.

## Comportamiento de datos y seguridad

- Las dos vistas deben construirse a partir de un mismo conjunto filtrado, ordenado y paginado para evitar resultados diferentes.
- Mantener búsqueda y paginación consistentes al cambiar entre vista de tarjetas y filas.
- Escapar datos de salida HTML en PHP (`htmlspecialchars`) y no insertar valores de usuario sin escape en plantillas JavaScript/HTML.
- Para contenido dinámico en cliente, preferir `textContent`/nodos DOM o una función de escape HTML; validar identificadores antes de construir selectores, targets o atributos.
- Conservar validaciones de backend; las validaciones frontend son asistencia visual, no reemplazo de las del servidor.
- Los iconos deben tener nombre accesible mediante texto visible, `aria-label` o `title`; controles solo de icono requieren foco visible.

## Responsive y accesibilidad

- La toolbar y la cabecera hacen wrap; los controles no deben salirse del ancho del panel.
- Cards: 1 columna en móvil; 2 en tablet si hay ancho suficiente; 3 en escritorio.
- Tabla: scroll horizontal controlado, sin scroll horizontal de toda la página.
- Los targets táctiles deben ser cómodos, con un mínimo aproximado de `32–40px` según tipo de control.
- Usar elementos semánticos (`button`, `table`, `th`, `label`) y atributos ARIA cuando el estado no sea evidente.
- Verificar contraste en tema oscuro y claro, hover/focus, navegación por teclado y mensajes vacíos.
- No comunicar stock/estado solo por color.

## Guía para migrar otro módulo

1. Identificar entidades, estados, búsquedas, acciones CRUD y flujos existentes; no eliminar comportamiento de negocio.
2. Crear título y acciones principales con UIkit.
3. Convertir navegación entre secciones a `uk-tab` + `uk-switcher` solo si existen secciones reales.
4. Reutilizar el patrón de toolbar: búsqueda, orden y toggle de vistas cuando ambas vistas aporten valor.
5. Renderizar la misma colección como cards y como tabla, compartiendo el filtrado/orden/paginación.
6. Añadir estados vacío, carga y error si el módulo tiene llamadas asíncronas.
7. Migrar formularios y diálogos a UIkit, conservar las rutas/nombres y comprobar Select2 en cada modal.
8. Aplicar clases compartidas o tokens globales; evitar CSS duplicado bajo nombres exclusivos del módulo.
9. Probar en tema oscuro y claro, escritorio y móvil, incluidos los casos sin datos y de muchos datos.
10. Validar altas, edición, eliminación, filtros, navegación de tabs, paginación y cada modal con datos reales.

## Criterios de aceptación por módulo

- No contiene tabs ni modales Bootstrap activos.
- Texto y superficies respetan los dos temas y mantienen contraste legible.
- La búsqueda filtra los campos acordados; orden y paginación no se reinician incorrectamente.
- Si hay selector Tarjetas/Filas, ambos muestran la misma colección y las mismas acciones.
- El estado vacío es visible cuando corresponde.
- La tabla no desborda el viewport; las cards se adaptan a móvil.
- Botones, tabs, Select2 y modales se pueden operar sin cierres inesperados.
- CRUD, rutas, permisos y validaciones del backend siguen funcionando.
- No hay errores de PHP/JavaScript y se ha hecho una prueba visual del módulo.

## Límites

Esta especificación define el lenguaje visual y los patrones de interacción, no obliga a convertir todos los listados en cards. Si una entidad requiere comparación de muchas columnas, la tabla puede ser la vista inicial; si los registros son visuales o tienen pocos campos, las cards pueden ser la vista inicial. El toggle se implementa cuando las dos presentaciones sean útiles para esa entidad.

---

## Prompt detallado para migrar otro módulo en OpenCode

Copia el siguiente prompt en OpenCode y reemplaza los campos entre corchetes. Adjunta también este archivo con `@docs/ESPECIFICACION_UI_MODULOS.md` si no está ya incluido como contexto:

```text
Actúa como desarrollador experto en PHP MVC, UIkit 3, JavaScript y CSS dentro del repositorio abierto en OpenCode. Migra la interfaz del módulo [NOMBRE DEL MÓDULO] para que siga `docs/ESPECIFICACION_UI_MODULOS.md` y el patrón ya aplicado al módulo de Productos.

OBJETIVO
Modernizar la UI de [NOMBRE DEL MÓDULO] con el sistema visual UIkit dark-first y soporte de tema claro, manteniendo íntegros los flujos existentes. Debe sentirse parte de la misma aplicación que Productos y funcionar en móvil y escritorio.

ANTES DE EDITAR
1. Inspecciona la vista del módulo, su controlador, modelos relacionados, layout compartido, estilos y JavaScript que use. Identifica el flujo real de lectura/alta/edición/eliminación, los permisos, los nombres de campos, las rutas, los mensajes y las dependencias de las tablas/modales.
2. No des por hecho que los datos o variables tienen una forma determinada: confirma sus nombres y estructura en el controlador/modelo/vista.
3. Identifica tabs, tablas, filtros, Select2, modales, DataTables, eventos y scripts inline. Busca referencias Bootstrap que puedan afectar a los componentes que migres.
4. Antes de modificar, comparte un plan breve de archivos y cambios. Después continúa con la implementación en esta misma tarea; no te detengas esperando confirmación, salvo que exista una ambigüedad que impida proceder con seguridad. No cambies lógica de negocio si basta con ajustar la presentación.

REQUISITOS VISUALES Y DE INTERACCIÓN
1. Usa UIkit (uk-container, uk-card, uk-button, uk-input, uk-select, uk-table, uk-modal, uk-tab/uk-switcher, uk-pagination y uk-icon cuando corresponda) y los tokens globales de `public/assets/css/style.css`. No añadas Bootstrap ni colores/textos fijos que rompan el modo oscuro o claro.
2. Crea una cabecera de módulo con título claro y acciones primarias/secundarias agrupadas; debe adaptarse con wrap en pantallas estrechas.
3. Migra tabs reales a uk-tab + uk-switcher, con paneles asociados en el mismo orden. No mezcles nav-tabs, data-bs-toggle="tab", tab-pane ni clases de activación Bootstrap en esos tabs.
4. Para listados, crea toolbar responsive con búsqueda pertinente, orden ascendente/descendente y selector Tarjetas/Filas únicamente si ambas presentaciones tienen utilidad para esta entidad. Las dos vistas deben representar el mismo conjunto filtrado, ordenado y paginado, con las mismas acciones disponibles.
5. Cards: usa grilla UIkit responsive (una columna en móvil; dos o tres en pantallas amplias según el contenido), superficie y bordes temáticos, jerarquía de texto clara, estados identificados por etiqueta y botones de acción accesibles.
6. Filas: usa tabla UIkit (`uk-table` y variantes adecuadas) dentro de `uk-overflow-auto`; evita aplastar columnas o provocar scroll horizontal de toda la página. En móvil el scroll horizontal debe quedar contenido en el wrapper.
7. Añade un estado vacío informativo para cero registros y cero coincidencias. Incluye acción de crear cuando corresponda.
8. Implementa paginación real y consistente en ambas vistas. Añade selector etiquetado de tamaño con opciones razonables para el módulo; valida el tamaño guardado, persístelo en localStorage con clave propia del módulo si es conveniente, renderiza solo el segmento solicitado y calcula las páginas desde el total filtrado. Indica página activa, bloquea anterior/siguiente/primera/última cuando corresponda y evita navegación accidental al inicio. Cambios de búsqueda, orden o tamaño vuelven a la primera página; si se filtra y la página actual deja de existir, ajústala a una página válida. El cambio de vista conserva tamaño y página.
9. Migra los modales a UIkit sin cambiar rutas ni nombres de campo. Mantén validaciones y botones de cancelar/guardar claros. IDs y targets deben ser únicos.
10. Si hay Select2 dentro de un modal UIkit, inicialízalo con `dropdownParent` apuntando a `$("#ID_MODAL .uk-modal-dialog")`, nunca al wrapper exterior del modal. No añadas handlers que oculten el modal al seleccionar una opción: escoger un item debe conservar el modal abierto y actualizar únicamente el valor del select.
11. Mantén DataTables solo donde siga aportando valor y comprueba su inicialización cuando el contenido vive en tabs ocultos. No inicialices dos veces la misma tabla ni dupliques IDs.
12. Conserva rutas, permisos, contratos de formularios, comportamiento CRUD, validación de backend y relaciones de datos. Escapa los datos en salida PHP y protege cualquier HTML generado dinámicamente.
13. Usa nombres y estilos compartidos siempre que sea razonable; no dupliques bloques globales ni alteres estilo de módulos ajenos al alcance.
14. Asegura accesibilidad: labels vinculadas, botones semánticos, aria-label/title para iconos, aria-pressed para el toggle, foco visible, navegación por teclado y estados que no dependan únicamente del color.

VALIDACIÓN OBLIGATORIA
1. Ejecuta comprobación de sintaxis PHP en cada vista PHP modificada y revisión/chequeo de JavaScript cuando corresponda.
2. Revisa los diagnósticos disponibles en OpenCode/editor en todos los archivos cambiados y corrige los que se deban a esta migración.
3. Prueba o inspecciona: tema oscuro y claro; escritorio y móvil; vacío y muchos registros; búsqueda; ambos sentidos de orden; cada tamaño de página; anterior/siguiente/primera/última; cambio entre tarjetas y filas; todos los botones; alta, edición, eliminación y validación; tabs; modales; selección Select2 sin cierre del modal; DataTables y mensajes de éxito/error.
4. No afirmes que un flujo visual funciona solo porque el archivo compila. Distingue las comprobaciones de sintaxis de las pruebas de navegador que sí se hayan realizado y reporta cualquier prueba manual pendiente.

CRITERIOS DE ACEPTACIÓN
- El módulo coincide visualmente con el diseño especificado y respeta ambos temas.
- No hay tabs ni modales Bootstrap activos en las partes migradas.
- CRUD, rutas, permisos, datos, validaciones y acciones originales siguen funcionando.
- La búsqueda, orden y paginación son coherentes entre las vistas disponibles y no generan páginas inválidas.
- Select2 dentro de UIkit permanece dentro de `.uk-modal-dialog` y seleccionar una opción no cierra el modal.
- El layout responde en móvil sin overflow de página; el estado vacío es correcto.
- PHP/JavaScript no presentan errores nuevos y se informa exactamente qué validación se ejecutó.

Al terminar, resume archivos modificados, decisiones de UI, validaciones ejecutadas y pruebas de navegador/manuales pendientes. No reescribas controladores o modelos si la migración puede limitarse a las vistas, CSS y JS del módulo.
```
