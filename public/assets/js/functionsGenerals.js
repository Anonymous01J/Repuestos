function openModalElement(target) {
    const modalEl = typeof target === 'string' ? document.querySelector(target) : target;
    if (!modalEl) return;

    if (window.UIkit && UIkit.modal) {
        const modal = UIkit.modal(modalEl);
        if (modal) {
            modal.show();
            return;
        }
    }

    if (modalEl.classList) {
        modalEl.classList.add('uk-open');
    }
}

function hideModalElement(target) {
    const modalEl = typeof target === 'string' ? document.querySelector(target) : target;
    if (!modalEl) return;

    if (window.UIkit && UIkit.modal) {
        const modal = UIkit.modal(modalEl);
        if (modal) {
            modal.hide();
            return;
        }
    }

    if (modalEl.classList) {
        modalEl.classList.remove('uk-open');
    }
}

function hideToastElement(toastEl) {
    if (!toastEl) return;

    toastEl.classList.remove('uk-open');
    toastEl.setAttribute('hidden', 'hidden');
}

function showTabFromTrigger(trigger) {
    if (!trigger) return;

    const targetSelector = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href') || trigger.getAttribute('data-target');
    if (!targetSelector) return;

    const target = document.querySelector(targetSelector);
    if (!target) return;

    const uiTabList = trigger.closest('ul.uk-tab');
    if (uiTabList) {
        const items = uiTabList.querySelectorAll('li');
        items.forEach(function (item) {
            item.classList.remove('uk-active');
        });

        const activeItem = trigger.closest('li');
        if (activeItem) {
            activeItem.classList.add('uk-active');
        }

        const switcher = uiTabList.nextElementSibling;
        if (switcher && switcher.classList.contains('uk-switcher')) {
            const panes = switcher.children;
            for (let i = 0; i < panes.length; i++) {
                panes[i].classList.remove('uk-active');
                panes[i].setAttribute('hidden', 'hidden');
            }

            if (target && target.parentElement === switcher) {
                target.classList.add('uk-active');
                target.removeAttribute('hidden');
            }
        }

        return;
    }

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tabButton) {
        tabButton.classList.remove('active');
    });

    document.querySelectorAll('.tab-pane').forEach(function (pane) {
        pane.classList.remove('show', 'active');
    });

    trigger.classList.add('active');
    target.classList.add('show', 'active');
}

function applyTheme(theme) {
    const selectedTheme = theme === 'light' ? 'light' : 'dark';
    document.body.classList.toggle('light-theme', selectedTheme === 'light');
    document.body.classList.toggle('dark-theme', selectedTheme === 'dark');
    document.documentElement.setAttribute('data-theme', selectedTheme);
    localStorage.setItem('btnColor', selectedTheme);

    const bcvIcon = document.getElementById('bcvIcon');
    if (bcvIcon) {
        bcvIcon.src = selectedTheme === 'light'
            ? './public/assets/img/bcv.png'
            : './public/assets/img/bcv2.png';
    }

    const themeButton = document.getElementById('themeToggleBtn');
    if (themeButton) {
        const themeIcon = themeButton.querySelector('[uk-icon]');
        if (themeIcon && window.UIkit && UIkit.icon && !UIkit.getComponent(themeIcon, 'icon')) {
            UIkit.icon(themeIcon, { icon: 'settings', ratio: 1.15 });
        }
        themeButton.setAttribute('aria-label', selectedTheme === 'light' ? 'Cambiar a tema oscuro' : 'Cambiar a tema claro');
        themeButton.setAttribute('title', selectedTheme === 'light' ? 'Cambiar a tema oscuro' : 'Cambiar a tema claro');
    }
}

function initThemeToggle() {
    const storedTheme = localStorage.getItem('btnColor') || 'dark';
    applyTheme(storedTheme);

    const themeButton = document.getElementById('themeToggleBtn');
    if (themeButton) {
        themeButton.addEventListener('click', function () {
            const nextTheme = document.body.classList.contains('light-theme') ? 'dark' : 'light';
            applyTheme(nextTheme);
        });
    }
}

function initAppLoader() {
    const loader = document.getElementById('appLoader');
    if (!loader) return;

    const hideLoader = function () {
        loader.classList.add('is-hidden');
        window.setTimeout(function () {
            loader.remove();
        }, 400);
    };

    if (document.readyState === 'complete') {
        hideLoader();
        return;
    }

    window.addEventListener('load', hideLoader, { once: true });
}

$(document).ready(function () {
    initThemeToggle();
    initAppLoader();

    const ahora = new Date();
    const tzOffset = ahora.getTimezoneOffset() * 60000; // en milisegundos
    const localISO = new Date(ahora - tzOffset).toISOString().slice(0, 16);
    $('#fecha').val(localISO);

    if ($.fn && $.fn.DataTable) {
        $('.table-DT').DataTable({
            language: {
                url: 'public/assets/libs/dataTable/Spanish.json'
            },
            responsive: true
        });

        // Para la tabla de entradas
        $('#tablaEntradas').DataTable({
            language: {
                url: 'public/assets/libs/dataTable/Spanish.json'
            },
            order: [[0, 'desc']],
            pageLength: 10,
            responsive: true
        });

        // Para la tabla de ventas
        $('#tablaVentas').DataTable({
            language: {
                url: 'public/assets/libs/dataTable/Spanish.json'
            },
            order: [[0, 'desc']],
            pageLength: 10,
            responsive: true
        });
    }

    //inputs de precios
    function InputPrice(input) {
        // BUG: Si se llama varias veces, se agregan múltiples listeners.
        // FIX: Usar jQuery .off('input') antes de .on('input') para evitar duplicados.
        $(input).off('input').on('input', function (e) {
            let valueUser = e.target.value.replace(/,/g, "").replace(/\./g, "").replace(/[^0-9]/g, "");
            let valueLength = valueUser.length;
            if (valueLength <= 2) {
                e.target.value = "0" + "," + valueUser;
            } else if (valueLength >= 3) {
                let it = valueUser.slice(0, valueLength - 2) + "," + valueUser.slice(valueLength - 2);
                e.target.value = it.replace(/^0(?=\d)\,?/, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });
    }
    InputPrice(".input-price");

    // Modal Tasa BCV
    const modalTasa = document.getElementById('modalTasa');
    if (modalTasa && window.UIkit && UIkit.util) {
        UIkit.util.on(modalTasa, 'beforeshow', function () {
            $.get('?c=CHome/tasa', function(data) {
                let tasa = JSON.parse(data);
                if (!tasa.error) {
                    $('#inputTasa').val(tasa.valor);
                    $('#inputTasaId').val(tasa.id);
                    $('#fechaActualizacion').text(tasa.fecha_actualizacion);
                } else {
                    $('#inputTasa').val('');
                    $('#inputTasaId').val('');
                    $('#fechaActualizacion').text('No disponible');
                }
            });
        });
    }

    $('#btnActualizarApi').on('click', function() {
        fetch("https://ve.dolarapi.com/v1/dolares")
            .then(res => res.json())
            .then(data => {
                $('#inputTasa').val(parseFloat(data[0].promedio).toFixed(2));
            });
    });

    $('#formTasa').on('submit', function(e) {
        e.preventDefault();
        $.post('?c=CHome/updateTasa', $(this).serialize(), function() {
            hideModalElement('#modalTasa');
            $('#Dolar_bcv').text($('#inputTasa').val() + " Bs");
        });
    });

    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const targetSelector = this.getAttribute('data-bs-target');
            if (targetSelector) {
                openModalElement(targetSelector);
            }
        });
    });

    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const modal = this.closest('.modal') || this.closest('[uk-modal]');
            if (modal) {
                hideModalElement(modal);
            }
        });
    });

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            showTabFromTrigger(trigger);
        });
    });

});