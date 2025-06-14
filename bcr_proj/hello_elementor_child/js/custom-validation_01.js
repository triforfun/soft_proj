document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.querySelector('input[name="start-date"]');
    const endDateInput = document.querySelector('input[name="end-date"]');
    let holidays = [];

    // Cargar días festivos desde el archivo JSON
    fetch(ajax_object.holidays_url)
        .then(response => response.json())
        .then(data => {
            holidays = data;
        })
        .catch(error => console.error('Error al cargar las fechas festivas:', error));

    function validateStartDate() {
        const startDate = new Date(startDateInput.value);
        let errorMessage = '';

        // Validar que la fecha de inicio no sea domingo
        if (startDate.getDay() === 0) {
            errorMessage += 'La fecha de inicio no puede ser domingo.\n';
        }

        // Validar que la fecha de inicio no sea un día festivo
        const startDateStr = startDate.toISOString().split('T')[0];
        if (holidays.includes(startDateStr)) {
            errorMessage += 'La fecha de inicio no puede ser un día festivo.\n';
        }

        if (errorMessage) {
            alert(errorMessage);
        }
    }

    function validateEndDate() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        let errorMessage = '';

        // Validar que la fecha de fin sea posterior a la de inicio
        if (endDate <= startDate) {
            errorMessage += 'La fecha de fin debe ser superior a la fecha de inicio.\n';
        }

        // Validar que la fecha de fin no sea domingo
        if (endDate.getDay() === 0) {
            errorMessage += 'La fecha de fin no puede ser domingo.\n';
        }

        // Validar que la fecha de fin no sea un día festivo
        const endDateStr = endDate.toISOString().split('T')[0];
        if (holidays.includes(endDateStr)) {
            errorMessage += 'La fecha de fin no puede ser un día festivo.\n';
        }

        if (errorMessage) {
            alert(errorMessage);
        }
    }

    startDateInput.addEventListener('change', validateStartDate);
    endDateInput.addEventListener('change', validateEndDate);
});
