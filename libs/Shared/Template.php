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
    abstract function read();
    abstract function create();
    abstract function edit();
    abstract function delete();
    
    
    public function execute($in)
    {
        
    }
    
    public function request()
    {
        
    }
}
