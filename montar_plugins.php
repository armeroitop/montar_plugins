<?php

if ($argc < 4) {
    echo "Uso: php process_plugins.php <ruta_al_csv> <ruta_a_plugins> <ruta_a_destino>\n";
    exit(1);
}

$csvFile = $argv[1];
define('PLUGIN_DIR', $argv[2]);
define('DESTINATION_ROOT', $argv[3]);

// Códigos ANSI para colores
define('COLOR_VERDE', "\033[42;30m"); // Fondo verde, texto blanco
define('COLOR_ROJO',  "\033[41;37m"); 
define('COLOR_RESET', "\033[0m");    // Restablecer colores

// Función para buscar un directorio que contenga un nombre parcial
function findPluginFolder($pluginName)
{
    $directories = scandir(PLUGIN_DIR);

    foreach ($directories as $directory) {
        if (strpos($directory, $pluginName) !== false && is_dir(PLUGIN_DIR . '/' . $directory)) {
            return $directory;
        }
    }

    return null; // Si no encuentra nada
}

// Función para copiar directorios recursivamente, ignorando cualquier .git y su contenido
function copyDirectory($source, $destination)
{
    if (!is_dir($source)) {
        return false;
    }

    // Evitar crear carpetas .git
    if (basename($destination) === '.git') {
        echo "Ignorando la creación de carpeta: $destination\n";
        return false;
    }

      // Crear el directorio solo si no existe
    if (!is_dir($destination)) {
        @mkdir($destination, 0777, true);
    }

    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..' || $file === '.git') {
            continue; // Ignorar directorios especiales
        }

        $srcPath = $source . '/' . $file;
        $destPath = $destination . '/' . $file;

        // Ignorar cualquier carpeta .git o archivos relacionados
        if ($file === '.git' || strpos($srcPath, '/.git') !== false || strpos($file, '.git') !== false) {
            echo "Ignorando: $srcPath\n";
            continue;
        }

        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $destPath);
        } else {
            copy($srcPath, $destPath);
        }
    }

    closedir($dir);
    return true;
}

// Verificar si el archivo CSV fue pasado como argumento
if (!file_exists($csvFile)) {
    echo "El archivo CSV especificado no existe: $csvFile\n";
    exit(1);
}

// Leer y procesar el archivo CSV
if (($handle = fopen($csvFile, 'r')) !== false) {
    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) < 2) {
            echo "Línea inválida en el CSV, se requieren dos columnas.\n";
            continue;
        }

        [$relativeDestinationPath, $pluginName] = $data;

        $pluginFolder = findPluginFolder($pluginName);

        if (!$pluginFolder) {
            echo "No se encontró un plugin que contenga: $pluginName\n";
            continue;
        }

        $pluginPath = PLUGIN_DIR . '/' . $pluginFolder;

        // Copiar archivos de la raíz del plugin al destino
        $fullDestinationPath = DESTINATION_ROOT . '/' . $relativeDestinationPath;

        if (copyDirectory($pluginPath, $fullDestinationPath)) {
            echo COLOR_VERDE . "Añadido el contenido de $pluginFolder a $fullDestinationPath" . COLOR_RESET . "\n";
        } else {
            echo COLOR_ROJO . "Error al copiar el contenido de $pluginFolder". COLOR_RESET . "\n";
        }
    }

    fclose($handle);
} else {
    echo "No se pudo abrir el archivo CSV.\n";
}
