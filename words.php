<?php
/*
 * Plugin Name: Tarea Pluggin Damian
 * Plugin URL: http..
 * Descripcion:
 * Autor:
 * Version: 1.0
 * Autor URL
 */
/**
 * @author acastineiraduran
 * @version 1.0S
 */

/**
 * Cambia las palabras malsonantes del arreglo de <code>$soez</code> por palabras aptas
 * para el público del arreglo de <code>$eufemismo</code> .
 */

$soez = array("puta", "maricon", "gilipollas", "joder", "ostia");
$eufemismo = array("prostituta", "homosexual", "idiota", "diablos", "cuerpo de cristo");
/*
function renym_wordpress_typo_fix( $text ) {
    global $soez, $eufemismo;
    return str_ireplace( $soez, $eufemismo, $text );
}

add_filter( 'the_content', 'renym_wordpress_typo_fix' );
*/

/**
 * Creamos una tabla en la base de datos
 * con 2 columnas para almacenar los datos
 *
 * @return void
 */
function createTable() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    // le añado el prefijo a la tabla
    $table_name = $wpdb->prefix . 'malsonantes';
    // creamos la sentencia sql
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        palabrota varchar(255) NOT NULL,
        eufemismo varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    // incluir el fichero para poder ejecutar el dbDelta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'plugins_loaded', 'createTable' );

/**
 * Insertar registro en la tabla
 *
 * @return void
 */
function insertRow() {
    global $wpdb, $soez, $eufemismo;
    $table_name = $wpdb->prefix . 'malsonantes';
    $flag = $wpdb->get_results("SELECT * FROM $table_name");
    if (count($flag)==0){
        for ($i = 0; $i < count($soez); $i++){
            $wpdb->insert(
                $table_name,
                array(
                    'palabrota' => $soez[$i],
                    'eufemismo' => $eufemismo[$i]
                )
            );
        }
    }
}
add_action( 'plugins_loaded', 'insertRow');


/**
 * Ver registros de la tabla de datos
 *
 * @return array|object|stdClass[]|null
 */

function selectData(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'malsonantes';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    return $results;
}

/**
 * Recorremos los registros
 * proporcionados por <code>selectData()</code>
 * para almacenarlos en dos
 * arreglos distintos.
 *
 * @param $text
 * @return array|string|string[]
 */

function renym_wordpress_typo_fix( $text ) {
    $malsonantes = selectData();
    foreach ($malsonantes as $result){
        $soez[] = $result->palabrota;
        $eufemismo[] = $result->eufemismo;
    }
    return str_ireplace($soez, $eufemismo, $text);
}

add_filter( 'the_content', 'renym_wordpress_typo_fix' );
