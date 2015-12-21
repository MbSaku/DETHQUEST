<?php
//Site backend - mainframe
define('General', "General");
define('Languages', "Idiomas");
define('Modules', "Módulos");
define('Permissions', "Permisos");
define('Appearance', "Temas");
define('Overwatch', "Registros de actividad");
define('You_must_create_the_database_and_permissions', "Es necesario que crees una base de datos vacía nombrada");
//General
define('General_configuration_help', "Aquí puedes configurar la información principal, meta tags, registro abierto de usurios y mantenimiento del sitio.");
define('Metatags', "Meta tags (dejar en blanco para eliminar):");
define('Metatagname', "Variable");
define('Metatagdesc', "Valor");
define('Metatagadd', "Añadir meta");
define('Maintenance_mode', "Modo mantenimiento (solo puede loguearse el administrador y ver los contenidos)");
define('Allow_free_registry', "Permitir registro libre de usuarios (los registrados tomarán automáticamente el cargo de mayor nivel definido)");
define('Save_settings', "Guardar");
define('Site_configuration_saved', "Se ha guardado la configuración del sitio");
define('Meta_variables_not_saved', "No se pudo guardar la meta información");
define('Site_configuration_not_saved', "La configuración no se pudo actualizar");
//Languages
define('Language_configuration_help', "Aquí puedes configurar los idiomas disponibles para tu sitio web. Puedes forzar uno de ellos a que se muestre por defecto.");
define('Available_languages', 'Idiomas disponibles:');
define('Language_reference', 'Ref');
define('Language_name', 'Nombre');
define('Add_new_language', "Añadir");
define('Force_display', 'Forzar por defecto');
define('Save_language', "Guardar");
define('Delete_language', "Borrar");
define('Language_not_saved', "El idioma no se guardó correctamente");
define('Language_saved', "Idioma guardado");
define('Language_deleted', "Idioma borrado");
define('Language_could_not_be_deleted', "Se produjo un error borrando el idioma");
//Modules
define('Modules_help', "Aquí puedes configurar, instalar y desinstalar los módulos que añadirán funcionalidad a tu sitio web. También puedes editar su nombre en el idioma activo para traducirlo.");
define('Active_language', "Idioma activo:");
define('Activated_modules', "Módulos activados:");
define('Installed_modules', "Módulos instalados:");
define('Module_name', "Nombre:");
define('Module_url', "Url:");
define('Module_shortcut', "Acceso directo");
define('Module_active', "Activado");
define('Save_module', "Guardar");
define('Uninstall_module', "Desinstalar");
define('Module_configuration_not_saved', "La configuración del módulo no pudo guardarse.");
define('Module_not_translated', "El módulo no pudo traducirse.");
define('Module_data_saved', "Datos de módulo guardados.");
define('New_module', "Instalar un nuevo módulo");
define('Upload_zip_module_files', "Subir fichero zip:");
define('Upload_module_file', "Subir");
define('Zip_module_format_not_correct', "Formato interno incorrecto");
define('Module_not_installed', "El módulo no se pudo instalar correctamente");
define('Module_installed', "Módulo instalado");
define('You_will_delete_this_module_are_you_sure', "Estás a punto de eliminar este módulo. Esta acción es definitiva y no se podrá deshacer. ¿Estás seguro?");
define('Yes_delete_module', "Si");
define('No_delete_module', "No, déjalo como está");
define('Module_deleted', "Módulo desinstalado");
define('Module_was_not_deleted', "Se produjo un error eliminando los datos");
define('Move_up', "Arriba");
define('Move_down', "Abajo");
//Permissions
define('Permission_help', "Aquí puedes configurar los permisos internos de acceso a usuarios registrados según cargo. No es posible editar los permisos administrativos.");
define('Module_charge', "Módulo / Cargo");
define('Rebuild_permissions', "Reconstruir permisos");
define('Operation_not_permitted', "Operación no permitida");
define('Permission_setting_error', 'Error introduciendo permiso: ');
define('Permissions_rebuilt', 'Permisos reconstruidos');
//Appearance
define('Appearance_help', "Aquí puedes subir, activar y eliminar temas estéticos para tu sitio.");
define('Active_theme', "Tema activo:");
define('Available_themes', "Temas instalados:");
define('Activate_theme', "Activar");
define('Delete_theme', "Desinstalar");
define('New_theme', "Instalar un tema nuevo");
define('Upload_zip_theme_files', "Subir fichero zip:");
define('Upload_zip_file', "Subir");
define('Zip_theme_format_not_correct', "Formato interno incorrecto");
define('Theme_installed', "Tema instalado");
define('Theme_not_installed', "El tema no se pudo instalar correctamente");
define('Theme_activated', "Tema aplicado. Probablemente tengas que refrescar la página");
define('You_will_delete_this_theme_are_you_sure', "Estás a punto de desinstalar este tema. Esta acción es definitiva y no se puede deshacer. ¿Estás seguro?");
define('Yes_delete_theme', "Si");
define('No_delete_theme', "No, dejarlo como está");
define('Theme_deleted', "Tema desinstalado");
define('Theme_was_not_deleted', "Se produjo un error eliminando el tema");
//Overwatch
define('Date_from', "Desde:");
define('Date_to', "Hasta:");
define('Ip_filter', "I.P.:");
define('All_ips', "Cualquiera");
define('Username_filter', "Usuario:");
define('All_users', "Cualquiera");
define('View_log', "Ver registro");
define('Search_filter', "Buscar");
?>