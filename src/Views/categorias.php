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
            <h2 class="app-section-title uk-margin-remove">Gestión de categorías</h2>
            <div class="uk-text-muted uk-margin-small-top">Categorías de productos</div>
        </div>
        <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nueva categoría</button>
    </div>

    <div class="uk-card uk-card-default uk-card-body">
        <div class="app-item-list-shell">
            <div class="app-item-toolbar">
                <div class="app-item-search uk-search uk-search-default">
                    <span uk-search-icon></span>
                    <input id="categoriasSearch" class="uk-search-input" type="search" placeholder="Buscar categoría..." aria-label="Buscar categoría">
                </div>

                <div class="app-item-sort">
                    <span>Orden</span>
                    <button type="button" class="uk-icon-button" data-categorias-sort="asc" aria-label="Orden ascendente">
                        <span uk-icon="icon: arrow-down; ratio: 0.9"></span>
                    </button>
                    <button type="button" class="uk-icon-button" data-categorias-sort="desc" aria-label="Orden descendente">
                        <span uk-icon="icon: arrow-up; ratio: 0.9"></span>
                    </button>
                </div>

                <div class="uk-button-group app-view-toggle" role="group" aria-label="Vista de categorías">
                    <button type="button" class="uk-button uk-button-small" data-categorias-view="cards" aria-pressed="true">
                        <span uk-icon="icon: grid" aria-hidden="true"></span><span>Tarjetas</span>
                    </button>
                    <button type="button" class="uk-button uk-button-small" data-categorias-view="rows" aria-pressed="false">
                        <span uk-icon="icon: table" aria-hidden="true"></span><span>Filas</span>
                    </button>
                </div>

                <label class="app-page-size-control" for="categoriasPageSize">
                    <span>Elementos</span>
                    <select id="categoriasPageSize" class="uk-select uk-form-small">
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </label>
            </div>

            <div id="categoriasList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
            <div id="categoriasEmpty" class="app-empty-state uk-hidden">
                <span uk-icon="icon: tag; ratio: 1.5"></span>
                <div>No hay categorías para mostrar.</div>
            </div>
            <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                <div></div>
                <ul id="categoriasPagination" class="uk-pagination" uk-margin></ul>
            </div>
        </div>
    </div>
</div>

<?php foreach ($categorias as $c): ?>
    <div id="modalEditar<?= $c['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar categoría</h2>
            <form action="?c=CCategorias/edit" method="POST">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <div class="uk-margin">
                    <label class="uk-form-label">Nombre</label>
                    <div class="uk-form-controls">
                        <input type="text" name="nombre" class="uk-input" value="<?= htmlspecialchars($c['nombre']) ?>" required>
                    </div>
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEliminar<?= $c['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Confirmar eliminación</h2>
            <form action="?c=CCategorias/delete" method="POST">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <p>¿Estás seguro de que deseas eliminar la categoría <strong><?= htmlspecialchars($c['nombre']) ?></strong>?</p>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<div id="modalAgregar" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Agregar categoría</h2>
        <form action="?c=CCategorias/add" method="POST">
            <div class="uk-margin">
                <label class="uk-form-label">Nombre</label>
                <div class="uk-form-controls">
                    <input type="text" name="nombre" class="uk-input" required>
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
    const categoriasData = <?= json_encode($categorias, JSON_UNESCAPED_UNICODE) ?>;
    let categoriasView = localStorage.getItem('categoriasView') === 'rows' ? 'rows' : 'cards';
    const categoriasPageSizeOptions = [6, 12, 24, 48];
    const categoriasSavedPageSize = Number(localStorage.getItem('categoriasPageSize'));
    let categoriasPageSize = categoriasPageSizeOptions.includes(categoriasSavedPageSize) ? categoriasSavedPageSize : 6;
    let categoriasSortDirection = 1;

    function renderCategoriasList({ data, listEl, emptyEl, paginationEl }) {
        const searchInput = document.getElementById('categoriasSearch');
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const sorted = [...data].sort((a, b) => {
            const left = (a.nombre || '').toLowerCase();
            const right = (b.nombre || '').toLowerCase();
            return left.localeCompare(right, 'es', { sensitivity: 'base' }) * categoriasSortDirection;
        });

        const filtered = sorted.filter((categoria) => {
            const texto = [categoria.id, categoria.nombre].join(' ').toLowerCase();
            return !query || texto.includes(query);
        });

        const pageSize = categoriasPageSize;
        const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
        const currentPage = Math.min(Number(listEl.dataset.page || 1), totalPages);
        listEl.dataset.page = String(currentPage);
        const start = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(start, start + pageSize);

        document.querySelectorAll('[data-categorias-view]').forEach((button) => {
            const active = button.dataset.categoriasView === categoriasView;
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

        if (categoriasView === 'rows') {
            listEl.classList.remove('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.add('app-product-list-rows');
            listEl.innerHTML = `
                <div class="uk-overflow-auto app-product-table-wrap">
                    <table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class="uk-text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pageItems.map((categoria) => `
                                <tr>
                                    <td>${categoria.id}</td>
                                    <td><strong>${categoria.nombre}</strong></td>
                                    <td class="uk-text-right">
                                        <div class="uk-button-group">
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar${categoria.id}">Editar</button>
                                            <button class="uk-button uk-button-danger uk-button-small" type="button" uk-toggle="target: #modalEliminar${categoria.id}">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            listEl.classList.add('uk-grid', 'uk-grid-medium', 'uk-flex-center');
            listEl.classList.remove('app-product-list-rows');
            listEl.innerHTML = pageItems.map((categoria) => `
                <div class="uk-width-1-1 uk-width-1-2@m uk-width-1-3@l">
                    <div class="uk-card uk-card-default app-product-card">
                        <div class="uk-card-body">
                            <div class="app-product-head">
                                <div>
                                    <h4 class="app-product-title">${categoria.nombre}</h4>
                                </div>
                                <div class="app-product-actions">
                                    <button class="app-icon-action" type="button" aria-label="Editar categoría" uk-toggle="target: #modalEditar${categoria.id}">
                                        <span uk-icon="icon: file-edit; ratio: 0.9"></span>
                                    </button>
                                </div>
                            </div>
                            <hr class="app-product-divider">
                            <div class="app-product-meta">
                                <p class="app-meta-label">ID</p>
                                <p class="app-meta-value">${categoria.id}</p>
                            </div>
                            <div class="app-product-meta">
                                <p class="app-meta-label">Nombre</p>
                                <p class="app-meta-value">${categoria.nombre}</p>
                            </div>
                            <div class="app-product-actions app-product-actions--bottom uk-margin-top">
                                <button class="uk-button uk-button-danger uk-button-small" type="button" uk-toggle="target: #modalEliminar${categoria.id}">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        const pageLinks = [];
        pageLinks.push(`<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-categorias-page="1">«</a></li>`);
        pageLinks.push(`<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-categorias-page="${Math.max(1, currentPage - 1)}">Anterior</a></li>`);
        pageLinks.push(`<li class="uk-active"><span>${currentPage}</span></li>`);
        pageLinks.push(`<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-categorias-page="${Math.min(totalPages, currentPage + 1)}">Siguiente</a></li>`);
        pageLinks.push(`<li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-categorias-page="${totalPages}">»</a></li>`);
        paginationEl.innerHTML = pageLinks.join('');

        paginationEl.querySelectorAll('a[data-categorias-page]').forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const nextPage = Number(this.dataset.categoriasPage || 1);
                if (Number.isNaN(nextPage) || nextPage < 1 || nextPage > totalPages) return;
                listEl.dataset.page = String(nextPage);
                renderCategoriasList({ data, listEl, emptyEl, paginationEl });
            });
        });
    }

    function renderCategoriasLists() {
        const listEl = document.getElementById('categoriasList');
        const emptyEl = document.getElementById('categoriasEmpty');
        const paginationEl = document.getElementById('categoriasPagination');
        if (listEl && emptyEl && paginationEl) {
            renderCategoriasList({ data: categoriasData, listEl, emptyEl, paginationEl });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('categoriasSearch');
        const pageSizeSelect = document.getElementById('categoriasPageSize');

        if (pageSizeSelect) {
            pageSizeSelect.value = String(categoriasPageSize);
            pageSizeSelect.addEventListener('change', function () {
                const selectedPageSize = Number(this.value);
                if (!categoriasPageSizeOptions.includes(selectedPageSize)) return;
                categoriasPageSize = selectedPageSize;
                localStorage.setItem('categoriasPageSize', String(categoriasPageSize));
                const listEl = document.getElementById('categoriasList');
                if (listEl) listEl.dataset.page = '1';
                renderCategoriasLists();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const listEl = document.getElementById('categoriasList');
                if (listEl) listEl.dataset.page = '1';
                renderCategoriasLists();
            });
        }

        document.querySelectorAll('[data-categorias-sort]').forEach((button) => {
            button.addEventListener('click', function () {
                categoriasSortDirection = this.dataset.categoriasSort === 'asc' ? 1 : -1;
                const listEl = document.getElementById('categoriasList');
                if (listEl) listEl.dataset.page = '1';
                renderCategoriasLists();
            });
        });

        document.querySelectorAll('[data-categorias-view]').forEach((button) => {
            button.addEventListener('click', function () {
                categoriasView = this.dataset.categoriasView;
                localStorage.setItem('categoriasView', categoriasView);
                const listEl = document.getElementById('categoriasList');
                if (listEl) listEl.dataset.page = '1';
                renderCategoriasLists();
            });
        });

        renderCategoriasLists();
    });
</script>
=======
    <?php require_once __DIR__ . '/complements/footer.php'; ?>
>>>>>>> 427dc3a866328b94a630a4682abb9d435b139c9d
</body>
</html>