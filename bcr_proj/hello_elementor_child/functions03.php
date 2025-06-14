<?php
// Salir si se accede directamente
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - No modificar ni eliminar los comentarios anteriores o posteriores

if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')) :
    function child_theme_configurator_css() {
        wp_enqueue_style(
            'chld_thm_cfg_child',
            trailingslashit(get_stylesheet_directory_uri()) . 'style.css',
            array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style', 'hello-elementor-header-footer')
        );
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 20);
// END ENQUEUE PARENT ACTION

// Encolar el script de validación personalizado y definir ajax_object
function enqueue_custom_validation_script() {
    wp_enqueue_script(
        'custom-validation',
        get_stylesheet_directory_uri() . '/js/custom-validation.js', // Ruta del archivo JS
        array('jquery'), // Dependencias (opcional)
        null, // Versión (null para evitar la caché durante desarrollo)
        true // Cargar en el footer
    );

    // Pasar la URL del archivo holidays.json al script
    wp_localize_script('custom-validation', 'ajax_object', array(
        'holidays_url' => get_stylesheet_directory_uri() . '/holidays.json' // Ruta del archivo JSON
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_validation_script');

// Script de validación en tiempo real para Contact Form 7 (versión básica)
function custom_cf7_real_time_validation_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var startDateInput = document.querySelector('input[name="start-date"]');
        var endDateInput = document.querySelector('input[name="end-date"]');
        if (!startDateInput || !endDateInput) return; // Salir si los campos no existen

        startDateInput.addEventListener('change', function () {
            if (new Date(startDateInput.value).getDay() === 0) {
                alert('La fecha de inicio no puede ser domingo.');
                startDateInput.value = '';
            }
        });

        endDateInput.addEventListener('change', function () {
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);

            if (endDate <= startDate) {
                alert('La fecha de fin debe ser superior a la fecha de inicio.');
                endDateInput.value = '';
            } else if (endDate.getDay() === 0) {
                alert('La fecha de fin no puede ser domingo.');
                endDateInput.value = '';
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_real_time_validation_script');
