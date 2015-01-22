# AutoViewComposers
### For Laravel 4 
A Laravle package for conveniently binding view composer classes to their respective views.

How to install:

	"mamift/auto-viewcomposers":"dev-master"
	
to your composer.json. Then add the following to your config/app.php file.

	"Mamift\AutoViewcomposers\AutoViewComposersServiceProvider"

After this, use the php artisan command:

	php artisan config:publish mamift/auto-viewcomposers

This will publish a config.php file under **app/config/packages/mamift/auto-viewcomposers**. 

The service provider class will do all the work when the package is registered. By default, views are searched for under *app\_path() . '/views/'* and view composer classes are searched under *app\_path() . '/composers/'*. You can change that inside the config.php file.

### Notes

- Does not support function view composers.
- Only supports class based view composers.
- Views inside folders are enumerated as 'folder.viewname', so your view composer class can be either:
	- Inside another folder with the same name, or have the class name as Folder.Viewname like **"class Admin.Index {}".**