<!DOCTYPE html>
<html lang="es">
<?php
    $usuarios = $usuarios ?? [];
    $usuariosInactive = $usuariosInactive ?? [];
    $roles = $roles ?? [];
    $escapar = static fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    $usuariosJson = static fn(array $lista): string => json_encode(
        array_map(static fn($usuario) => [
            'id' => (int) $usuario['id'],
            'id_rol' => (int) $usuario['id_rol'],
            'nombre' => (string) $usuario['nombre'],
            'rol' => (string) $usuario['rol'],
            'usuario' => (string) $usuario['usuario'],
        ], $lista),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    );
    $usuariosActivosJson = $usuariosJson($usuarios);
    $usuariosInactivosJson = $usuariosJson($usuariosInactive);
    require_once __DIR__ . '/complements/head.php';
    require_once __DIR__ . '/complements/header.php';
?>

<main class="uk-container uk-container-large app-dashboard-shell">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
        <div>
            <h1 class="app-section-title uk-margin-remove">Gestión de usuarios</h1>
            <p class="uk-text-muted uk-margin-small-top">Usuarios activos e inactivos del sistema.</p>
        </div>
        <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">
            <span uk-icon="icon: plus" aria-hidden="true"></span> Nuevo usuario
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="uk-alert-success" uk-alert><p class="uk-margin-remove">Operación realizada con éxito.</p></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="uk-alert-danger" uk-alert><p class="uk-margin-remove">No se pudo completar la operación.</p></div>
    <?php endif; ?>

    <section class="uk-card uk-card-default uk-card-body">
        <div class="app-item-list-shell">
            <div class="app-item-toolbar">
                <div class="app-item-search uk-search uk-search-default">
                    <span uk-search-icon></span>
                    <input id="usuariosSearch" class="uk-search-input" type="search" placeholder="Buscar usuario..." aria-label="Buscar usuario">
                </div>

                <div class="app-item-sort">
                    <span>Orden</span>
                    <button type="button" class="uk-icon-button" data-usuarios-sort="asc" aria-label="Orden ascendente">
                        <span uk-icon="icon: arrow-down; ratio: 0.9"></span>
                    </button>
                    <button type="button" class="uk-icon-button" data-usuarios-sort="desc" aria-label="Orden descendente">
                        <span uk-icon="icon: arrow-up; ratio: 0.9"></span>
                    </button>
                </div>

                <div class="uk-button-group app-view-toggle" role="group" aria-label="Vista de usuarios">
                    <button type="button" class="uk-button uk-button-small" data-usuarios-view="cards" aria-pressed="true">
                        <span uk-icon="icon: grid" aria-hidden="true"></span><span>Tarjetas</span>
                    </button>
                    <button type="button" class="uk-button uk-button-small" data-usuarios-view="rows" aria-pressed="false">
                        <span uk-icon="icon: table" aria-hidden="true"></span><span>Filas</span>
                    </button>
                </div>

                <label class="app-page-size-control" for="usuariosPageSize">
                    <span>Elementos</span>
                    <select id="usuariosPageSize" class="uk-select uk-form-small">
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </label>
            </div>

            <ul class="uk-tab uk-margin-bottom" uk-tab>
                <li class="uk-active"><a href="#">Activos <span class="uk-badge"><?= count($usuarios) ?></span></a></li>
                <li><a href="#">Inactivos <span class="uk-badge"><?= count($usuariosInactive) ?></span></a></li>
            </ul>

            <ul class="uk-switcher">
                <li>
                    <div id="usuariosActivosList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
                    <div id="usuariosActivosEmpty" class="app-empty-state uk-hidden">
                        <span uk-icon="icon: users; ratio: 1.5"></span>
                        <div>No hay usuarios activos para mostrar.</div>
                    </div>
                    <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                        <div></div>
                        <ul id="usuariosActivosPagination" class="uk-pagination" uk-margin></ul>
                    </div>
                </li>
                <li>
                    <div id="usuariosInactivosList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
                    <div id="usuariosInactivosEmpty" class="app-empty-state uk-hidden">
                        <span uk-icon="icon: users; ratio: 1.5"></span>
                        <div>No hay usuarios inactivos para mostrar.</div>
                    </div>
                    <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                        <div></div>
                        <ul id="usuariosInactivosPagination" class="uk-pagination" uk-margin></ul>
                    </div>
                </li>
            </ul>
        </div>
    </section>
</main>

<?php foreach ($usuarios as $u): ?>
    <?php $idUsuario = (int) $u['id']; ?>
    <div id="modalEditarActivo<?= $idUsuario ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar usuario</h2>
            <form action="?c=CUsuarios/edit" method="POST">
                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                <div class="uk-margin">
                    <label class="uk-form-label" for="nombreActivo<?= $idUsuario ?>">Nombre</label>
                    <input id="nombreActivo<?= $idUsuario ?>" type="text" name="nombre" class="uk-input" value="<?= $escapar($u['nombre']) ?>" required>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="rolActivo<?= $idUsuario ?>">Rol</label>
                    <select id="rolActivo<?= $idUsuario ?>" name="rol" class="uk-select" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['id'] ?>" <?= (int) $r['id'] === (int) $u['id_rol'] ? 'selected' : '' ?>><?= $escapar($r['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="usuarioActivo<?= $idUsuario ?>">Usuario</label>
                    <input id="usuarioActivo<?= $idUsuario ?>" type="text" name="usuario" class="uk-input" value="<?= $escapar($u['usuario']) ?>" required>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="claveActiva<?= $idUsuario ?>">Nueva contraseña <span class="uk-text-muted">(opcional)</span></label>
                    <input id="claveActiva<?= $idUsuario ?>" type="password" name="clave" class="uk-input" autocomplete="new-password">
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modalConfirmarEliminar<?= $idUsuario ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Inhabilitar usuario</h2>
            <form action="?c=CUsuarios/delete" method="POST">
                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                <p>¿Deseas inhabilitar a <?= $escapar($u['nombre']) ?>?</p>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-danger">Inhabilitar</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php foreach ($usuariosInactive as $u): ?>
    <?php $idUsuario = (int) $u['id']; ?>
    <div id="modalEditarInactivo<?= $idUsuario ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar usuario inactivo</h2>
            <form action="?c=CUsuarios/edit" method="POST">
                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                <div class="uk-margin">
                    <label class="uk-form-label" for="nombreInactivo<?= $idUsuario ?>">Nombre</label>
                    <input id="nombreInactivo<?= $idUsuario ?>" type="text" name="nombre" class="uk-input" value="<?= $escapar($u['nombre']) ?>" required>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="rolInactivo<?= $idUsuario ?>">Rol</label>
                    <select id="rolInactivo<?= $idUsuario ?>" name="rol" class="uk-select" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['id'] ?>" <?= (int) $r['id'] === (int) $u['id_rol'] ? 'selected' : '' ?>><?= $escapar($r['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="usuarioInactivo<?= $idUsuario ?>">Usuario</label>
                    <input id="usuarioInactivo<?= $idUsuario ?>" type="text" name="usuario" class="uk-input" value="<?= $escapar($u['usuario']) ?>" required>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="claveInactiva<?= $idUsuario ?>">Nueva contraseña <span class="uk-text-muted">(opcional)</span></label>
                    <input id="claveInactiva<?= $idUsuario ?>" type="password" name="clave" class="uk-input" autocomplete="new-password">
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modalConfirmarActivar<?= $idUsuario ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Activar usuario</h2>
            <form action="?c=CUsuarios/active" method="POST">
                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                <p>¿Deseas activar a <?= $escapar($u['nombre']) ?>?</p>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-success">Activar</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<div id="modalAgregar" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Nuevo usuario</h2>
        <form action="?c=CUsuarios/add" method="POST" class="uk-form-stacked">
            <div class="uk-margin">
                <label class="uk-form-label" for="nuevoNombreUsuario">Nombre</label>
                <input id="nuevoNombreUsuario" type="text" name="nombre" class="uk-input" required>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="nuevoRolUsuario">Rol</label>
                <select id="nuevoRolUsuario" name="rol" class="uk-select" required>
                    <option value="">Seleccionar rol</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['id'] ?>"><?= $escapar($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="nuevoUsuario">Usuario</label>
                <input id="nuevoUsuario" type="text" name="usuario" class="uk-input" autocomplete="username" required>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="nuevaClave">Contraseña</label>
                <input id="nuevaClave" type="password" name="clave" class="uk-input" autocomplete="new-password" required>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="confirmarClave">Confirmar contraseña</label>
                <input id="confirmarClave" type="password" name="confirmar_clave" class="uk-input" autocomplete="new-password" required>
            </div>
            <div class="uk-text-right">
                <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                <button type="submit" class="uk-button uk-button-primary">Crear usuario</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/complements/footer.php'; ?>
<script>
    const usuariosActivosData = <?= $usuariosActivosJson ?>;
    const usuariosInactivosData = <?= $usuariosInactivosJson ?>;
    let usuariosView = localStorage.getItem('usuariosView') === 'rows' ? 'rows' : 'cards';
    const usuariosPageSizeOptions = [6, 12, 24, 48];
    const usuariosSavedPageSize = Number(localStorage.getItem('usuariosPageSize'));
    let usuariosPageSize = usuariosPageSizeOptions.includes(usuariosSavedPageSize) ? usuariosSavedPageSize : 6;
    let usuariosSortDirection = 1;

    function escapeUsuarioHtml(value) {
        const element = document.createElement('span');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function usuarioBadge(active) {
        return active
            ? '<span class="uk-badge" style="background:#16803c;color:#fff;">Activo</span>'
            : '<span class="uk-badge" style="background:#b45309;color:#fff;">Inactivo</span>';
    }

    function renderUsuarios({ data, listEl, emptyEl, paginationEl, active }) {
        if (!listEl || !emptyEl || !paginationEl) return;

        const query = (document.getElementById('usuariosSearch')?.value ?? '').trim().toLocaleLowerCase('es');
        const filtered = [...data]
            .filter((user) => [user.nombre, user.usuario, user.rol].join(' ').toLocaleLowerCase('es').includes(query))
            .sort((left, right) => left.nombre.localeCompare(right.nombre, 'es', { sensitivity: 'base' }) * usuariosSortDirection);
        const totalPages = Math.max(1, Math.ceil(filtered.length / usuariosPageSize));
        const currentPage = Math.min(Number(listEl.dataset.page || 1), totalPages);
        listEl.dataset.page = String(currentPage);
        const pageItems = filtered.slice((currentPage - 1) * usuariosPageSize, currentPage * usuariosPageSize);

        document.querySelectorAll('[data-usuarios-view]').forEach((button) => {
            const selected = button.dataset.usuariosView === usuariosView;
            button.classList.toggle('uk-button-primary', selected);
            button.classList.toggle('uk-button-default', !selected);
            button.setAttribute('aria-pressed', String(selected));
        });

        if (!pageItems.length) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('uk-hidden');
            paginationEl.innerHTML = '';
            return;
        }
        emptyEl.classList.add('uk-hidden');

        const renderedItems = pageItems.map((user) => {
            const id = Number(user.id);
            const editTarget = active ? `#modalEditarActivo${id}` : `#modalEditarInactivo${id}`;
            const actionTarget = active ? `#modalConfirmarEliminar${id}` : `#modalConfirmarActivar${id}`;
            const actionLabel = active ? 'Inhabilitar' : 'Activar';
            const actionClass = active ? 'uk-button-danger' : 'uk-button-success';
            const name = escapeUsuarioHtml(user.nombre);
            const username = escapeUsuarioHtml(user.usuario);
            const role = escapeUsuarioHtml(user.rol);

            if (usuariosView === 'rows') {
                return `<tr>
                    <td><strong>${name}</strong></td>
                    <td>${role}</td>
                    <td>${username}</td>
                    <td>${usuarioBadge(active)}</td>
                    <td class="uk-text-right"><div class="uk-button-group">
                        <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: ${editTarget}">Editar</button>
                        <button class="uk-button ${actionClass} uk-button-small" type="button" uk-toggle="target: ${actionTarget}">${actionLabel}</button>
                    </div></td>
                </tr>`;
            }

            return `<div class="uk-width-1-1 uk-width-1-2@m uk-width-1-3@l">
                <article class="uk-card uk-card-default app-product-card">
                    <div class="uk-card-body">
                        <div class="app-product-head">
                            <div><h3 class="app-product-title">${name}</h3></div>
                            <button class="app-icon-action" type="button" aria-label="Editar usuario ${name}" title="Editar usuario" uk-toggle="target: ${editTarget}">
                                <span uk-icon="icon: file-edit; ratio: 0.9" aria-hidden="true"></span>
                            </button>
                        </div>
                        <hr class="app-product-divider">
                        <div class="app-product-meta"><p class="app-meta-label">Estado</p><p class="app-meta-value">${usuarioBadge(active)}</p></div>
                        <div class="app-product-meta"><p class="app-meta-label">Usuario</p><p class="app-meta-value">${username}</p></div>
                        <div class="app-product-meta"><p class="app-meta-label">Rol</p><p class="app-meta-value">${role}</p></div>
                        <div class="app-product-actions uk-margin-top" style="justify-content:flex-end;">
                            <button class="uk-button ${actionClass} uk-button-small" type="button" uk-toggle="target: ${actionTarget}">${actionLabel}</button>
                        </div>
                    </div>
                </article>
            </div>`;
        }).join('');

        if (usuariosView === 'rows') {
            listEl.classList.remove('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.add('app-product-list-rows');
            listEl.innerHTML = `<div class="uk-overflow-auto app-product-table-wrap"><table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table">
                <thead><tr><th>Nombre</th><th>Rol</th><th>Usuario</th><th>Estado</th><th class="uk-text-right">Acciones</th></tr></thead>
                <tbody>${renderedItems}</tbody>
            </table></div>`;
        } else {
            listEl.classList.add('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.remove('app-product-list-rows');
            listEl.innerHTML = renderedItems;
        }

        const pageLinks = [
            `<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-usuarios-page="1" aria-label="Primera página">«</a></li>`,
            `<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-usuarios-page="${Math.max(1, currentPage - 1)}">Anterior</a></li>`,
            `<li class="uk-active"><span>${currentPage} / ${totalPages}</span></li>`,
            `<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-usuarios-page="${Math.min(totalPages, currentPage + 1)}">Siguiente</a></li>`,
            `<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-usuarios-page="${totalPages}" aria-label="Última página">»</a></li>`
        ];
        paginationEl.innerHTML = pageLinks.join('');
        paginationEl.querySelectorAll('a[data-usuarios-page]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const page = Number(link.dataset.usuariosPage);
                if (page < 1 || page > totalPages) return;
                listEl.dataset.page = String(page);
                renderUsuarios({ data, listEl, emptyEl, paginationEl, active });
            });
        });
    }

    function renderTodasLasVistasUsuarios() {
        renderUsuarios({
            data: usuariosActivosData,
            listEl: document.getElementById('usuariosActivosList'),
            emptyEl: document.getElementById('usuariosActivosEmpty'),
            paginationEl: document.getElementById('usuariosActivosPagination'),
            active: true
        });
        renderUsuarios({
            data: usuariosInactivosData,
            listEl: document.getElementById('usuariosInactivosList'),
            emptyEl: document.getElementById('usuariosInactivosEmpty'),
            paginationEl: document.getElementById('usuariosInactivosPagination'),
            active: false
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('usuariosSearch');
        const pageSizeSelect = document.getElementById('usuariosPageSize');
        if (pageSizeSelect) {
            pageSizeSelect.value = String(usuariosPageSize);
            pageSizeSelect.addEventListener('change', () => {
                const pageSize = Number(pageSizeSelect.value);
                if (!usuariosPageSizeOptions.includes(pageSize)) return;
                usuariosPageSize = pageSize;
                localStorage.setItem('usuariosPageSize', String(usuariosPageSize));
                document.querySelectorAll('#usuariosActivosList, #usuariosInactivosList').forEach((list) => list.dataset.page = '1');
                renderTodasLasVistasUsuarios();
            });
        }
        searchInput?.addEventListener('input', () => {
            document.querySelectorAll('#usuariosActivosList, #usuariosInactivosList').forEach((list) => list.dataset.page = '1');
            renderTodasLasVistasUsuarios();
        });
        document.querySelectorAll('[data-usuarios-sort]').forEach((button) => {
            button.addEventListener('click', () => {
                usuariosSortDirection = button.dataset.usuariosSort === 'asc' ? 1 : -1;
                document.querySelectorAll('#usuariosActivosList, #usuariosInactivosList').forEach((list) => list.dataset.page = '1');
                renderTodasLasVistasUsuarios();
            });
        });
        document.querySelectorAll('[data-usuarios-view]').forEach((button) => {
            button.addEventListener('click', () => {
                usuariosView = button.dataset.usuariosView;
                localStorage.setItem('usuariosView', usuariosView);
                renderTodasLasVistasUsuarios();
            });
        });
        renderTodasLasVistasUsuarios();
    });
</script>
</body>
</html>
