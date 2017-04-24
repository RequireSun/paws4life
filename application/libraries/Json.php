<?php
class Json
{
    private $json     = "";

    /* {{{ protected void __construct() */
    /**
     * Constructor of QQVIP_Json
     */
    protected function __construct()
    {
    }
    /* }}} */

    /* {{{ public static string encode(array) */
    /**
     * Encode array to JSON.
     *
     * @param  array  $data  Array data.
     * @return  string
     */
    public static function encode($data)
    {
        if ( !isset($this) ) {
            $instance = new self();
            return $instance->encode_data($data);
        }
        $json = $this->encode_data($data);
        $this->json = $json;
        return $json;
    }
    /* }}} */

    /* {{{ private string encode_data(mixed &) */
    /**
     * Encode array to JSON format recursively.
     *
     * @param  array& $data  The input array.
     * @return  string
     */
    private function encode_data(&$data)
    {
        if ( is_array($data) ) {
            return $this->encode_array($data);
        }
        else if ( is_string($data) ) {
            return $this->encode_string($data);
        }
        else if ( is_int($data) || is_float($data) ) {
            return $data;
        }
        else if ( is_bool($data) ) {
            return ( $data ? "true" : "false" );
        }
        else {
            return "null";
        }
    }
    /* }}} */

    /* {{{ private string encode_array(array &) */
    /**
     * Encode array.
     *
     * @param  array&  $data  The input array.
     * @return  string
     */
    private function encode_array(&$data)
    {
        $tmp = array();

        // Check array type: Indexed or Associative
        $key_arr = range( 0, count($data)-1 );
        if ( !empty($data) && array_keys($data) !== $key_arr ) {
            // Associative array
            while( list($key, $value) = each($data) ) {
                $tmp[] = $this->encode_string($key)
                       . ":"
                       . $this->encode_data($value);
            }
            return "{" . implode(",", $tmp) . "}";
        }
        else {
            // Indexed array
            $count = count($data);
            for( $i = 0; $i < $count; $i++ ) {
                $tmp[] = $this->encode_data($data[$i]);
            }
            return "[" . implode(",", $tmp) . "]";
        }
    }
    /* }}} */

    /* {{{ private string encode_string(string &) */
    /**
     * Encode string.
     *
     * @param  string  $data  Array key or element value
     * @return  string
     */
    private function encode_string(&$data)
    {
        $data = trim($data);
        $search = array( '"', "\\", "\b", "\f", "\n", "\r", "\t" );
        $replace = array( "\\\"", "\\\\", "\\\b", "\\\f", "\\\n",
                          "\\\r", "\\\t" );
        $data = str_replace( $search, $replace, $data );
        $data = str_replace('\\\"', '\"', $data);
        return '"' . $data . '"';
    }
    /* }}} */

    /* {{{ public static array decode(string) */
    /**
     * Decode JSON data to an array.
     *
     * @param  string  $json    JSON data.
     * @return  array | false    return result array or false in failure.
     */
    public static function decode($json)
    {
        if ( function_exists("json_decode") ) {
            return json_decode($json);
        }

        // todo: decode JSON data in PHP if buidin decoder does not exist.
    }
    /* }}} */

    /* {{{ public void __destruct() */
    /**
     * Destructor of QQVIP_Json
     */
    public function __destruct()
    {
    }
    /* }}} */
}