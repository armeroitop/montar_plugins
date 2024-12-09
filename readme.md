
# Proceso de montaje de Moodle y plugins desde CSV

Este script de PHP permite procesar un archivo `.csv` con dos columnas: 

1. **Ruta relativa de destino**: Raiz en Moodle donde el plugin debe copiarse. 
2. **Nombre parcial del plugin**: Nombre del plugin. Se usará para buscar el plugin en un directorio concreto.

El script busca la carpeta correspondiente al plugin (por coincidencia parcial en el nombre) y copia **todos los archivos y subdirectorios de la carpeta raíz del plugin**, junto con los archivos contenidos en sus subdirectorios, al directorio de destino especificado.

## Requisitos

- PHP 7.4 o superior.
- Un archivo CSV con el formato descrito en la sección **Uso**.
- Directorios de origen y destino accesibles en el sistema de archivos.

## Uso

### 1. Preparar el archivo CSV

Crea un archivo `.csv` donde cada línea tenga el siguiente formato:

```csv
ruta/relativa/destino,nombre_parcial_plugin
otra/ruta/destino,otro_plugin
```

- La primera columna especifica la **ruta relativa de destino** donde se copiarán los archivos.
- La segunda columna contiene un **nombre parcial** del plugin que debe buscarse en el directorio de plugins.

Ejemplo:

```csv
course/format/remuiformat,format_remuiformat
mod/questionnaire,mod_questionnaire
```

### 2. Ejecutar el script

Ejecuta el script desde la línea de comandos con la siguiente sintaxis:

```bash
php montar_plugins.php <ruta_al_csv> <ruta_a_plugins> <ruta_a_destino>
```

- `<ruta_al_csv>`: Ruta al archivo `.csv` con las instrucciones.
- `<ruta_a_plugins>`: Ruta al directorio donde se encuentran las carpetas de los plugins.
- `<ruta_a_destino>`: Ruta raíz donde se copiarán los archivos.

#### Ejemplo de ejecución:

```bash
php montar_plugins.php listado_plugins.csv /home/usuario/plugins /www/var/html/moodle405
```

En este ejemplo:
- El archivo CSV está en el directorio actual ` . `.
- El directorio de plugins está en `/home/usuario/plugins`.
- Los archivos se copiarán bajo `/www/var/html/moodle405`.

### 3. Resultado

Por cada línea en el archivo CSV:
1. El script buscará una carpeta en el directorio de plugins cuyo nombre contenga el texto especificado en la segunda columna.
2. Copiará **todos los archivos de la raíz del directorio del plugin y sus subdirectorios** al destino indicado en la primera columna del CSV.
3. Los directorios preexistentes en el destino no serán sobrescritos, los archivos si que se sobreescribiran.

## Estructura resultante

```
moodle405/
├── mod/               # Directorio de plugins de origen
│   ├── questionnaire/
│   │   ├── archivo1.txt
│   │   ├── archivo2.txt
│   │   └── subcarpeta_a/
│   │       ├── archivo3.txt
│   │       └── ...
│   └── otro_plugin/
│       ├── archivo4.txt
│       └── subcarpeta_b/
├── config.php              
├── version.php            

```

Después de ejecutar el script, todos los archivos en `plugin_de_ejemplo` y `otro_plugin`, incluyendo archivos en la raíz y en subcarpetas, se copiarán a las rutas correspondientes bajo `destinos`.

## Notas

1. Si no se encuentra un plugin cuyo nombre coincida con el indicado en el CSV, el script lo indicará en la salida estándar.
2. El script copia los archivos de la raíz del plugin y sus subdirectorios.
3. Los archivos existentes en el destino no serán sobrescritos.
4. El script crea las carpetas de destino si no existen.
5. Las carpetas `.git` y su contenido son ignorados automáticamente.

## Autor

David Gerardo Martínez Armero  
**Email**: armeroitop@gmail.com  



