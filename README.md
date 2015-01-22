# AutoViewComposers
### For Laravel 4 
A Laravel package for conveniently binding view composer classes to their respective views.

### How to install:

Add

	"mamift/auto-viewcomposers":"dev-master"
	
to your composer.json. Then run **"composer update"**. Then add the following to your config/app.php file:

	"Mamift\AutoViewcomposers\AutoViewComposersServiceProvider"

After this, use the php artisan command:

	php artisan config:publish mamift/auto-viewcomposers

This will publish a config.php file under **app/config/packages/mamift/auto-viewcomposers**. Edit the **root_namespace** configuration setting; change the array field as follows:
	    
	'root_namespace' => 'AppNamespace\ViewComposers',
	    
The service provider class will register all view composer classes declared under this namespace.

By default, views are searched for under *app\_path() . '/views/'* and view composer classes are searched under *app\_path() . '/composers/'*. You can change that inside the config.php file.

	'views_path'          => app_path() . '/views/',
    'view_composers_path' => app_path() . '/composers/',

### Notes

- Does not support function view composers.
- Only supports class based view composers.
- Views inside folders are enumerated as 'folder.viewname', so your view composer class can be either:
	- Inside another folder with the same name, or have the class name as Folder.Viewname like **"class Admin.Index {}".**