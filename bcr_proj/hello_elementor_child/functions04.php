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
function get_available_bikes($size, $start_date, $end_date) {
    $bikes = json_decode(file_get_contents(get_stylesheet_directory() . '/bikes.json'), true);
    $reservations = json_decode(file_get_contents(get_stylesheet_directory() . '/reservations.json'), true);

    // Filtrar bicicletas por talla, tipo y disponibilidad
    $filtered_bikes = array_filter($bikes, function($bike) use ($size) {
        return $bike['size'] === $size && $bike['available'] === true && $bike['stock'] > 0;
    });

    // Reducir el stock según reservas
    foreach ($filtered_bikes as &$bike) {
        $bike_stock = $bike['stock'];
        foreach ($reservations as $reservation) {
            if (
                $reservation['bike_id'] == $bike['id'] &&
                strtotime($reservation['start_date']) <= strtotime($end_date) &&
                strtotime($reservation['end_date']) >= strtotime($start_date)
            ) {
                $bike_stock--;
            }
        }
        $bike['available_stock'] = $bike_stock; // Actualizar stock disponible
    }

    // Filtrar por stock disponible
    return array_filter($filtered_bikes, function($bike) {
        return $bike['available_stock'] > 0;
    });
}
// Definir la función get_bike_size antes de su uso
function get_bike_size($height) {
    $sizes = [
        49 => [150, 160],
        52 => [161, 165],
        54 => [166, 170],
        56 => [171, 180],
        58 => [181, 190],
        61 => [191, 200]
    ];

    foreach ($sizes as $size => $range) {
        if ($height >= $range[0] && $height <= $range[1]) {
            return $size;
        }
    }
    return null; // Si no encaja en ninguna talla
}

// Luego, asegúrate de que el código para la validación de reservas esté aquí

add_action('wpcf7_before_send_mail', 'custom_bike_reservation_validation');

function custom_bike_reservation_validation($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $data = $submission->get_posted_data();
        $height = intval($data['your-height']);
        $start_date = $data['start-date'];
        $end_date = $data['end-date'];

        // Depuración
        error_log("Start date: $start_date, End date: $end_date, Height: $height");

        // Calcular talla
        $size = get_bike_size($height);
        error_log("Bike size: $size");

        // Obtener bicicletas disponibles
        $available_bikes = get_available_bikes($size, $start_date, $end_date);
        error_log("Available bikes: " . count($available_bikes));

        if (empty($available_bikes)) {
            $submission->set_status('validation_failed');
            $submission->add_error('bike-type', 'No hay bicicletas disponibles para las fechas seleccionadas.');
        } else {
            // Seleccionar la primera bicicleta disponible
            $selected_bike = reset($available_bikes);

            // Guardar la reserva
            save_reservation($selected_bike['id'], $start_date, $end_date);

            // Opcional: Añadir mensaje personalizado
            $submission->set_status('completed'); // Esto marca el formulario como completado
            $submission->add_message(
                'Reserva confirmada. Bicicleta asignada: ' . $selected_bike['type'] . ' talla ' . $selected_bike['size'] .
                ' para las fechas del ' . $start_date . ' al ' . $end_date . '.'
            );

        }
    }
}

function save_reservation($bike_id, $start_date, $end_date) {
    $reservations_file = get_stylesheet_directory() . '/reservations.json';
    $reservations = json_decode(file_get_contents($reservations_file), true);

    $reservations[] = [
        'bike_id' => $bike_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ];

    file_put_contents($reservations_file, json_encode($reservations, JSON_PRETTY_PRINT));
}

