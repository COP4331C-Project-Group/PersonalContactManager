<?php
    abstract class JsonDeserializer
    {
        public static function Deserialize($json) : object
        {
            $className = get_called_class();

            $classInstance = new $className();
            
            if (is_string($json))
                $json = json_decode($json);
    
            foreach ($json as $key => $value) {
                if (!property_exists($classInstance, strval($key))) 
                    continue;

                $classInstance->{strval($key)} = $value;
            }
    
            return $classInstance;
        }

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