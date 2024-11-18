como instalarlo:  
descargarlo de este repositorio como archivo comprimido(.rar .zip etc) y descomprimir  (carpeta www en laragon)

Preparar el Entorno en la PC 
En la PC  asegúrate de que:
MySQL esté instalado y un servidor MySQL esté en ejecución (puede usar laragon xammp u otro como servidor local).
en laragon en la configuracion general de la app activar la creacion automatica de host virtuales y  en servicios y puertos activar el ssl 443



como inicializar la base de datos y como acceder en caso de que haya un usuario administrador. 
acceder a la consola de laragon hice uso de estas credenciales de usuario
Iniciar sesion:
mysql -u root -p   
2 enter y entra

Crear la base de datos asistencias en el servidor MySQL  antes de importar
yo la llame asistencias;
CREATE DATABASE asistencias;

luego hacer exit   para poder hacer el  el import  del sql


Importar la Base de Datos en el proyecto
En el proyecto, usa el comando para importar el archivo SQL:
mysql -u root -p asistencias < asistencias.sql
Esto restaurará los datos y la estructura de la base de datos.


Configurar el Proyecto Web
el modelo intentaba ser  mvc por lo que adentro de la carpeta raiz abra otras carpetas para separar la logica de los archivos (el retirar esas carpetas hara que deba cambiar todas las rutas en cada archivo)

Asegúrate de copiar también el proyecto desde C:\laragon\www\ASISTENCIAS a la carpeta raíz del servidor web 
Verifica que el archivo de configuración de conexión (conexion.php) apunte al servidor MySQL correcto.





resumen del sistema:
solo registra alumnos asistencias  notas  y usuarios

muestra gran parte de la informacion guardada
 
 muestra condicion  de final de cursada correctamente

puede hacer todas las funciones de la seccion alumnos

falto modificiar algunas cosas en las asistencias y en las notas

y algunos archivos en el directorio no hacen nada  como el de instituciones





 

