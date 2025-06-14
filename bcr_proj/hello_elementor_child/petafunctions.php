// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style', 'hello-elementor-header-footer'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 20);

// END ENQUEUE PARENT ACTION

// Función para cargar las fechas festivas desde el archivo JSON
function load_holidays() {
    $holidays_file = get_stylesheet_directory() . '/holidays.json';
    if (file_exists($holidays_file)) {
        $holidays_json = file_get_contents($holidays_file);
        $holidays = json_decode($holidays_json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $holidays;
        } else {
            error_log('Error al decodificar JSON: ' . json_last_error_msg());
        }
    } else {
        error_log('Archivo holidays.json no encontrado');
    }
    return [];
}

// Función de validación en tiempo real
function custom_cf7_real_time_validation_script() {
    $holidays = json_encode(load_holidays());
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var startDateInput = document.querySelector('input[name="start-date"]');
        var endDateInput = document.querySelector('input[name="end-date"]');
        var holidays = <?php echo $holidays; ?>;
        console.log('Fechas festivas cargadas:', holidays);

        function isHoliday(date) {
            var dateStr = date.toISOString().split('T')[0];
            return holidays.includes(dateStr);
        }

        function validateStartDate() {
            var startDate = new Date(startDateInput.value);
            var errorMessage = '';

            // Validar que la fecha de inicio no sea domingo
            if (startDate.getDay() === 0) {
                errorMessage += 'La fecha de inicio no puede ser domingo.\n';
            }

            // Validar que la fecha de inicio no sea un día festivo
            if (isHoliday(startDate)) {
                errorMessage += 'La fecha de inicio no puede ser un día festivo.\n';
            }

            if (errorMessage) {
                alert(errorMessage);
                startDateInput.value = ''; // Resetear el campo de fecha
            }
        }

        function validateEndDate() {
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var errorMessage = '';

            // Validar que la fecha de fin sea superior a la de inicio
            if (endDate <= startDate) {
                errorMessage += 'La fecha de fin debe ser superior a la fecha de inicio.\n';
            }

            // Validar que la fecha de fin no sea domingo
            if (endDate.getDay() === 0) {
                errorMessage += 'La fecha de fin no puede ser domingo.\n';
            }

            // Validar que la fecha de fin no sea un día festivo
            if (isHoliday(endDate)) {
                errorMessage += 'La fecha de fin no puede ser un día festivo.\n';
            }

            if (errorMessage) {
                alert(errorMessage);
                endDateInput.value = ''; // Resetear el campo de fecha
            }
        }

        startDateInput.addEventListener('change', validateStartDate);
        endDateInput.addEventListener('change', validateEndDate);
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_real_time_validation_script');
