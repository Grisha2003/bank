<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Shared;

/**
 * Description of Template
 *
 * @author vladimir
 */ 
abstract class Template {
    protected 
            $inData,
            $params,
            $status,
            $outData,
            $error,
            $method,
            $db;
    
    public function __construct($method, $db)
    {
        $this->status = true;
        $this->params = [];
        $this->method = $method;
        $this->db = $db;
    }
    
    abstract protected function read();
    abstract protected function create();
    abstract protected function edit();
    abstract protected function delete();
    abstract protected function validate();
    
    public function execute($in)
    {
        
        $this->inData = $in;
        $this->validate();
        switch ($this->method) {
            case 'create': $this->create();
                break;
            case 'read': $this->read();
                break;
            case 'edit': $this->edit();
                break;
            case 'delete': $this->delete();
                break;
            default :
                $this->status = false;
                $this->error = 'Нет такого метода';
        }
        return $this->status ? $this->outData : $this->error;
       
    }
    
    
}
