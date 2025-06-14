<?php
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

function custom_cf7_real_time_validation_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var startDateInput = document.querySelector('input[name="start-date"]');
        var endDateInput = document.querySelector('input[name="end-date"]');
        var holidays = ['2024-05-01', '2024-12-25', '2025-05-01', '2025-12-25'];

        function validateStartDate() {
            var startDate = new Date(startDateInput.value);
            var errorMessage = '';

            // Validar que la fecha de inicio no sea domingo
            if (startDate.getDay() === 0) {
                errorMessage += 'La fecha de inicio no puede ser domingo.\n';
            }

            // Validar que la fecha de inicio no sea un día festivo
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

            // Validar que la fecha de fin sea superior a la de inicio
            if (endDate <= startDate) {
                errorMessage += 'La fecha de fin debe ser superior a la fecha de inicio.\n';
            }

            // Validar que la fecha de fin no sea domingo
            if (endDate.getDay() === 0) {
                errorMessage += 'La fecha de fin no puede ser domingo.\n';
            }

            // Validar que la fecha de fin no sea un día festivo
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
