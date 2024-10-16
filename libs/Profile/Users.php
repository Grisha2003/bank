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
                $this->error = ['error'=>'Неверный метод.'];
                
        }
    }
    
    private function prepareData($data)
    {
        $params = [
            'create' => [
                'name'=> isset($data['name']) && $data['name'] != '' ? $data['name'] : null,
                'surname' => isset($data['surname']) && $data['surname'] != '' ? $data['surname'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'read' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ]
        ];
        return $params;
    }
    
    protected function read()
    {
        if ($this->status) {
            $query = "SELECT * FROM users WHERE pin = $1";
            $dbData = pg_query_params($this->db, $query, $this->params);
            if ($dbData != false) {
                $res = pg_fetch_assoc($dbData);
                $this->outData = ['answer'=>$res];
            } else {
                $this->status = false;
                $this->error = ['error' => 'Ошибак запроса в бд'];
            }
        }
    }
    
    protected function create()
    {
        if ($this->status) {
            $query = "INSERT INTO users(name, surname, pin) VALUES ($1, $2, $3)";
            $dbData = pg_query_params($this->db, $query, $this->params);

            if ($dbData != false) {
                //$res = pg_fetch_assoc($dbData);
                $this->outData = ['answer' => 'Ок'];
            } else {
                $this->status = false;
                $this->error = ['error' => 'Ошибка запроса в бд'];
            } 
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
            $this->error = ['error' => 'Неверные параметры.'];
        }
        
        if ($this->status) {
            $this->params = [
                $data['create']['name'],
                $data['create']['surname'],
                $data['create']['pin']
            ];
        }
    }
    
    private function validateRead($data)
    {
        if (!isset($data['read']['pin'])) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры.'];
        }
        
        if ($this->status) {
            $this->params = [
                $data['read']['pin']
            ];
        }
    }
    
    private function validateEdit($data)
    {
        
    }
    
    private function validateDelete($data)
    {
        
    }
}
