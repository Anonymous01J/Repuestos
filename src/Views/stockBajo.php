<!DOCTYPE html>
<html lang="es">
<?php
include_once __DIR__ . '/complements/head.php';
include_once __DIR__ . '/complements/header.php';

$productosAlerta = $productosAlerta ?? [];
$sinStock = array_values(array_filter($productosAlerta, static fn($producto) => ($producto['nivel_alerta'] ?? '') === 'SIN_STOCK'));
$stockBajo = array_values(array_filter($productosAlerta, static fn($producto) => ($producto['nivel_alerta'] ?? '') !== 'SIN_STOCK'));
?>

<main class="uk-container uk-container-large app-dashboard-shell">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
        <div>
            <h2 class="app-section-title uk-margin-remove">Alertas de inventario</h2>
            <p class="uk-text-muted uk-margin-small-top">Productos agotados o con existencias iguales o inferiores al mínimo.</p>
        </div>
        <a href="?c=CHome/view" class="uk-button uk-button-default">Volver al panel</a>
    </div>

    <div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-3@m uk-margin-bottom" uk-grid>
        <div><div class="uk-card uk-card-default uk-card-body app-stock-metric"><div class="app-stock-metric-label">Total de alertas</div><div class="app-stock-metric-value"><?= count($productosAlerta) ?></div></div></div>
        <div><div class="uk-card uk-card-default uk-card-body app-stock-metric"><div class="app-stock-metric-label">Sin stock</div><div class="app-stock-metric-value app-stock-metric-value--danger"><?= count($sinStock) ?></div></div></div>
        <div><div class="uk-card uk-card-default uk-card-body app-stock-metric"><div class="app-stock-metric-label">Stock bajo</div><div class="app-stock-metric-value app-stock-metric-value--warning"><?= count($stockBajo) ?></div></div></div>
    </div>

    <section class="uk-card uk-card-default uk-card-body">
        <div class="app-item-list-shell">
            <div class="app-item-toolbar">
                <div class="app-item-search uk-search uk-search-default">
                    <span uk-search-icon></span>
                    <input id="stockAlertSearch" class="uk-search-input" type="search" placeholder="Buscar producto, marca o categoría..." aria-label="Buscar alertas de inventario">
                </div>
                <div class="app-item-sort">
                    <span>Orden</span>
                    <button type="button" class="uk-icon-button" data-stock-sort="asc" aria-label="Orden alfabético ascendente"><span uk-icon="icon: arrow-down; ratio: 0.9"></span></button>
                    <button type="button" class="uk-icon-button" data-stock-sort="desc" aria-label="Orden alfabético descendente"><span uk-icon="icon: arrow-up; ratio: 0.9"></span></button>
                </div>
                <div class="uk-button-group app-view-toggle" role="group" aria-label="Vista de alertas">
                    <button type="button" class="uk-button uk-button-small" data-stock-view="cards" aria-pressed="true"><span uk-icon="icon: grid" aria-hidden="true"></span><span>Tarjetas</span></button>
                    <button type="button" class="uk-button uk-button-small" data-stock-view="rows" aria-pressed="false"><span uk-icon="icon: table" aria-hidden="true"></span><span>Filas</span></button>
                </div>
                <label class="app-page-size-control" for="stockAlertPageSize">
                    <span>Elementos</span>
                    <select id="stockAlertPageSize" class="uk-select uk-form-small">
                        <option value="6">6</option><option value="12">12</option><option value="24">24</option><option value="48">48</option>
                    </select>
                </label>
            </div>

            <div id="stockAlertList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
            <div id="stockAlertEmpty" class="app-empty-state uk-hidden">
                <span uk-icon="icon: check; ratio: 1.5"></span>
                <div>No hay productos que coincidan con la búsqueda.</div>
            </div>
            <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                <div></div><ul id="stockAlertPagination" class="uk-pagination" uk-margin></ul>
            </div>
        </div>
    </section>
</main>

<div id="stockAlertImageModal" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-text-center uk-padding-small">
        <button class="uk-modal-close-default" type="button" uk-close aria-label="Cerrar imagen"></button>
        <img id="stockAlertFullImage" src="" alt="Imagen ampliada del producto" class="uk-border-rounded" style="max-height: 80vh; max-width: 100%; object-fit: contain;">
        <p id="stockAlertImageCaption" class="uk-text-meta uk-margin-small-top uk-margin-remove-bottom"></p>
    </div>
</div>

<?php include_once __DIR__ . '/complements/footer.php'; ?>
<script>
    const stockAlertData = <?= json_encode($productosAlerta, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    let stockAlertView = localStorage.getItem('stockAlertView') === 'rows' ? 'rows' : 'cards';
    let stockAlertSortDirection = 1;
    const stockAlertPageSizes = [6, 12, 24, 48];
    const storedStockAlertPageSize = Number(localStorage.getItem('stockAlertPageSize'));
    let stockAlertPageSize = stockAlertPageSizes.includes(storedStockAlertPageSize) ? storedStockAlertPageSize : 6;

    function escapeStockAlertHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    function renderStockAlertList() {
        const list = document.getElementById('stockAlertList');
        const empty = document.getElementById('stockAlertEmpty');
        const pagination = document.getElementById('stockAlertPagination');
        const query = (document.getElementById('stockAlertSearch').value || '').trim().toLocaleLowerCase('es');
        const sorted = [...stockAlertData].sort((a, b) => String(a.nombre || '').localeCompare(String(b.nombre || ''), 'es', { sensitivity: 'base' }) * stockAlertSortDirection);
        const filtered = sorted.filter((product) => [product.nombre, product.marca, product.categoria, product.nivel_alerta].join(' ').toLocaleLowerCase('es').includes(query));
        const pageCount = Math.max(1, Math.ceil(filtered.length / stockAlertPageSize));
        const currentPage = Math.min(Number(list.dataset.page || 1), pageCount);
        list.dataset.page = String(currentPage);
        const start = (currentPage - 1) * stockAlertPageSize;
        const pageItems = filtered.slice(start, start + stockAlertPageSize);
        const cardsView = stockAlertView === 'cards';

        list.classList.toggle('uk-grid', cardsView);
        list.classList.toggle('uk-grid-medium', cardsView);
        list.classList.toggle('uk-flex-center', cardsView);
        list.classList.toggle('app-product-list-rows', !cardsView);
        document.querySelectorAll('[data-stock-view]').forEach((button) => {
            const active = button.dataset.stockView === stockAlertView;
            button.classList.toggle('uk-button-primary', active);
            button.classList.toggle('uk-button-default', !active);
            button.setAttribute('aria-pressed', String(active));
        });

        if (!pageItems.length) {
            list.innerHTML = '';
            empty.classList.remove('uk-hidden');
            pagination.innerHTML = '';
            return;
        }

        empty.classList.add('uk-hidden');
        const statusHtml = (product) => {
            const noStock = Number(product.stock_actual) === 0;
            const label = noStock ? 'Sin stock' : 'Stock bajo';
            const deficit = Math.max(0, Number(product.stock_minimo) - Number(product.stock_actual));
            return `<span class="uk-label ${noStock ? 'uk-label-danger' : 'uk-label-warning'}">${label}</span><span class="uk-text-small uk-text-muted">Faltan ${deficit} ud${deficit === 1 ? '' : 's'} para el mínimo</span>`;
        };

        if (!cardsView) {
            list.innerHTML = `<div class="uk-overflow-auto app-product-table-wrap"><table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table">
                <thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock actual</th><th>Mínimo</th><th>Máximo</th><th>Estado</th></tr></thead>
                <tbody>${pageItems.map((product) => `<tr>
                    <td><div class="app-product-row-identity"><img class="stock-alert-thumbnail" src="${product.imagen ? `public/assets/img/products/${encodeURIComponent(product.imagen)}` : 'public/assets/img/products/generic.png'}" alt="${escapeStockAlertHtml(product.nombre)}" data-product-image data-image-name="${escapeStockAlertHtml(product.imagen || 'generic.png')}" data-image-title="${escapeStockAlertHtml(product.nombre)}" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';this.dataset.imageName='generic.png';"><div><strong>${escapeStockAlertHtml(product.nombre)}</strong><span>${escapeStockAlertHtml(product.marca || 'Sin marca')}</span></div></div></td>
                    <td>${escapeStockAlertHtml(product.categoria || 'Sin categoría')}</td>
                    <td>${Number(product.precio || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} $</td>
                    <td><strong>${Number(product.stock_actual || 0)}</strong></td><td>${Number(product.stock_minimo || 0)}</td>
                    <td>${product.stock_maximo === null ? '—' : Number(product.stock_maximo)}</td>
                    <td><div class="uk-flex uk-flex-column uk-flex-middle" style="gap: 6px;">${statusHtml(product)}</div></td>
                </tr>`).join('')}</tbody>
            </table></div>`;
        } else {
            list.innerHTML = pageItems.map((product) => `<div class="uk-width-1-1 uk-width-1-2@m uk-width-1-3@l">
                <article class="uk-card uk-card-default app-product-card"><div class="uk-card-body">
                    <button class="stock-alert-image-button" type="button" data-product-image data-image-name="${escapeStockAlertHtml(product.imagen || 'generic.png')}" data-image-title="${escapeStockAlertHtml(product.nombre)}" aria-label="Ver imagen completa de ${escapeStockAlertHtml(product.nombre)}">
                        <img class="stock-alert-image" src="${product.imagen ? `public/assets/img/products/${encodeURIComponent(product.imagen)}` : 'public/assets/img/products/generic.png'}" alt="${escapeStockAlertHtml(product.nombre)}" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';this.closest('button').dataset.imageName='generic.png';">
                        <span class="uk-text-small">Ver imagen completa</span>
                    </button>
                    <div class="app-product-head"><div><h3 class="app-product-title">${escapeStockAlertHtml(product.nombre)}</h3><span class="uk-text-small uk-text-muted">${escapeStockAlertHtml(product.marca || 'Sin marca')}</span></div></div>
                    <hr class="app-product-divider">
                    <div class="app-product-meta"><p class="app-meta-label">Estado</p><p class="app-meta-value">${Number(product.stock_actual) === 0 ? 'Sin stock' : 'Stock bajo'}</p></div>
                    <div class="app-product-meta"><p class="app-meta-label">Stock actual</p><p class="app-meta-value">${Number(product.stock_actual || 0)}</p></div>
                    <div class="app-product-meta"><p class="app-meta-label">Stock mínimo</p><p class="app-meta-value">${Number(product.stock_minimo || 0)}</p></div>
                    <div class="app-product-meta"><p class="app-meta-label">Stock máximo</p><p class="app-meta-value">${product.stock_maximo === null ? '—' : Number(product.stock_maximo)}</p></div>
                    <div class="app-product-meta"><p class="app-meta-label">Categoría</p><p class="app-meta-value">${escapeStockAlertHtml(product.categoria || 'Sin categoría')}</p></div>
                    <div class="app-product-meta"><p class="app-meta-label">Precio</p><p class="app-meta-value">${Number(product.precio || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} $</p></div>
                    <div class="uk-margin-small-top">${statusHtml(product)}</div>
                </div></article>
            </div>`).join('');
        }

        pagination.innerHTML = `<li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-stock-page="${Math.max(1, currentPage - 1)}">Anterior</a></li>
            <li class="uk-active"><span>${currentPage} / ${pageCount}</span></li>
            <li class="${currentPage >= pageCount ? 'uk-disabled' : ''}"><a href="#" data-stock-page="${Math.min(pageCount, currentPage + 1)}">Siguiente</a></li>`;
        pagination.querySelectorAll('a[data-stock-page]').forEach((anchor) => anchor.addEventListener('click', (event) => {
            event.preventDefault();
            list.dataset.page = anchor.dataset.stockPage;
            renderStockAlertList();
        }));
        list.querySelectorAll('[data-product-image]').forEach((imageTrigger) => imageTrigger.addEventListener('click', () => {
            const image = document.getElementById('stockAlertFullImage');
            image.src = `public/assets/img/products/${encodeURIComponent(imageTrigger.dataset.imageName || 'generic.png')}`;
            image.onerror = function () {
                this.onerror = null;
                this.src = 'public/assets/img/products/generic.png';
            };
            document.getElementById('stockAlertImageCaption').textContent = imageTrigger.dataset.imageTitle || 'Producto';
            if (window.UIkit && UIkit.modal) UIkit.modal('#stockAlertImageModal').show();
        }));
    }

    document.addEventListener('DOMContentLoaded', () => {
        const search = document.getElementById('stockAlertSearch');
        const pageSize = document.getElementById('stockAlertPageSize');
        pageSize.value = String(stockAlertPageSize);
        search.addEventListener('input', () => { document.getElementById('stockAlertList').dataset.page = '1'; renderStockAlertList(); });
        pageSize.addEventListener('change', () => {
            const selected = Number(pageSize.value);
            if (!stockAlertPageSizes.includes(selected)) return;
            stockAlertPageSize = selected;
            localStorage.setItem('stockAlertPageSize', String(selected));
            document.getElementById('stockAlertList').dataset.page = '1';
            renderStockAlertList();
        });
        document.querySelectorAll('[data-stock-sort]').forEach((button) => button.addEventListener('click', () => {
            stockAlertSortDirection = button.dataset.stockSort === 'asc' ? 1 : -1;
            renderStockAlertList();
        }));
        document.querySelectorAll('[data-stock-view]').forEach((button) => button.addEventListener('click', () => {
            stockAlertView = button.dataset.stockView;
            localStorage.setItem('stockAlertView', stockAlertView);
            renderStockAlertList();
        }));
        renderStockAlertList();
    });
</script>
</html>