<!DOCTYPE html>
<html lang="es">
<?php
    include_once __DIR__ . '/complements/head.php';
    include_once __DIR__ . '/complements/header.php';
?>

<?php if (isset($_GET['success'])): ?>
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="toastSuccess" class="toast show align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">✅ Operación realizada con éxito.</div>
            <button type="button" class="uk-close uk-position-small uk-position-top-right" aria-label="Cerrar" onclick="this.closest('.toast').remove();"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="uk-container uk-container-large app-dashboard-shell">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
        <div>
            <h2 class="app-section-title uk-margin-remove">Gestión de proveedores</h2>
            <div class="uk-text-muted uk-margin-small-top">Proveedores activos e inactivos</div>
        </div>
        <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nuevo Proveedor</button>
    </div>

    <div class="uk-card uk-card-default uk-card-body">
        <div class="app-item-list-shell">
            <div class="app-item-toolbar">
                <div class="app-item-search uk-search uk-search-default">
                    <span uk-search-icon></span>
                    <input id="proveedoresSearch" class="uk-search-input" type="search" placeholder="Buscar proveedor..." aria-label="Buscar proveedor">
                </div>

                <div class="app-item-sort">
                    <span>Orden</span>
                    <button type="button" class="uk-icon-button" data-proveedores-sort="asc" aria-label="Orden ascendente">
                        <span uk-icon="icon: arrow-down; ratio: 0.9"></span>
                    </button>
                    <button type="button" class="uk-icon-button" data-proveedores-sort="desc" aria-label="Orden descendente">
                        <span uk-icon="icon: arrow-up; ratio: 0.9"></span>
                    </button>
                </div>

                <div class="uk-button-group app-view-toggle" role="group" aria-label="Vista de proveedores">
                    <button type="button" class="uk-button uk-button-small" data-proveedores-view="cards" aria-pressed="true">
                        <span uk-icon="icon: grid" aria-hidden="true"></span><span>Tarjetas</span>
                    </button>
                    <button type="button" class="uk-button uk-button-small" data-proveedores-view="rows" aria-pressed="false">
                        <span uk-icon="icon: table" aria-hidden="true"></span><span>Filas</span>
                    </button>
                </div>

                <label class="app-page-size-control" for="proveedoresPageSize">
                    <span>Elementos</span>
                    <select id="proveedoresPageSize" class="uk-select uk-form-small">
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </label>
            </div>

            <ul class="uk-tab uk-margin-bottom" uk-tab>
                <li class="uk-active"><a href="#">Activos <span class="uk-badge"><?= count($proveedores) ?></span></a></li>
                <li><a href="#">Inactivos <span class="uk-badge"><?= count($proveedoresInactive) ?></span></a></li>
            </ul>

            <ul class="uk-switcher">
                <li>
                    <div id="proveedoresActivosList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
                    <div id="proveedoresActivosEmpty" class="app-empty-state uk-hidden">
                        <span uk-icon="icon: users; ratio: 1.5"></span>
                        <div>No hay proveedores activos para mostrar.</div>
                    </div>
                    <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                        <div></div>
                        <ul id="proveedoresActivosPagination" class="uk-pagination" uk-margin></ul>
                    </div>
                </li>

                <li>
                    <div id="proveedoresInactivosList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
                    <div id="proveedoresInactivosEmpty" class="app-empty-state uk-hidden">
                        <span uk-icon="icon: users; ratio: 1.5"></span>
                        <div>No hay proveedores inactivos para mostrar.</div>
                    </div>
                    <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                        <div></div>
                        <ul id="proveedoresInactivosPagination" class="uk-pagination" uk-margin></ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php foreach ($proveedores as $p): ?>
    <div id="modalEditarActivo<?= $p['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar Proveedor</h2>
            <form action="?c=CProveedores/edit" method="POST">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <div class="uk-margin">
                    <label class="uk-form-label">Teléfono</label>
                    <div class="uk-form-controls">
                        <input type="text" name="telefono" class="uk-input" value="<?= htmlspecialchars($p['telefono']) ?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Dirección</label>
                    <div class="uk-form-controls">
                        <textarea name="direccion" class="uk-textarea"><?= htmlspecialchars($p['direccion']) ?></textarea>
                    </div>
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalConfirmarEliminar<?= $p['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Confirmar eliminación</h2>
            <form action="?c=CProveedores/delete" method="POST">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <p>¿Estás seguro de que deseas inhabilitar a <?= htmlspecialchars($p['nombre']) ?>?</p>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php foreach ($proveedoresInactive as $p): ?>
    <div id="modalEditarInactivo<?= $p['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar Proveedor</h2>
            <form action="?c=CProveedores/edit" method="POST">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <div class="uk-margin">
                    <label class="uk-form-label">Teléfono</label>
                    <div class="uk-form-controls">
                        <input type="text" name="telefono" class="uk-input" value="<?= htmlspecialchars($p['telefono']) ?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Dirección</label>
                    <div class="uk-form-controls">
                        <textarea name="direccion" class="uk-textarea"><?= htmlspecialchars($p['direccion']) ?></textarea>
                    </div>
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalConfirmarActivar<?= $p['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Confirmar activación</h2>
            <form action="?c=CProveedores/active" method="POST">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <p>¿Estás seguro de que deseas habilitar a <?= htmlspecialchars($p['nombre']) ?>?</p>
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
        <h2 class="uk-modal-title">Agregar Proveedor</h2>
        <form action="?c=CProveedores/add" method="POST">
            <div class="uk-margin">
                <label class="uk-form-label">Nombre</label>
                <div class="uk-form-controls">
                    <input type="text" name="nombre" class="uk-input" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Cédula/RIF</label>
                <div class="uk-form-controls">
                    <input type="text" name="cedula_rif" class="uk-input" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Teléfono</label>
                <div class="uk-form-controls">
                    <input type="text" name="telefono" class="uk-input" required>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Dirección</label>
                <div class="uk-form-controls">
                    <textarea name="direccion" class="uk-textarea" required></textarea>
                </div>
            </div>
            <div class="uk-text-right">
                <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                <button type="submit" class="uk-button uk-button-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php
    include_once __DIR__ . '/complements/footer.php';
?>

<script>
    const proveedoresActivosData = <?= json_encode($proveedores, JSON_UNESCAPED_UNICODE) ?>;
    const proveedoresInactivosData = <?= json_encode($proveedoresInactive, JSON_UNESCAPED_UNICODE) ?>;
    let proveedoresView = localStorage.getItem('proveedoresView') === 'rows' ? 'rows' : 'cards';
    const proveedoresPageSizeOptions = [6, 12, 24, 48];
    const proveedoresSavedPageSize = Number(localStorage.getItem('proveedoresPageSize'));
    let proveedoresPageSize = proveedoresPageSizeOptions.includes(proveedoresSavedPageSize) ? proveedoresSavedPageSize : 6;
    let proveedoresSortDirection = 1;

    function getProveedorBadge(isActive) {
        return isActive
            ? '<span class="uk-badge uk-border-rounded" style="background:#22c55e;color:#fff;">Activo</span>'
            : '<span class="uk-badge uk-border-rounded" style="background:#f59e0b;color:#111827;">Inactivo</span>';
    }

    function renderProveedoresList({ data, listEl, emptyEl, paginationEl, isActive }) {
        if (!listEl || !emptyEl || !paginationEl) return;

        const searchInput = document.getElementById('proveedoresSearch');
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const sorted = [...data].sort((a, b) => {
            const left = (a.nombre || '').toLowerCase();
            const right = (b.nombre || '').toLowerCase();
            return left.localeCompare(right, 'es', { sensitivity: 'base' }) * proveedoresSortDirection;
        });

        const filtered = sorted.filter((proveedor) => {
            const texto = [proveedor.nombre, proveedor.cedula_rif, proveedor.telefono, proveedor.direccion].join(' ').toLowerCase();
            return !query || texto.includes(query);
        });

        const pageSize = proveedoresPageSize;
        const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
        const currentPage = Math.min(Number(listEl.dataset.page || 1), totalPages);
        listEl.dataset.page = String(currentPage);
        const start = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(start, start + pageSize);

        document.querySelectorAll('[data-proveedores-view]').forEach((button) => {
            const active = button.dataset.proveedoresView === proveedoresView;
            button.classList.toggle('uk-button-primary', active);
            button.classList.toggle('uk-button-default', !active);
            button.setAttribute('aria-pressed', String(active));
        });

        if (!pageItems.length) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('uk-hidden');
            paginationEl.innerHTML = '';
            return;
        }

        emptyEl.classList.add('uk-hidden');

        if (proveedoresView === 'rows') {
            listEl.classList.remove('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.add('app-product-list-rows');
            listEl.innerHTML = `
                <div class="uk-overflow-auto app-product-table-wrap">
                    <table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula/RIF</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th class="uk-text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pageItems.map((proveedor) => {
                                const targetEdit = isActive ? `#modalEditarActivo${proveedor.id}` : `#modalEditarInactivo${proveedor.id}`;
                                const targetDelete = isActive ? `#modalConfirmarEliminar${proveedor.id}` : `#modalConfirmarActivar${proveedor.id}`;
                                const actionButton = isActive
                                    ? '<button type="button" class="uk-button uk-button-danger uk-button-small" uk-toggle="target: ' + targetDelete + '">Eliminar</button>'
                                    : '<button type="button" class="uk-button uk-button-success uk-button-small" uk-toggle="target: ' + targetDelete + '">Activar</button>';

                                return `
                                    <tr>
                                        <td><strong>${proveedor.nombre}</strong></td>
                                        <td>${proveedor.cedula_rif}</td>
                                        <td>${proveedor.telefono}</td>
                                        <td>${proveedor.direccion}</td>
                                        <td>${getProveedorBadge(isActive)}</td>
                                        <td class="uk-text-right">
                                            <div class="uk-button-group">
                                                <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: ${targetEdit}">Editar</button>
                                                ${actionButton}
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            listEl.classList.add('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.remove('app-product-list-rows');
            listEl.innerHTML = pageItems.map((proveedor) => {
                const targetEdit = isActive ? `#modalEditarActivo${proveedor.id}` : `#modalEditarInactivo${proveedor.id}`;
                const targetDelete = isActive ? `#modalConfirmarEliminar${proveedor.id}` : `#modalConfirmarActivar${proveedor.id}`;
                const actionLabel = isActive ? 'Eliminar' : 'Activar';
                const actionClass = isActive ? 'uk-button-danger' : 'uk-button-success';
                return `
                    <div class="uk-width-1-1 uk-width-1-2@m uk-width-1-3@l">
                        <div class="uk-card uk-card-default app-product-card">
                            <div class="uk-card-body">
                                <div class="app-product-head">
                                    <div>
                                        <h4 class="app-product-title">${proveedor.nombre}</h4>
                                    </div>
                                    <div class="app-product-actions">
                                        <button class="app-icon-action" type="button" aria-label="Editar proveedor" uk-toggle="target: ${targetEdit}">
                                            <span uk-icon="icon: file-edit; ratio: 0.9"></span>
                                        </button>
                                    </div>
                                </div>
                                <hr class="app-product-divider">
                                <div class="app-product-meta">
                                    <p class="app-meta-label">Estado</p>
                                    <p class="app-meta-value">${getProveedorBadge(isActive)}</p>
                                </div>
                                <div class="app-product-meta">
                                    <p class="app-meta-label">Cédula</p>
                                    <p class="app-meta-value">${proveedor.cedula_rif}</p>
                                </div>
                                <div class="app-product-meta">
                                    <p class="app-meta-label">Teléfono</p>
                                    <p class="app-meta-value">${proveedor.telefono}</p>
                                </div>
                                <div class="app-product-meta">
                                    <p class="app-meta-label">Dirección</p>
                                    <p class="app-meta-value">${proveedor.direccion}</p>
                                </div>
                                <div class="app-product-actions app-product-actions--bottom uk-margin-top">
                                    <button class="uk-button ${actionClass} uk-button-small" type="button" uk-toggle="target: ${targetDelete}">${actionLabel}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        const pageLinks = [];
        pageLinks.push(`<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-proveedores-page="1">«</a></li>`);
        pageLinks.push(`<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-proveedores-page="${Math.max(1, currentPage - 1)}">Anterior</a></li>`);
        pageLinks.push(`<li class="uk-active"><span>${currentPage}</span></li>`);
        pageLinks.push(`<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-proveedores-page="${Math.min(totalPages, currentPage + 1)}">Siguiente</a></li>`);
        pageLinks.push(`<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-proveedores-page="${totalPages}">»</a></li>`);
        paginationEl.innerHTML = pageLinks.join('');

        paginationEl.querySelectorAll('a[data-proveedores-page]').forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const nextPage = Number(this.dataset.proveedoresPage || 1);
                if (Number.isNaN(nextPage) || nextPage < 1 || nextPage > totalPages) return;
                listEl.dataset.page = String(nextPage);
                renderProveedoresList({ data, listEl, emptyEl, paginationEl, isActive });
            });
        });
    }

    function renderProveedoresLists() {
        const activeList = document.getElementById('proveedoresActivosList');
        const inactiveList = document.getElementById('proveedoresInactivosList');
        const activeEmpty = document.getElementById('proveedoresActivosEmpty');
        const inactiveEmpty = document.getElementById('proveedoresInactivosEmpty');
        const activePagination = document.getElementById('proveedoresActivosPagination');
        const inactivePagination = document.getElementById('proveedoresInactivosPagination');

        if (activeList && activeEmpty && activePagination) {
            renderProveedoresList({ data: proveedoresActivosData, listEl: activeList, emptyEl: activeEmpty, paginationEl: activePagination, isActive: true });
        }

        if (inactiveList && inactiveEmpty && inactivePagination) {
            renderProveedoresList({ data: proveedoresInactivosData, listEl: inactiveList, emptyEl: inactiveEmpty, paginationEl: inactivePagination, isActive: false });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('proveedoresSearch');
        const pageSizeSelect = document.getElementById('proveedoresPageSize');

        if (pageSizeSelect) {
            pageSizeSelect.value = String(proveedoresPageSize);
            pageSizeSelect.addEventListener('change', function () {
                const selectedPageSize = Number(this.value);
                if (!proveedoresPageSizeOptions.includes(selectedPageSize)) return;
                proveedoresPageSize = selectedPageSize;
                localStorage.setItem('proveedoresPageSize', String(proveedoresPageSize));
                document.querySelectorAll('#proveedoresActivosList, #proveedoresInactivosList').forEach((list) => {
                    list.dataset.page = '1';
                });
                renderProveedoresLists();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                document.querySelectorAll('#proveedoresActivosList, #proveedoresInactivosList').forEach((list) => {
                    list.dataset.page = '1';
                });
                renderProveedoresLists();
            });
        }

        document.querySelectorAll('[data-proveedores-sort]').forEach((button) => {
            button.addEventListener('click', function () {
                proveedoresSortDirection = this.dataset.proveedoresSort === 'asc' ? 1 : -1;
                document.querySelectorAll('#proveedoresActivosList, #proveedoresInactivosList').forEach((list) => {
                    list.dataset.page = '1';
                });
                renderProveedoresLists();
            });
        });

        document.querySelectorAll('[data-proveedores-view]').forEach((button) => {
            button.addEventListener('click', function () {
                proveedoresView = this.dataset.proveedoresView;
                localStorage.setItem('proveedoresView', proveedoresView);
                document.querySelectorAll('#proveedoresActivosList, #proveedoresInactivosList').forEach((list) => {
                    list.dataset.page = '1';
                });
                renderProveedoresLists();
            });
        });

        renderProveedoresLists();
    });
</script>
</body>
</html>
