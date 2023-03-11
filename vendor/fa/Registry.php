<?php

namespace fa;

class Registry

{

    use traits\TSingleton;

    protected static array $land = ['code' => 'ru'];
    protected static array $language = ['code' => 'ru'];
    protected static array $userRoles = [];
    protected static array $errors = [
        '404' => 'aaaaaaa',
        '500' => 'aaaaaaa',
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

    public static function setLand($land)
    {
        self::$land = $land;
        return 1;
    }

    public static function getLand(): array
    {
        return self::$land;
    }

    public function setLanguage($language)
    {
        self::$language = $language;
        return 1;
    }

    public function getLanguage(): array
    {
        return self::$language;
    }

    public function addAccess($value)
    {
        foreach (self::$userRoles as $ur) {
            if ($ur == $value) {
                return 1;
            }
        }
        self::$userRoles[] = $value;
            return 2;
    }

    public function getAccess(): array
    {
        return self::$userRoles;
    }

    public function setError($key, $value)
    {
        self::$errors[$key] = $value;
    }

    public function getError($key)
    {
        return self::$errors[$key] ?? null;
    }

    public function getErrors()
    {
        return self::$errors;
    }

    public function setSetting($key, $value)
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

    public function getSetting($key)
    {
        return self::$settings[$key] ?? null;
    }

    public function getSettings()
    {
        return self::$settings;
    }

    public function setLabel($key, $value)
    {
        self::$labels[$key] = $value;
    }

    public function getLabel($key)
    {
        return self::$labels[$key] ?? null;
    }

    public function getLabels()
    {
        return self::$labels;
    }

    public function setModul($name, $params)
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
            $modul = ['name' => $name, 'complete' => false, 'object' => null];
            if ($pos) {
                array_push($result, $modul);
            } else {
                array_unshift($result, $modul);
            }
        }
        self::$modules = $result;
    }

    public function getModul($name)
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

    public function getModules()
    {
        return self::$modules;
    }

    public function updateModul($modul)
    {
        foreach ( self::$modules as $key => $value) {
            if ( array_key_exists('name', $value) ) {
                if ( $modul['name'] == $value['name'] ) {
                    self::$modules[$key] = $modul;
                }
            }
        }
    }
}