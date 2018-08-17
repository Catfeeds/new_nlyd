<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace library;

/**
 * Autoload.
 */
class Autoloader
{
    /**
     * Autoload root path.
     *
     * @var string
     */
    protected static $_autoloadRootPath = '';

    /**
     * Set autoload root path.
     *
     * @param string $root_path
     * @return void
     */
    public static function setRootPath($root_path)
    {
        self::$_autoloadRootPath = $root_path;
    }

    /**
     * Load files by namespace.
     *
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name)
    {

        $class_path = str_replace('\\', '/', $name);

        if (strpos($name, 'Controller\\') !== false ) {
            $class_file = get_stylesheet_directory() .'/Application/'. $class_path . '.class.php';
        }
        if(strpos($name, 'library\\') !== false ){
            $class_file = ABSPATH.'wp-includes/'. $class_path . '.class.php';
        }
        if(strpos($name, 'Vendor\\') !== false ){
            $class_file = ABSPATH.'wp-includes/library/'. $class_path . '.php';
        }
        
        //var_dump($class_file);
        if(!empty($class_file)){
            if (is_file($class_file)) {
                require_once($class_file);
                if (class_exists($name, false)) {
                    return true;
                }
            }
        }
    }
}

spl_autoload_register('\library\Autoloader::loadByNamespace');