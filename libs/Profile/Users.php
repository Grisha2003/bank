<?php

namespace Profile;

/**
 * Description of Users
 *
 * @author vladimir
 */
class Users extends \Shared\Template 
{
    protected function validate()
    {
        $data = $this->prepareData($this->inData);
        switch ($this->method) {
            case 'create': 
                $this->validateCreate($data);
                break;
            case 'read': 
                $this->validateRead($data);
                break;
            case 'edit': 
                $this->validateEdit($data);
                break;
            case 'delete': 
                $this->validateDelete($data);
                break;
            default :
                $this->status = false;
                $this->error = 'Неверный метод.';
                
        }
    }
    
    private function prepareData($data)
    {
        $params = [
            'create' => [
                'name'=> isset($data['name']) && $data['name'] != '' ? $data['name'] : null,
                'surname' => isset($data['surname']) && $data['surname'] != '' ? $data['surname'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ]
        ];
        return $params;
    }
    
    protected function read()
    {
        return;
    }
    
    protected function create()
    {
        return;
    }
    
    protected function delete()
    {
        return;
    }
    
    protected function edit()
    {
        return;
    }
    
    private function validateCreate($data)
    {
        
    }
    
    private function validateRead($data)
    {
        
    }
    
    private function validateEdit($data)
    {
        
    }
    
    private function validateDelete($data)
    {
        
    }
}
