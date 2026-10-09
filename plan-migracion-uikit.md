# Plan de implementación: migración a UIkit responsive

## Decisiones tomadas

- Estrategia: migración gradual por fases.
- Prioridad: comenzar por el layout base y la vista de login.
- Assets: UIkit instalado localmente en el proyecto.
- Objetivo visual: rediseño premium, responsivo y consistente.
- Plugins: DataTables y Select2 se resolverán en una segunda fase para no bloquear la migración del diseño general.

## Objetivo

Migrar la interfaz actual de Bootstrap a UIkit 3 manteniendo la lógica de negocio y la funcionalidad del sistema, pero mejorando la experiencia visual, la responsividad y la consistencia de la UI.

La prioridad es trasladar la capa visual sin romper flujos de ventas, inventario, CRUDs y autenticación. El enfoque será incremental, validando cada etapa antes de avanzar.

## Alcance

- Layout común: header, navegación, estructura general, sidebar/mobile.
- Inicio de sesión.
- Dashboard principal.
- CRUDs de clientes, proveedores, categorías, usuarios, roles.
- Vista de productos, que requiere tratamiento especial por su complejidad.
- Adaptación de scripts y plugins que dependan de Bootstrap.

## Fase 1: diagnóstico y línea base

### Objetivo
Documentar el estado actual y definir la referencia visual antes del cambio.

### Tareas
- Revisar las vistas principales:
  - [src/Views/home.php](src/Views/home.php)
  - [src/Views/login.php](src/Views/login.php)
  - [src/Views/productos.php](src/Views/productos.php)
  - [src/Views/clientes.php](src/Views/clientes.php)
  - [src/Views/proveedores.php](src/Views/proveedores.php)
  - [src/Views/categorias.php](src/Views/categorias.php)
  - [src/Views/usuarios.php](src/Views/usuarios.php)
  - [src/Views/roles.php](src/Views/roles.php)
- Revisar los includes compartidos:
  - [src/Views/complements/head.php](src/Views/complements/head.php)
  - [src/Views/complements/header.php](src/Views/complements/header.php)
  - [src/Views/complements/footer.php](src/Views/complements/footer.php)
- Identificar clases Bootstrap, atributos `data-bs-*`, tabs, modales, toast, formularios, cards y tablas.
- Revisar el CSS propio en [public/assets/css/style.css](public/assets/css/style.css) para separarlo de dependencias visuales de Bootstrap.
- Revisar JS global en [public/assets/js/functionsGenerals.js](public/assets/js/functionsGenerals.js) para detectar eventos y llamadas a Bootstrap.

### Entregable
- Inventario de componentes visuales y JS dependientes de Bootstrap.
- Estándar base de diseño para la migración.

## Fase 2: preparación de UIkit

### Objetivo
Incorporar UIkit de forma controlada y dejar una base sólida para las vistas.

### Tareas
- Descargar UIkit 3 localmente dentro del proyecto.
- Incluir CSS y JS en el layout compartido.
- Definir variables de estilo globales:
  - paleta de colores
  - tipografía
  - espaciado
  - botones
  - inputs
  - cards
  - tablas
  - alertas
- Crear un sistema visual coherente para todo el sistema.
- Mantener Bootstrap desactivado en las páginas migradas para evitar conflictos.

### Recomendación
No mezclar Bootstrap y UIkit en una misma pantalla de forma permanente. La migración debe ser una transición controlada por vista y por bloque funcional.

## Fase 3: migración del layout compartido

### Objetivo
Reemplazar el sistema base visual del panel y dejar una estructura moderna y responsive.

### Tareas
- Rediseñar el header.
- Reemplazar la navegación por un sistema compatible con UIkit.
- Ajustar el sidebar o menú de navegación para móvil y escritorio.
- Mantener la funcionalidad actual de tasa BCV y cierre de sesión.
- Revisar espacios, márgenes, estados y alineaciones para una interfaz más uniforme.

### Puntos críticos
- Mantener la lógica PHP intacta.
- Cambiar solo la capa de presentación.
- Validar comportamiento mobile/tablet/desktop.

## Fase 4: migración del login

### Objetivo
Dejar la autenticación con una pantalla moderna y enfocada en conversión.

### Tareas
- Rediseñar [src/Views/login.php](src/Views/login.php) con una composición basada en UIkit.
- Reemplazar card, formulario y mensajes dentro del nuevo esquema visual.
- Mantener el flujo actual de login y validaciones existentes.
- Asegurar buena visualización en pantallas pequeñas.

## Fase 5: migración de pantallas CRUD

### Objetivo
Actualizar las pantallas CRUD sin cambiar la lógica de negocio ni los contratos de datos.

### Tareas
- Aplicar el nuevo sistema visual a:
  - clientes
  - proveedores
  - categorías
  - usuarios
  - roles
- Reemplazar clases de layout y formularios por estructuras UIkit.
- Ajustar modales, botones, filtros, tablas y mensajes.
- Validar que los formularios y validaciones sigan funcionando igual.

## Fase 6: migración de la vista de productos

### Objetivo
Resolver la vista más compleja y sensible con una reescritura controlada.

### Tareas
- Revisar todos los componentes de [src/Views/productos.php](src/Views/productos.php): pestañas, paneles, tablas, formularios, filtros y modales.
- Rediseñar la vista para que siga siendo usable en móvil.
- Reemplazar la lógica de Bootstrap para tabs, modales y avisos por equivalentes de UIkit.
- Revisión de validaciones y flujo de stock, ventas y entradas.
- Validar interacción con AJAX y DataTables.

### Riego principal
La vista de productos tiene más dependencias de JS y componentes de UI. Debe migrarse después de la base y del layout, y se recomienda hacerlo por bloques funcionales.

## Fase 7: adaptación de plugins y JS

### Objetivo
Quitar dependencias de Bootstrap de plugins, sin romper la funcionalidad.

### Tareas
- Resolver DataTables y responsive table con diseño compatible con UIkit.
- Revisar Select2 y sus temas para que encajen con la nueva visual.
- Reemplazar llamadas a `bootstrap.Modal`, `bootstrap.Tab`, `bootstrap.Toast` y otros APIs.
- Reescribir eventos ligados a `data-bs-*` y `show.bs.modal`.
- Controlar el comportamiento de los scripts en páginas migradas y no migradas.

### Recomendación
Hacer esta fase después de la migración base para evitar trabajos repetidos de ajustes visuales con Bootstrap presente.

## Fase 8: validación y QA

### Objetivo
Confirmar que la aplicación funciona sin regresiones y con mejor experiencia mobile.

### Validaciones
- Login y permisos.
- CRUDs básicos.
- Inventario y ventas.
- Productos con selección, edición y visualización.
- Modales, mensajes y casos de error.
- Responsive en móvil, tablet y escritorio.
- Compatibilidad de navegador principal.

### Criterios de éxito
- Sin errores de JS por dependencias de Bootstrap.
- Navegación intuitiva y consistente.
- Diseño uniforme visualmente.
- Funcionalidad preservada.

## Orden recomendado de ejecución

1. Diagnóstico y línea base.
2. Instalación local de UIkit.
3. Rediseño del layout compartido.
4. Login.
5. CRUDs generales.
6. Dashboard.
7. Productos.
8. DataTables y Select2.
9. Validación final y ajustes.

## Riesgos esperados

- Acoplamiento visual fuerte a Bootstrap.
- Dependencias de JS en `functionsGenerals.js`.
- Filtros y tablas que necesitan reconfiguración.
- Conflictos si se cargan Bootstrap y UIkit al mismo tiempo.

## Resultado esperado

Una aplicación con una interfaz más moderna, clara y responsiva, manteniendo toda la lógica de negocio y mejorando la operatividad diaria para usuarios en escritorio y móvil.

## Fase 1 ejecutada: diagnóstico de la UI actual

### Evidencia revisada
- [src/Views/complements/head.php](src/Views/complements/head.php): carga Bootstrap, DataTables, Responsive y Select2.
- [src/Views/complements/footer.php](src/Views/complements/footer.php): carga jQuery, DataTables, Bootstrap bundle, Select2 y JS global.
- [src/Views/complements/header.php](src/Views/complements/header.php): usa `navbar`, modal Bootstrap y atributos `data-bs-*` para abrir la tasa BCV.
- [src/Views/login.php](src/Views/login.php): importa Bootstrap desde CDN y usa card, form, toast y botones con clases Bootstrap.
- [src/Views/home.php](src/Views/home.php): dashboard con cards, badges, alertas y tabla responsiva usando clases Bootstrap.
- [public/assets/css/style.css](public/assets/css/style.css): contiene estilos visuales escritos sobre clases de Bootstrap y media queries orientadas a componentes concretos como `.nav-tabs`, `.modal-dialog`, `.table-responsive` y `.accordion-button`.
- [public/assets/js/functionsGenerals.js](public/assets/js/functionsGenerals.js): inicializa DataTables, formatea precios, y usa eventos como `show.bs.modal` y `$('#modalTasa').modal('hide')`.
- [src/Views/productos.php](src/Views/productos.php): contiene la mayor concentración de dependencias: tabs, toasts, modales, acordeón, Select2 y DataTables.

### Hallazgos clave
1. Hay una dependencia global de Bootstrap en el layout compartido. Esto afecta todas las pantallas del sistema.
2. El login no comparte el layout común y carga Bootstrap desde CDN por separado, lo que requiere un tratamiento distinto.
3. El dashboard y los CRUDs están construidos sobre componentes Bootstrap, no solo sobre estilos personalizados.
4. La vista de productos es el punto más crítico por su combinación de tabs, modales, DataTables, Select2 y validaciones JS.
5. El estilo propio en [public/assets/css/style.css](public/assets/css/style.css) está acoplado a clases Bootstrap, por lo que una migración total implicará refactor de estilos y no solo cambio del framework.
6. Los plugins de tablas y selectores están configurados para trabajar con el tema Bootstrap y deberían migrarse en una fase posterior para evitar romper funcionalidad mientras se rediseña la UI.

### Riesgo identificado
La migración no es un cambio de CSS únicamente: requiere actualizar la capa visual, la API JS de Bootstrap y la integración con DataTables/Select2.

### Resultado de la fase 1
Se dejó documentado el inventario real de la UI y los puntos de impacto para comenzar la siguiente etapa: integración de UIkit y rediseño del layout base.
