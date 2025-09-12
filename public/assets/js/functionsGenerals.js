$(document).ready(function () {
    const ahora = new Date();
    const tzOffset = ahora.getTimezoneOffset() * 60000; // en milisegundos
    const localISO = new Date(ahora - tzOffset).toISOString().slice(0, 16);
    $('#fecha').val(localISO);


    $('.table-DT').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/es-ES.json'
        }
    });
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
            $('#modalTasa').modal('hide');
            // Actualiza el valor en el header
            $('#Dolar_bcv').text($('#inputTasa').val() + " Bs");
        });
    });
});