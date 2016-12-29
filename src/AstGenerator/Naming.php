<?php

namespace Joli\Jane\AstGenerator;

use Doctrine\Common\Inflector\Inflector;

/**
 * Helper to generate name for property / class / ....
 *
 * All methods of the class must be deterministic way (same input will always have the same output)
 */
class Naming
{
    /**
     * Get a property name
     *
     * @param $name
     * @return string
     */
    public static function getPropertyName($name)
    {
        $name = self::replaceDollar($name);

        return Inflector::camelize($name);
    }

    /**
     * Get a method name given a prefix
     *
     * @param $prefix
     * @param $name
     * @return string
     */
    public static function getPrefixedMethodName($prefix, $name)
    {
        $name = self::replaceDollar($name);

        return sprintf("%s%s", $prefix, Inflector::classify($name));
    }

    /**
     * Get a class name
     *
     * @param $name
     * @return string
     */
    public static  function getClassName($name)
    {
        $name = self::replaceDollar($name);

        return Inflector::classify($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected static function replaceDollar($name)
    {
        if (preg_match('/\$/', $name)) {
            $name = preg_replace_callback('/\$([a-z])/', function ($matches) {
                return 'dollar'.ucfirst($matches[1]);
            }, $name);
        }

        return $name;
    }
}
