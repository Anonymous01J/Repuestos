$(document).ready(function () {
    const dolarBCV = async () => {
        let search = await fetch("https://ve.dolarapi.com/v1/dolares")
        let response = await search.json()
        document.getElementById("Dolar_bcv").textContent = parseFloat(response[0].promedio).toFixed(2) + " Bs"
        console.log(response[0].promedio);
    }
    dolarBCV();
    $('.table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});