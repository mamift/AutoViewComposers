<?php namespace Mamift\AutoViewcomposers;

use Illuminate\Support\ServiceProvider;
use Mamift\AutoViewcomposers\NamespaceClassFinder;

class AutoViewcomposersServiceProvider extends ServiceProvider {

	/**
	 * The root namespace all your view composers classes are set under.
	 * 
	 * @var string
	 */
    private $root_namespace;
    
    /**
     * The absolute path to the views directory.
     * 
     * @var string
     */
    private $views_path;
    
    /**
     * The absolute path to the the view composer's directory.
     * 	
     * @var string
     */
    private $view_composers_path;


	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Sets the directory path to Laravel's views. Relativ to $app_directory (i.e. wherever app/ is located).
     * 
     * @var string (has default value of '/views/')
     */
    public function set_view_directory($directory_string = '/views/') 
    {
    	if (!isset($this->views_path)) {
    		$this->views_path = $directory_string;
    	}
    }

    /**
     * Constructor; gets the app's views and view_composer path and app object
     */
    public function __construct($app) 
    {
        $this->app = $app;
    }

	/**
     * Returns an array of laravel views. Views inside subfolders are written as 'directory.viewfilename'
     * @param  $dir: directory to scan
     * @param  $filter: string filter to use - will only return files of a certain extension; get's fed into strrpos(); if using blade, use 'blade.php' or if using another template engine like smarty, use '.tpl'
     * @return an array representing the list of views
     */
    public function get_laravel_views($dir, $filter = '.') 
    {
        // $dir_handle = opendir($dir);
        // dblog($dir);
        // dblog($filter);
        if (!isset($dir)) $dir = $this->views_path or app_path() . '/views/';
        
        // array of views to return
        $array = [];

        // depth first search of views
        foreach(scandir($dir) as $file) {
            $not_current_dir = ($file != '.');
            $not_parent_dir = ($file != '..');
            
            if (is_dir($dir . $file) && $not_current_dir && $not_parent_dir) {
                // scan the subdirectory
                $subdir = get_laravel_views($dir . $file . '/', $filter); 
                // $subdir_size = count($subdir);

                foreach ($subdir as $subfile) {
                    $array[] = $file . '.' . $subfile;
                }

            // filter only .tpl files
            } elseif ($file != '.' && $file != '..' && strrpos($file, $filter))  {
                // echo $file . ', ';
                $file = str_replace('_', '-', $file);
                if (!strpos($file, "_") == 1) {
                    $array[] = str_replace($filter, '', $file);
                }
            }
        } 

        return $array;
    }

	/**
     * Recursive directory scan for class-based (not closure) view composer files under app_path() . '/composers/''
     * @param  $dir: directory to scan
     * @param  $filter: string represnting a filter to use: using composer.php will list only files that have .composer.php in the file name.
     * @return an array representing the file and folder structure
     */
    public function get_laravel_view_composers($dir, $filter = '.php') 
    {
        if (!isset($dir)) $dir = $this->view_composers_path or app_path() . '/composers/';
        
        // array of view composers to return
        $array = [];

        foreach(scandir($dir) as $file) {
            if (is_dir($dir . $file) && $file != '.' && $file != '..') {
                // scan the subdirectory
                $subdir = $this->get_laravel_view_composers($dir . $file . '/', $filter); 
                $subdir_size = count($subdir);

                foreach ($subdir as $subfile) {
                    $array[] = $subfile;
                }

                // $array[] = "$file" . '.' . $subdir[$subdir_size-1];

            // filter only .tpl files
            } elseif ($file != '.' && $file != '..' && strrpos($file, $filter))  {
                // echo $file . ', ';
                // $file = str_replace('_', '-', $file);
                if (!strpos($file, "_") == 1) {
                    $array[] = str_replace($filter, '', $file);
                }
            }
        } 

        return $array;
    }

	/**
     * Get all the classes declared under the root namespace
     * 
     * @return array: returns an array of class names (not include full namespace path)
     */
    public function get_classes_under_root_namespace() 
    {
        $class_finder = new NamespaceClassFinder(); 
        $classes = $class_finder->getClassesOfNameSpace($this->root_namespace);

        // because $class_finder will return root_namespace + class name, we will need to trim the namespace off for comparison against it's corresponding laravel view name
        for ($i = 0; $i < count($classes); $i++) {
            $collapsed_array = explode('\\', $classes[$i]);
            $last = $collapsed_array[count($collapsed_array)-1];

            $classes[$i] = $last;
        }

        return $classes;
    }

    /**
     * Registers view composers with their respective views.
     */
    private function register_composers_to_views() 
    {
    	$views = $this->get_laravel_views($this->views_path, '.tpl');
        $view_composers = $this->get_laravel_view_composers($this->view_composers_path, '.composer.php');
        
        // $view_composers = $this->get_classes_under_root_namespace();
        // $class_finder = new NamespaceClassFinder(); 
        // $view_composers = $class_finder->getClassesOfNameSpace($this->root_namespace);

        for ($i = 0; $i < count($views); $i++) {
            $views[$i] = strtolower($views[$i]);
        }

        for ($i = 0; $i < count($view_composers); $i++) {
            $view_composers[$i] = strtolower($view_composers[$i]);
        }

        // echo var_dump($views);
        // echo var_dump($view_composers);
        // echo var_dump(get_namespaces());

        // auto register any classes under the root_namespace
        foreach ($view_composers as $comp) {
            $composer_class = $this->root_namespace . "\\" . ucwords($comp);

            for ($i = 0; $i < count($views); $i++) {
                $view = $views[$i];

                // echo ' View: ' . $view . ', ';
                // echo 'Composer: ' . $composer_class . ', <br/>';

                if (strcmp($view, $comp) == 0) {
                    // echo 'View: ' . $view . ', ';
                    // echo 'Composer: ' . $composer_class . ', <br />';
                    
                    $this->app->view->composer($view, $composer_class);
                    $i = count($views);
                    break;
                }
            }
        }

        // $this->app->view->composer('masterlayout', 'Mamift\ViewComposers\Masterlayout');
    }
    
    /**
     * Boot the service provider. Called before a request is routed.
     */
    public function boot() 
    {
        $this->package('mamift/autoViewcomposers');
        $this->register_composers_to_views();
        // echo var_dump($this->app['config']);
    }

	/**
	 * Register the service provider. Called immediately when the service provider is registered.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->package('mamift/autoViewcomposers');

        $config = $this->app['config'];
        $config_key = "autoViewcomposers::";

        // echo var_dump ($config);
        // the root namespace of all your view composer classes
        $this->root_namespace = $config->get($config_key . "root_namespace");
        
        // sets the location of laravel's views
        $this->views_path = $config->get($config_key . "views_path");
        // sets the location of the composers
        $this->view_composers_path = $config->get($config_key . "view_composers_path");

        // register composeres to views
		$this->register_composers_to_views();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
