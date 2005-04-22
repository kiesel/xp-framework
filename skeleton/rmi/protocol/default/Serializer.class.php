<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Serializer extends Object {

    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   &mixed var
     * @return  string
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function representationOf(&$var) {
      switch (gettype($var)) {
        case 'NULL':    return 'N;';
        case 'boolean': return 'b:'.$var.';';
        case 'integer': return 'i:'.$var.';';
        case 'float':   return 'd:'.$var.';';
        case 'string':  return 's:'.strlen($var).':"'.$var.'";';
        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= Serializer::representationOf($key).Serializer::representationOf($var[$key]);
          }
          return $s.'}';
        case 'object':
          $name= xp::typeOf($var);
          $props= get_object_vars($var);
          $s= 'O:'.strlen($name).':"'.$name.'":'.sizeof($props).':{';
          foreach (array_keys($props) as $name) {
            $s.= Serializer::representationOf($name).Serializer::representationOf($var->{$name});
          }
          unset($r);
          return $s.'}';
        case 'resource': return ''; // Ignore (resources can't be serialized)
        default: throw(new FormatException(
          'Cannot serialize unknown type '.xp::typeOf($var)
        ));
      }
    }
  
    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   string serialized
     * @param   &int length
     * @return  &mixed
     * @throws  lang.ClassNotFoundException if a class cannot be found
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function &valueOf($serialized, &$length, &$handler) {
      switch ($serialized{0}) {
        case 'N': $length= 2; return NULL;
        case 'b': $length= 4; return (bool)substr($serialized, 2, strpos($serialized, ';', 2)- 2);
        case 'i': 
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (int)$v;
        case 'd': 
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (float)$v;
        case 's':
          $strlen= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $length= 2 + strlen($strlen) + 2 + $strlen + 2;
          return substr($serialized, 2+ strlen($strlen)+ 2, $strlen);
        case 'a':
          $a= array();
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset+= strlen($size)+ 2+ 2;
          for ($i= 0; $i < $size; $i++) {
            $key= Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
            $a[$key]= &Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $a;
        case 'O':
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          try(); {
            $class= &XPClass::forName(substr($serialized, 2+ strlen($len)+ 2, $len));
          } if (catch('ClassNotFoundException', $e)) {
            $class= &XPClass::forName('lang.Object');   // FIXME: Use UnknownClass or sth.
            #return throw($e);
          }
          $instance= &$class->newInstance();
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
            $instance->{$member}= &Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $instance;
        case 'I':
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $interface= substr($serialized, 2+ strlen($len)+ 2, $len);
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          $cl= &ClassLoader::getDefault();
          try(); {
            $instance= &Proxy::newProxyInstance(
              $cl, 
              array(XPClass::forName($interface, $cl)), 
              $handler->newInstance(Serializer::valueOf(substr($serialized, $offset), $len, $handler))
            );
          } if (catch('ClassNotFoundException', $e)) {
            return throw($e);
          }
          $length= $offset+ 1;
          return $instance;
          
        default: throw(new FormatException(
          'Cannot deserialize unknown type "'.$serialized{0}.'" ('.$serialized.')'
        ));
      }
    }
  }
?>
