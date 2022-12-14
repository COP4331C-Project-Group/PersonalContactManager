<?php
    abstract class JsonDeserializer
    {
        /**
         * Deserializes json string or object to an object.
         * 
         * @param string|object|array $json json string or json object.
         * @return object object of the class that inherited this method.
         */
        public static function Deserialize($json) : object
        {
            $className = get_called_class();

            $classInstance = new $className();
            
            if (is_string($json))
                $json = json_decode($json);
    
            foreach ($json as $key => $value) {
                if (!property_exists($classInstance, strval($key))) 
                    continue;
                    
                try 
                {                
                    $classInstance->{strval($key)} = $value;
                }
                catch (TypeError $e)
                {
                    continue;
                }

            }
    
            return $classInstance;
        }

        /**
         * Deserializes an array of json strings or objects to an array of objects.
         * 
         * @param string $json json string or json object represented as an array.
         * @return array array of objects of the class that inherited this method.
         */
        public static function DeserializeArray($json) : array
        {
            $json = json_decode($json);
            $items = [];

            foreach ($json as $item)
                $items[] = self::Deserialize($item);

            return $items;
        }
    }
?>
