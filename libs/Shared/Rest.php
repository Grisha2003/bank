<?php

namespace Route;

/**
 * Description of Rest
 *
 * @author vladimir
 */
class Rest {
    private $namespace;
    private $method;
    private $inData;
    private $outData;
    public function __construct($data)
    {
        $this->inData = $data;
    }
    
    private function readMethod($method)
    {
        $retMethod = null;
        switch ($method) {
            case 'POST':
                $retMethod = 'create';
                break;
            case 'GET':
                $retMethod = 'read';
                break;
            case 'PUT':
                $retMethod = 'edit';
                break;
            case 'DELETE':
                $retMethod = 'delete';
                break;
            default:
                //ERROR
        }
        
        return $retMethod;
    }
    
    private function readBody($body)
    {
        
    }
    
    public function getData()
    {
        $ret = [
            'namespace' => $this->namespace,
            'method' => $this->method,
            'data' => $this->outData
        ];
        
        return $ret;
    }
}
