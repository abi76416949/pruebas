## Bienvedido al repositorio de forcefit 🏋️‍
![](https://github.com/temolzin/forcefit/blob/master/public/img/forcefit.png)

## Forcefit
Sistema para la administración y gestón de gimnasios, que ayuda al gimnasio a tener un control de usuario y de esta manera ofrecer servicios de alta calidad y una mejor experiencia a los usuarios.

## Preview
![](https://github.com/temolzin/forcefit/blob/master/public/img/preview1.png)
![](https://github.com/temolzin/forcefit/blob/master/public/img/preview2.png)

### requisitos previos a la instalacion📋
Para ejecutar este proyecto necesitas.

- Servidor PHP(xampp, wamp, lamp)
- Editor de texto **Visual Studio Code** (opcional)

### Instalalación 🔧💻
- En **GitHub.com**, ir a la página principal del repositorio forcefit  [Link](https://github.com/temolzin/forcefit/).
-  En la lista de la parte superior selecciona **code** [Clonar repositorio](https://github.com/temolzin/forcefit/)
- Para clonar el repositorio copeas el link.
- Abres la consola y selecciona la ubicación donde se clonara y ejecutas el siguiente comando.
``
git clone https://github.com/temolzin/forcefit.git
``
- Después lo abres en tu editor de texto preferido.
- Para ejecutarlo inicializas el servidor, si tienes **xampp** activas apache y MySQL
- Para la instalación de la Base de Datos encontrarás una carpeta con el nombre de **database** la cual contiene el archivo con la base de datos.
- Posteriormente instalas la base de datos.
- Para que el proyecto funcione correctamente ejecuta este comando en la carpeta raíz **composer install**

### Instalación de la  librería phinx📋
- Ejecuta este comando **composer.phar require robmorgan/phinx**

### Descargar Migraciones
- Ejecuta este comando **composer phinx-migrate** Para descargar las migraciones
### Descargar seeders
- Ejecuta el siguiente comando **composer phinx-seed-run** Para descargar los seders

- Si quieres agregar mas seders o actualizar puedes editar el Scrips que se encuentra en composer.json para que se ejecute el seder que creaste o actualizaste.
- En el archivo composer.json se encuentran los scrips para las migraciones.
- Estos son link para mayor información [Link](https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method/).
[Link](https://book.cakephp.org/phinx/0/en/seeding.html/).

### End
