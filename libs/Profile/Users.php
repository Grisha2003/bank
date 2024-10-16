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
        $retArr = [];
        $query = "INSERT INTO users(name, surname, pin) VALUES ($1, $2, $3)";
        $dbData = pg_query_params($this->db, $query, $this->params);
        
        if ($dbData != false) {
            $res = pg_fetch_assoc($dbData);
            $this->outData = $res;
        } else {
            $this->status = false;
            $this->error = 'Ошибка запроса в бд';
        }
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
        if (!isset($data['create']['pin'])) {
            $this->status = false;
            $this->error = 'Неверные параметры.';
        }
        
        if ($this->status) {
            $this->params = [
                $data['name'],
                $data['surname'],
                $data['pin']
            ];
        }
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
