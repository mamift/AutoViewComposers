# AutoViewComposers
### For Laravel 4.2
A Laravel package for conveniently binding view composer classes to their respective views.

### How to install:

Many steps to follow! Make sure you do as is written here!

1. Add the following to your **composer.json** file:

		"mamift/auto-viewcomposers":"dev-master"


2. Run **"composer update"**. Then add the following to your config/app.php file:

		"Mamift\AutoViewcomposers\AutoViewComposersServiceProvider"

	Note: There is no need to add an alias as this service does the work behind the scenes when **package->register()** is invoked. 
	
3. After this, use *php artisan* to publish the **config.php** file for this package:

		php artisan config:publish mamift/auto-viewcomposers

	This will publish a config.php file under **app/config/packages/mamift/auto-viewcomposers**. Edit the configuration settings inside the array to your liking. This one setting must be changed:
	    
		'root_namespace' => 'AppNamespace\ViewComposers',
	    
	Replace 'AppNamespace' with your own namespace; the service provider class will register all view composer classes declared under this namespace.

4. Create a view composer directory.

	Because Laravel 4 does not have a default place to put view composers classes, you must create a directory under your **app_path()** to store them. 

	By default, views are searched for under *app\_path() . '/views/'* and view composer classes are searched under *app\_path() . '/composers/'*. You can change that inside the config.php file.

		'views_path'          => app_path() . '/views/',
	    'view_composers_path' => app_path() . '/composers/',

5. Add the newly created view composer directory into your **composer.json** *autoload { classmap [] }* array:

		"autoload": {
			"classmap": [
				"app/commands",

				"app/composers",
				
				"app/controllers",
				"app/models",
				"app/database/migrations",
				"app/database/seeds",
				"app/tests/TestCase.php"
			]
		},

	This will ensure that any view composer classes get autoloaded under your namespace! 

### How to use (conventions to follow)

After following the steps explained above, you can now start creating view compoer classes and have them automatically bind to their respective views, so long as you follow the following conventions:

- View composer class names must be the same name as their respective view name:
		
		class Index {
			public function compose($view) {
				$view->with('title', "PAGE ONE");
			}
		}
		
		// the above view composer get's bound to:
		
		index.blade.php
		
- View composer file names must end with whatever is specified in your **config.php** (the default value is listed below)
		
	    'view_composer_extension'   => '.composer.php'

- The same goes for your views:
		
		'view_extension'            => '.blade.php'

If you are using a different template system, you can change the above config.php setting to something different, such as:

		'view_extension'            => '.tpl'

Obviously, you need to use a Smarty package that also supports view composers (dark/smarty-view does, some others don't).

### Notes

- Does not support anonymous function view composers.
- Only supports class based view composers (not yet for view creators).
- Views inside folders are enumerated as 'folder.viewname', so your view composer class can be either:
	- Inside another folder with the same name, or have the class name as Folder.Viewname like **"class Admin.Index {}".**
- Not tested in Laravel 5, probably doesn't work.
- The way this service compares view and view composer class names is not case sensitive! i.e. admin and ADMIN are the same!