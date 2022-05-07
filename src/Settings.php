<?php

namespace Luanardev\Settings;

use Spatie\LaravelSettings\Settings as SpatieSettings;
use Illuminate\Support\Str;
use ReflectionObject;
use ReflectionProperty;

abstract class Settings extends SpatieSettings
{
	
    public function get($property)
    {
        if($this->exists($property)){
            if(gettype($this->{$property}) == "boolean"){
                return $this->getBoolean($property);
            }else{
                return $this->getProperty($property);
            }           
        }
    }

    public function put($property, $value)
    {
        if($this->exists($property)){
            if(gettype($this->{$property}) == "boolean"){
               $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            $this->{$property} = $value;
            $this->save();
        }
    }

    public function saveAll(array $properties)
    {
        foreach($properties as $property => $value){
            $this->put($property, $value);
        }
    }

    public function exists($property)
    {
        $settings = $this->getSettings();
        return (array_key_exists($property, $settings)) ? true: false;
    }

    public function getProperties()
    {
        $reflectionObject = new ReflectionObject($this);
        return $reflectionObject->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    public function getSettings()
    {
        $results = [];
        $properties = $this->getProperties();
        foreach ($properties as $property) {
            $results[$property->getName()] = $property->getValue($this);
        }
        return $results;
    }

    private function getBoolean($property)
    {
        $value = $this->getProperty($property);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    private function getProperty($property)
    {
        return $this->{$property};      
    }


}
