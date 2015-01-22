<?php namespace Mamift\AutoViewcomposers;

/**
 * NamespaceClassFinder: finds all classes inside a given namespace
 * http://stackoverflow.com/questions/22761554/php-get-all-class-names-inside-a-particular-namespace
 */
class NamespaceClassFinder {

    private $namespaceMap = [];
    private $defaultNamespace = 'global';

    /**
     * The the constructor. Invokes $this->traverseClasses();
     */
    public function __construct()
    {
        $this->traverseClasses();
    }

    /**
     * Gets the namespace of the given class via reflection.
     * The global namespace (for example PHP's predefined ones) will be returned 
     * as a string defined as a property ($defaultNamespace) own namespaces will
     * be returned as the namespace itself 
     * @param  [string] $class [The class to reflection]
     * @return [string]        [The namespace name]
     */
    private function getNameSpaceFromClass($class)
    {
        // Get the namespace of the given class via reflection.
        

        $reflection = new \ReflectionClass($class);
        return $reflection->getNameSpaceName() === '' 
                ? $this->defaultNamespace
                : $reflection->getNameSpaceName();
    }

    /**
     * Gets all declared classes in the running PHP app and saves them to $this->namespaceMap.
     */
    public function traverseClasses()
    {
        // Get all declared classes
        $classes = get_declared_classes();

        foreach($classes as $class)
        {
            // Store the namespace of each class in the namespace map
            $namespace = $this->getNameSpaceFromClass($class);
            $this->namespaceMap[$namespace][] = $class;
        }
    }

    /**
     * Accessor method; returns the namespaceMap.
     * @return [array] [The namespaceMap]
     */
    public function getNameSpaces()
    {
        return array_keys($this->namespaceMap);
    }

    /**
     * Returns the classes listed under a namespace.
     * @param  [String] $namespace [The namespace]
     * @return [Array]            [The classes enumerated]
     */
    public function getClassesOfNameSpace($namespace)
    {
        if (!isset($this->namespaceMap[$namespace]))
            throw new \InvalidArgumentException('The Namespace '. $namespace . ' does not exist');

        return $this->namespaceMap[$namespace];
    }

}
