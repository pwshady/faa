<?php

namespace fa;

class Cache
{

    use traits\TSingleton;

    public function set($name, $data, $time = 3600): bool
    {
        $content['data'] = $data;
        $content['end_time'] = time() + $time;
        return file_put_contents( CACHE . '/' . $name . '.facache', serialize($content) ); 
    }

    public function get($name)
    {
        $file_path = CACHE . '/' . $name . '.facache';
        if ( file_exists($file_path) ) {
            $content = unserialize( file_get_contents($file_path) );
            if ( time() <= $content['end_time'] ) {
                return $content['data'];
            }
            unlink($file_path);
        }
        return false;
    }

    public function delete($name)
    {
        $file_path = CACHE . '/' . $name . '.facache';
        if ( file_exists($file_path) ) {
            unlink($file_path);
        }
    }

}