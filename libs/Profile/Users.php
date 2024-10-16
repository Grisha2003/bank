<?php

namespace Profile;

/**
 * Description of Users
 *
 * @author vladimir
 */
class Users extends \Shared\Template 
{

    protected function validate() {
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
                $this->error = ['error' => 'Неверный метод.'];
        }
    }

    private function prepareData($data) 
    {
        $params = [
            'create' => [
                'name' => isset($data['name']) && $data['name'] != '' ? $data['name'] : null,
                'surname' => isset($data['surname']) && $data['surname'] != '' ? $data['surname'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'read' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'edit' => [
                'id' => isset($data['id']) && (int) $data['id'] > 0 ? (int) $data['id'] : null,
                'summ' => isset($data['summ']) && (int) $data['summ'] > 0 ? (int) $data['summ'] : null
            ],
            'delete' => [
                'id' => isset($data['id']) && (int) $data['id'] > 0 ? (int) $data['id'] : null
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
                $this->outData = ['answer' => $res];
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
                $this->outData = ['answer' => 'ok'];
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
        if ($this->status) {
            $query = "UPDATE users SET sum = $1 WHERE id = $2";
            $dbData = pg_query_params($this->db, $query, $this->params);
            
            if ($dbData != false) {
               //$res = pg_fetch_assoc($dbData);
               $this->outData = ['answer' => 'ok'];
            } else {
                $this->status = false;
                $this->error = ['error' => 'Ошибка запроса в бд'];
            }
        }
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
        if (!isset($data['edit']['id'])
                || !isset($data['edit']['sum'])) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры'];
        }
        
        if ($this->status) {
            $this->params = [
                $data['edit']['sum'],
                $data['edit']['id']
            ];
        }
    }

    private function validateDelete($data) 
    {
        if (!isset($data['delete']['id'])) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры'];
        }
        
        if ($this->status) {
            $this->params = [
                $data['delete']['id']
            ];
        }
    }
}
