<?php

namespace fa;

class Registry

{

    use traits\TSingleton;

    protected static string $page = '';
    protected static string $request = '';

    protected static string $get = '';

    protected static array $land = ['code' => 'ru'];
    protected static array $language = ['code' => 'ru'];
    protected static array $userRoles = [];
    protected static array $errors = [
        '404' => '',
        '500' => '',
    ];
    protected static array $settings = [
        'title' => '',
        'styles' => [
            ['label' => '', 'type' => '', 'path' => ''],
            ['label' => 'test', 'type' => '', 'path' => '']
        ],
        'header_scripts' => [
            ['label' => '', 'type' => '', 'path' => '']
        ],
        'header_strings' => [
            ['label' => '', 'string' => '']
        ],
        'footer_scripts' => [
            ['label' => '', 'type' => '', 'path' => '']
        ],
        'footer_strings_top' => [
            ['label' => '', 'string' => '']
        ],
        'footer_strings_bottom' => [
            ['label' => '', 'string' => '']
        ],
    ];
    protected static array $labels = [
        'p__' => 'label'
    ];

    /*
    *Key: 'name' - modul name. Required key
    *Key: 'complete' - Value 1 after code processing
    *Key: 'object' - Resulting from the creation of the modul
    */
    protected static array $modules = [];

    /*
    *Key: 'name' - widget name. Required key
    *Key: 'complete' - Value 1 after code processing
    *Key: 'code' - Html code resulting from the creation of the widget
    *Key: 'cache' - Html code caching time in seconds. Optional key
    *Key: 'view' - View selector. Optional key
    */
    protected static array $widgets = [];

    public static function setPage($page)
    {
        self::$page = $page;
        return 1;
    }

    public static function getPage(): string
    {
        return self::$page;
    }

    public static function setRequest($request)
    {
        self::$request = $request;
        return 1;
    }

    public static function getRequest(): string
    {
        return self::$request;
    }

    public static function setGet($get)
    {
        self::$get = $get;
        return 1;
    }

    public static function getGet(): string
    {
        return self::$get;
    }

    public static function setLand($land)
    {
        self::$land = $land;
        return 1;
    }

    public static function getLand(): array
    {
        return self::$land;
    }

    public static function setLanguage($language)
    {
        self::$language = $language;
        return 1;
    }

    public static function getLanguage(): array
    {
        return self::$language;
    }

    public static function addAccess($value)
    {
        foreach (self::$userRoles as $ur) {
            if ($ur == $value) {
                return 1;
            }
        }
        self::$userRoles[] = $value;
            return 2;
    }

    public static function getAccess(): array
    {
        return self::$userRoles;
    }

    public function cleanAccess()
    {
        self::$userRoles = [];
    }

    public static function setError($key, $value)
    {
        self::$errors[$key] = $value;
    }

    public static function getError($key)
    {
        return self::$errors[$key] ?? '';
    }

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function setSetting($key, $value)
    {
        $method = 0;
        $pos = true;
        if (is_array($value)) {
            if (array_key_exists('method', $value)) {
                $method = $value['method'];
            }
        }
        if (is_array($value)) {
            if (array_key_exists('pos', $value)) {
                $pos = $value['pos'];
            }
        }
        switch ($method) {
            case 0:
                if (is_array($value)) {
                    self::$settings[$key][0] = $value;
                } else {
                self::$settings[$key] = $value;
                }
                break;
            case 1:
                if ($pos) {
                    array_push(self::$settings[$key], $value);
                } else {
                    array_unshift(self::$settings[$key], $value);
                }       
                break;
            case -1:
                if (is_array($value)) {
                    if (array_key_exists('label', $value)) {
                        $label = $value['label'];
                        $result = [];
                        foreach (self::$settings[$key] as $v) {
                            if (array_key_exists('label', $v)) {
                                if ($label != $v['label']) {
                                    array_push($result, $v);
                                }                               
                            } else {
                                array_push($result, $value);
                            }
                        }
                        self::$settings[$key] = $result;
                        break;
                    } 
                }
                self::$settings[$key] = null;
                break;
        }       
    }

    public static function getSetting($key)
    {
        return self::$settings[$key] ?? null;
    }

    public static function getSettings()
    {
        return self::$settings;
    }

    public static function setLabel($key, $value)
    {
        self::$labels[$key] = $value;
    }

    public static function getLabel($key)
    {
        return self::$labels[$key] ?? null;
    }

    public static function getLabels()
    {
        return self::$labels;
    }

    public static function setModul($name, $params)
    {
        $method = true;
        $pos = true;
        $result = [];
        foreach (self::$modules as $modul) {
            if ($name != $modul['name']) {
                array_push($result, $modul);
            }                               
        }
        if (array_key_exists('method', $params)) {
            $method = $params['method'];
        }
        if (array_key_exists('pos', $params)) {
            $pos = $params['pos'];
        }
        if ($method) {
            $modul = array_merge(['name' => $name, 'complete' => false, 'object' => null], $params);
            if ($pos) {
                array_push($result, $modul);
            } else {
                array_unshift($result, $modul);
            }
        }
        self::$modules = $result;
    }

    public static function getModul($name)
    {
        foreach ( self::$modules as $key => $value) {
            if ( array_key_exists('name', $value) ) {
                if ( $value['name'] == $name ) 
                {
                    return $value;
                }
            }
        }
        return null;
    }

    public static function getModules()
    {
        return self::$modules;
    }

    public static function updateModul($modul)
    {
        foreach ( self::$modules as $key => $value) {
            if ( array_key_exists('name', $value) ) {
                if ( $modul['name'] == $value['name'] ) {
                    self::$modules[$key] = $modul;
                }
            }
        }
    }

    public static function setWidget($name, $params)
    {
        $method = true;
        $pos = true;
        $result = [];
        foreach (self::$widgets as $widget) {
            if ($name != $widget['name']) {
                array_push($result, $widget);
            }                               
        }
        if (array_key_exists('method', $params)) {
            $method = $params['method'];
        }
        if (array_key_exists('pos', $params)) {
            $pos = $params['pos'];
        }
        if ($method) {
            $widget = array_merge(['name' => $name, 'complete' => false, 'code' => ''], $params);
            if ($pos) {
                array_push($result, $widget);
            } else {
                array_unshift($result, $widget);
            }
        }
        self::$widgets = $result;
    }

    public static function getWidget($name)
    {
        foreach ( self::$widgets as $key => $value) {
            if ( array_key_exists('name', $value) ) {
                if ( $value['name'] == $name ) 
                {
                    return $value;
                }
            }
        }
        return null;
    }

    public static function getWidgets()
    {
        return self::$widgets;
    }

    public static function updateWidget($widget)
    {
        foreach ( self::$widgets as $key => $value) {
            if ( array_key_exists('name', $value) ) {
                if ( $widget['name'] == $value['name'] ) {
                    self::$widgets[$key] = $widget;
                }
            }
        }
    }


}