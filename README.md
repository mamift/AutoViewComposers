# AutoViewComposers
### For Laravel 4 
A Laravle package for conveniently binding view composer classes to their respective views.

How to install:

	"mamift/auto-viewcomposers":"dev-master"
	
to your composer.json. THen add the following to your config/app.php file.

	"Mamift\AutoViewcomposers\AutoViewComposersServiceProvider"

This Service Provider class will do all the work when the package is registered. By default, views are searched for under *app\_path() . '/views/'* and view composer classes are searched under *app\_path() . '/composers/'*. You can change that inside the config.php file.