<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Auth;

/**
 * Description of Admin
 *
 * @author vladimir
 */
class Admin extends \Shared\Template {

    protected function validate() {
        $data = $this->prepareData($this->inData);
        switch ($this->method) {
            case 'create':
                $this->validateCreate($data);
                break;
            case 'read':
                $this->validateRead($data);
                break;
            default:
                $this->status = false;
                $this->error = ['error' => 'Неверный метод.'];
               // $this->error = ['error' => $this->method];
        }
    }

    private function prepareData($data) {
        $params = [
            'create' => [
                'password' => isset($data['password']) && $data['password'] != '' ? $data['password'] : null,
                'login' => isset($data['login']) && $data['login'] != '' ? $data['login'] : null,
                'type' => isset($data['type']) && $data['type'] != '' ? $data['type'] : null,
            ],
            'read' => [
                'type' =>  isset($data['type']) && $data['type'] != '' ? $data['type'] : null,
            ]
        ];

        return $params;
    }

    
    private function validateRead($params)
    {
        $this->params = [
            $params['read']['type']
        ];
    }
    protected function read() 
    {
        $query = "SELECT * FROM `users`";
        $dbData = mysqli_query($this->db, $query);
        
        if ($dbData != false) {
            $res = mysqli_fetch_assoc($dbData);
            if (!empty($res)) {
                $this->outData = ['data' => $dbData];
            } else {
                $this->status = false;
                $this->error = ['error' => 'База данных пуста'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибак запроса в бд'];
        }
    }

    protected function edit() {
        
    }

    protected function create() {
        if ($this->status) {
            switch ($this->params['type']) {
                case 'check':
                    $this->typeCheck();
                    break;
                case 'create':
                    $this->typeCreate();
                    break;
                default:
                    $this->status = false;
                    $this->error = ['error' => 'Нет такого типа'];
            }
        }
    }

    private function typeCreate() {
        $password = is_string($this->params['password']) ? $this->params['password'] : '';
        $login = is_string($this->params['login']) ? $this->params['login'] : '';

        //$strForHash = md5($password . $login);
        $strForHash = md5($password . ':' . $login);
        //$strForHash = '12312312';//md5($password . ':' . $login);

        $queryCheck = "SELECT id FROM `admin` WHERE hash = '$strForHash'";
        $dt = mysqli_query($this->db, $queryCheck);
        if ($dt !== false) {
            $resCheck = mysqli_fetch_assoc($dt);
            if (empty($resCheck)) {
                $query = "INSERT INTO admin(hash) VALUES ('$strForHash')";
                $dbData = mysqli_query($this->db, $query);

                if ($dbData != false) {
                    $_SESSION['token'] = $strForHash;
                    $this->outData = ['data' => 'ok'];
                } else {
                    $this->status = false;
                    $this->error = ['error' => 'Ошибка запроса в бд'];
                }
            } else {
                $this->status = false;
                $this->error = ['error' => 'Пользователь уже добавлен'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибка бд'];
           // $this->error = ['error' => $strForHash];
        }
    }

    private function typeCheck() {
        $password = is_string($this->params['password']) ? $this->params['password'] : '';
        $login = is_string($this->params['login']) ? $this->params['login'] : '';

        if (isset($_SESSION['token'])) {
            $strForHash = $_SESSION['token'];
        } else {
            $strForHash = md5($password . ':' . $login);
        }


        $query = "SELECT id FROM admin WHERE hash = '$strForHash'";
        $dbData = mysqli_query($this->db, $query);

        if ($dbData != false) {
            $res = mysqli_fetch_assoc($dbData);
            if (!empty($res)) {
                $_SESSION['token'] = $strForHash;
                $this->outData = ['data' => $strForHash];
            } else {
                $this->status = false;
                $this->error = ['error' => 'Пользователь не найден'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибак запроса в бд'];
        }
    }

    protected function delete() {
        
    }

    private function validateCreate($params) {
        if (!isset($params['create']['password']) || !isset($params['create']['login']) || !isset($params['create']['type'])) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'password' => $params['create']['password'],
                'login' => $params['create']['login'],
                'type' => $params['create']['type']
            ];
        }
    }
}
