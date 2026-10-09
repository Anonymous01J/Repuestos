function openModalElement(target) {
    const modalEl = typeof target === 'string' ? document.querySelector(target) : target;
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        return;
    }

    if (window.UIkit && UIkit.modal) {
        const modal = UIkit.modal(modalEl);
        if (modal) modal.show();
    }
}

function hideModalElement(target) {
    const modalEl = typeof target === 'string' ? document.querySelector(target) : target;
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
        return;
    }

    if (window.UIkit && UIkit.modal) {
        const modal = UIkit.modal(modalEl);
        if (modal) modal.hide();
    }
}

function hideToastElement(toastEl) {
    if (!toastEl) return;

    if (window.bootstrap && bootstrap.Toast) {
        const toast = bootstrap.Toast.getInstance(toastEl) || new bootstrap.Toast(toastEl);
        toast.hide();
        return;
    }

    if (window.UIkit && UIkit.util) {
        toastEl.classList.remove('uk-open');
        toastEl.setAttribute('hidden', 'hidden');
    }
}

function showTabFromTrigger(trigger) {
    if (!trigger) return;

    const targetSelector = trigger.getAttribute('data-bs-target');
    if (!targetSelector) return;

    if (window.bootstrap && bootstrap.Tab) {
        const tab = new bootstrap.Tab(trigger);
        tab.show();
        return;
    }

    const target = document.querySelector(targetSelector);
    if (!target) return;

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tabButton) {
        tabButton.classList.remove('active');
    });

    document.querySelectorAll('.tab-pane').forEach(function (pane) {
        pane.classList.remove('show', 'active');
    });

    trigger.classList.add('active');
    target.classList.add('show', 'active');
}

$(document).ready(function () {
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
    $('#modalTasa').on('show.bs.modal', function () {
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
            const modal = this.closest('.modal');
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