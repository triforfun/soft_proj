<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
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

// Validación en tiempo real para Contact Form 7 (mantén esta parte si ya la usas)
function custom_cf7_real_time_validation_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var startDateInput = document.querySelector('input[name="start-date"]');
        var endDateInput = document.querySelector('input[name="end-date"]');
        var holidays = ['2024-05-01', '2024-12-25', '2025-05-01', '2025-12-25']; // Puedes eliminar esta línea después de implementar el archivo JSON

        function validateStartDate() {
            var startDate = new Date(startDateInput.value);
            var errorMessage = '';

            if (startDate.getDay() === 0) {
                errorMessage += 'La fecha de inicio no puede ser domingo.\n';
            }

            var startDateStr = startDate.toISOString().split('T')[0];
            if (holidays.includes(startDateStr)) {
                errorMessage += 'La fecha de inicio no puede ser un día festivo.\n';
            }

            if (errorMessage) {
                alert(errorMessage);
            }
        }

        function validateEndDate() {
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var errorMessage = '';

            if (endDate <= startDate) {
                errorMessage += 'La fecha de fin debe ser superior a la fecha de inicio.\n';
            }

            if (endDate.getDay() === 0) {
                errorMessage += 'La fecha de fin no puede ser domingo.\n';
            }

            var endDateStr = endDate.toISOString().split('T')[0];
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
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_real_time_validation_script');

// NUEVO: Cargar script JS y archivo JSON de fechas festivas
function custom_enqueue_scripts() {
    wp_enqueue_script('custom-validation', get_stylesheet_directory_uri() . '/js/custom-validation.js', array('jquery'), null, true);

    // Pasar la URL del archivo JSON al script
    wp_localize_script('custom-validation', 'ajax_object', array(
        'holidays_url' => get_stylesheet_directory_uri() . '/holidays.json'
    ));
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');
