<?php

class Cache {

 public static $cache;
 
 public static function init(){
  Cache::$cache = new Memcached();
  Cache::$cache->addServer("localhost", 11211);
  
 }

 public static function setValue($key,$val)
 {
  $key = md5($key);
  Cache::$cache->set($key,$val);
 }

 public static function getValue($key)
 {
  $key = md5($key);
  return  Cache::$cache->get($key);
 }
 
 public static function setValueExp($key,$val,$exp=0)
 {
  $key = md5($key);
  Cache::$cache->set($key,$val);

 }

 public static function deleteKey($key)
 {
  $key = md5($key);
  return Cache::$cache->delete($key);
 }
 
 public static function setValueArray($key,$val)
 {
  $key = md5($key);
  $val = serialize($val);
  Cache::$cache->set($key,$val);
 }

 public static function getValueArray($key)
 {
  $key = md5($key);
  $val  = Cache::$cache->get($key);
  return unserialize($val);

 }

}

