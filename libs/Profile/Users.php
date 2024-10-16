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
                'group' => isset($data['group']) && $data['group'] != '' ? $data['group'] : null,
                'sum' => isset($data['sum']) && (int) $data['sum'] > 0 ? (int) $data['sum'] : null,
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
            $query = "SELECT * FROM users WHERE pin = " . $this->params['pin'];
            $dbData = mysqli_query($this->db, $query);
            if ($dbData != false) {
                $res = mysqli_fetch_assoc($dbData);
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
            $query = "INSERT INTO users(name, surname, group, sum, pin) VALUES (".$this->params['name']."," . $this->params['surname'] . "," .$this->params['group'] . ",". $this->params['sum'] .",". $this->params['pin'] . ")";
            $dbData = mysqli_query($this->db, $query);

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
            $query = "UPDATE users SET sum = ". $this->params['sum'] ." WHERE id = " . $this->params['id'];
            $dbData = mysqli_query($this->db, $query);
            
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
                'name' => $data['create']['name'],
                'surname' => $data['create']['surname'],
                'pin' => $data['create']['pin'],
                'sum' => $data['create']['sum'],
                'group' => $data['create']['group']
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
                'pin' => $data['read']['pin']
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
                'sum' => $data['edit']['sum'],
                'id' => $data['edit']['id']
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
                'id' => $data['delete']['id']
            ];
        }
    }
}
