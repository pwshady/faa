<?php

namespace fa;

use fa\basic\controllers;

class Router
{

    protected static string $page = '';
    protected static array $page_arr = [];
    protected static string $get = '';

    public static function dispatch($url)
    {
        self::removeQueryString($url);
        self::$page_arr = explode('/', self::$page);
        if (file_exists(ROOT . '/app/landlang.json')){
            echo self::getLandLang();
        }
        self::run();
    }

    protected static function removeQueryString($url)
    {
        if ($url) {
            $url_arr = explode('&', $url, 2);
            if (str_contains($url_arr[0], '=') === false) {
                if (count($url_arr) == 2) {
                    self::$page = $url_arr[0];
                    self::$get = $url_arr[1];
                return 1;
                } else {
                    self::$page = $url_arr[0];
                }
            } else {
                self::$get = $url_arr[0];
            }            
            return 1;
        }
    }

    protected static function getLandLang()
    {
        $res = 1;
        $ll = json_decode(file_get_contents(ROOT . '/app/landlang.json'), true);
        if (array_key_exists('base_land', $ll)) {
            self::setLand($ll['base_land']);
        }
        if (array_key_exists('base_language', $ll)) {
            self::setLanguage($ll['base_language']);
        }
        if (array_key_exists('type', $ll)) {
            switch ($ll['type']) {
                case 'prefix':
                    $res = self::getPrefixLandLang($ll);
                    if ($res <= 0 ) {
                        if (array_key_exists('base_land', $ll)) {
                            self::setLand($ll['base_land']);
                        }
                        if (array_key_exists('base_language', $ll)) {
                            self::setLanguage($ll['base_language']);
                        }
                    }
                    break;
            }
        }
        return $res;
    }

    protected static function run()
    {
        $controller = new basic\controllers\PageController('/app/pages', self::$page_arr);
        $controller->run();
    }

    protected static function getPrefixLandLang($ll)
    {
        if (array_key_exists('regex', $ll)) {
            $regex = $ll['regex'];
            $page_arr = explode('/', self::$page);
            if ($page_arr[0] == '') {
                return 1;
            }
            $res_land = -1;
            $res_languages = -1;
            if (array_key_exists('delimiter', $ll)) {
                if ($ll['delimiter'] == '/') {
                    return -1;
                }
                preg_match($regex, $page_arr[0], $prefix);
                $prefix_arr = explode($ll['delimiter'], $prefix[0]);
                if (array_key_exists('land', $ll)) {
                    switch ($ll['land']) {
                        case '0':
                            if (array_key_exists('lands', $ll)) {
                                $lands = $ll['lands'];
                                if (is_array($lands)) {
                                    foreach ($lands as $key => $value) {
                                        if ($prefix_arr[0] === $key) {
                                            $res_land = self::setLand($value);
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        case '1':
                            if (array_key_exists('lands', $ll) && count($prefix_arr == 2)) {
                                $lands = $ll['lands'];
                                if (is_array($lands)) {
                                    foreach ($lands as $key => $value) {
                                        if ($prefix_arr[1] === $key) {
                                            $res_land = self::setLand($value);
                                            break;
                                        }
                                    }
                                }
                            }                            
                            break;
                        default:
                            return -100;
                    }
                }
                if (array_key_exists('language', $ll)) {
                    switch ($ll['language']) {
                        case '0':
                            if (array_key_exists('languages', $ll)) {
                                $languages = $ll['languages'];
                                if (is_array($languages)) {
                                    foreach ($languages as $key => $value) {
                                        if ($prefix_arr[0] === $key) {
                                            $res_languages = self::setLanguage($value);
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        case '1':
                            if (array_key_exists('languages', $ll) && count($prefix_arr) == 2) {
                                $languages = $ll['languages'];
                                if (is_array($languages)) {
                                    foreach ($languages as $key => $value) {
                                        if ($prefix_arr[1] === $key) {
                                            $res_languages = self::setLanguage($value);
                                            break;
                                        }
                                    }
                                }
                            }                            
                            break;
                        default:
                            return -100;
                    }

                }
            } else {
                preg_match($regex, $page_arr[0], $prefix);
                if (array_key_exists('land', $ll)) {
                    switch ($ll['land']) {
                        case '0':
                            if (array_key_exists('lands', $ll)) {
                                $lands = $ll['lands'];
                                if (is_array($lands)) {
                                    foreach ($lands as $key => $value) {
                                        if ($prefix[0] === $key) {
                                            $res_land = self::setLand($value);
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        default:
                            return -100;
                    }
                }
                if (array_key_exists('language', $ll)) {
                    switch ($ll['language']) {
                        case '0':
                            if (array_key_exists('languages', $ll)) {
                                $languages = $ll['languages'];
                                if (is_array($languages)) {
                                    foreach ($languages as $key => $value) {
                                        if ($prefix[0] === $key) {
                                            $res_languages = self::setLanguage($value);
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        default:
                            return -100;
                    }
                }
            }
            if ($res_land > 0 && $res_languages > 0) {
                array_shift(self::$page_arr);
                return 1;
            } else {
                return -1;
            }
        }
    }

    protected static function setLand($land)
    {
        if (self::validateLand($land)) {
            return App::$app->setLand($land);
        }
    }

    protected static function validateLand($land)
    {
        return true;
    }

    protected static function setLanguage($lang)
    {
        if (self::validateLanguage($lang)) {
            return App::$app->setLanguage($lang);
        }
    }

    protected static function validateLanguage($lang)
    {
        return true;
    }
}