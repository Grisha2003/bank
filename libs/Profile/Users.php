<?php

namespace Profile;

/**
 * Description of Users
 *
 * @author vladimir
 */
class Users extends \Shared\Template {

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

    private function prepareData($data) {
       // file_put_contents('res.txt', $data);
        $params = [
            'create' => [
                'name' => isset($data['name']) && $data['name'] != '' ? $data['name'] : null,
                'surname' => isset($data['surname']) && $data['surname'] != '' ? $data['surname'] : null,
                'group' => isset($data['group']) && $data['group'] != '' ? $data['group'] : null,
                'sum' => isset($data['sum']) >= 0 && !empty($data['sum']) ? (int) $data['sum'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null,
                'token' => isset($data['token']) && $data['token'] != '' ? $data['token'] : null,
            ],
            'read' => [
                'type' => isset($data['type']) && $data['type'] != '' ? $data['type'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'edit' => [
                'token' => isset($data['token']) && $data['token'] != '' ? $data['token'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null,
                'sum' => isset($data['sum']) && (int) $data['sum'] > 0 ? (int) $data['sum'] : null,
                'type' => isset($data['type']) && $data['type'] != '' ? $data['type'] : null,
            ],
            'delete' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null,
                'token' => isset($data['token']) && $data['token'] != '' ? $data['token'] : null,
                //'token' => $data['token']
            ]
        ];
        return $params;
    }

    protected function read() {
        if ($this->status) {
            switch ($this->params['type']) {
                case 'list':
                    $this->typeList();
                    break;
                case 'read':
                    $this->typeRead();
                    break;
            }
        }
    }

    private function typeRead() {
        $pin = $this->params['pin'];
        $query = "SELECT * FROM users WHERE pin = $pin";
        $dbData = mysqli_query($this->db, $query);
        if ($dbData != false) {
            $res = mysqli_fetch_assoc($dbData);
            if (!empty($res)) {
                $this->outData = ['data' => $res];
            } else {
                $this->status = false;
                $this->error = ['error' => 'Пин-код не найден'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибак запроса в бд'];
        }
    }

    private function typeList() {
        $query = "SELECT * FROM users ORDER BY id DESC";
        $dbData = mysqli_query($this->db, $query);
        if ($dbData != false) {
            $res = mysqli_fetch_all($dbData);
            if (!empty($res)) {
                $this->outData = ['data' => $res];
            } else {
                $this->status = false;
                $this->error = ['error' => 'База данных пуста'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибак запроса в бд'];
        }
    }

    protected function create() {
        if ($this->status) {
            if ($this->checkToken()) {
                $name = $this->params['name'];
                $surname = $this->params['surname'];
                $group = $this->params['group'];
                $sum = $this->params['sum'];
                $pin = $this->params['pin'];
                $queryCheck = "SELECT * FROM users WHERE pin = $pin";
                $dt = mysqli_query($this->db, $queryCheck);
                $resCheck = mysqli_fetch_assoc($dt);
                if (empty($resCheck)) {
                    $query = "INSERT INTO users(name, surname, `group`, sum, pin) VALUES ('$name', '$surname', '$group', $sum, $pin)";
                    $dbData = mysqli_query($this->db, $query);

                    if ($dbData != false) {
                        $this->outData = ['data' => 'ok'];
                    } else {
                        $this->status = false;
                        $this->error = ['error' => 'Ошибка запроса в бд'];
                    }
                } else {
                    $this->status = false;
                    $this->error = ['error' => 'Пин-код уже занят'];
                }
            } else {
                $this->status = false;
                $this->error = ['error' => 'Вы не можете выполнять данное действие!'];
            }
        }
    }

    protected function delete() {
        if ($this->status) {
            if ($this->checkToken()) {
                $pin = $this->params['pin'];
                $queryCheck = "SELECT * FROM users WHERE pin = $pin";
                $dt = mysqli_query($this->db, $queryCheck);
                $resCheck = mysqli_fetch_assoc($dt);
                if (!empty($resCheck)) {
                    $query = "DELETE FROM users WHERE pin = $pin";
                    $dbData = mysqli_query($this->db, $query);

                    if ($dbData != false) {
                        $this->outData = ['data' => 'ok'];
                    } else {
                        $this->status = false;
                        $this->error = ['error' => 'Ошибка запроса в бд'];
                    }
                } else {
                    $this->status = false;
                    $this->error = ['error' => 'Пин-код не найден'];
                }
            } else {
                $this->status = false;
                $this->error = ['error' => 'Вы не можете выполнять данное действие!'];
              //  $this->error = ['error' => $this->params['token']];
            }
        }
    }
    
    private function checkToken()
    {
        $ret = false;
        $strForHash = is_string($this->params['token']) ? $this->params['token'] : '';
        $query = "SELECT id FROM `admin` WHERE hash = '$strForHash'";
        $dt = mysqli_query($this->db, $query);
        if ($dt !== false) {
            $resCheck = mysqli_fetch_assoc($dt);
            if (!empty($resCheck)) {
                $ret = true;
            } else {
                $this->status = false;
                $this->error = ['error' => 'Вы не можете выполнять данное действие!'];
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Ошибка бд'];
           // $this->error = ['error' => $strForHash];
        }
        
        return $ret;
    }

    protected function edit() {
        if ($this->checkToken()) {
            $sumMain = null;
            $sum = $this->params['sum'];
            $pin = $this->params['pin'];
            $selQuery = "SELECT sum FROM users WHERE pin = $pin";
            $sumDb = mysqli_query($this->db, $selQuery);
            $sumArr = mysqli_fetch_assoc($sumDb);
            switch ($this->params['type']) {
                case 'plus':
                    if (!empty($sumArr)) {
                        $sumMain = (int) $sumArr['sum'] + $sum;
                    } else {
                        $this->status = false;
                        $this->error = ['error' => 'Пин-код не найден'];
                    }
                    break;
                case 'minus':
                    if (!empty($sumArr)) {
                        if ($sum < $sumArr['sum']) {
                            $sumMain = (int) $sumArr['sum'] - $sum;
                        } else {
                            $this->status = false;
                            $this->error = ['error' => 'Недостаточно средств'];
                        }
                    } else {
                        $this->status = false;
                        $this->error = ['error' => 'Пин-код не найден'];
                    }
                    break;
            }

            if ($this->status) {
                $query = "UPDATE users SET sum = $sumMain WHERE pin = $pin";
                $dbData = mysqli_query($this->db, $query);

                if ($dbData != false) {
                    $this->outData = ['data' => 'ok'];
                } else {
                    $this->status = false;
                    $this->error = ['error' => 'Ошибка запроса в бд'];
                }
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Вы не можете выполнять данное действие!'];
        }
    }

    private function validateCreate($data) {
        if (!isset($data['create']['pin']) || mb_strlen((string) $data['create']['pin']) > 4) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры.'];
        }

        if ($this->status) {
            $this->params = [
                'name' => $data['create']['name'],
                'surname' => $data['create']['surname'],
                'pin' => $data['create']['pin'],
                'sum' => $data['create']['sum'],
                'group' => $data['create']['group'],
                'token' => $data['create']['token']
            ];
        }
    }

    private function validateRead($data) {
        switch ($data['read']['type']) {
            case 'read':
                if (!isset($data['read']['pin']) || mb_strlen((string) $data['read']['pin']) > 4) {
                    $this->status = false;
                    $this->error = ['error' => 'Неверные параметры'];
                }

                if ($this->status) {
                    $this->params = [
                        'pin' => $data['read']['pin'],
                        'type' => $data['read']['type']
                    ];
                }
                break;
            case 'list':
                if ($this->status) {
                    $this->params = [
                        'type' => $data['read']['type']
                    ];
                }
                break;
            default: 
                $this->status = false;
                $this->error = ['error' => 'Неверное значение type'];
        }
    }

    private function validateEdit($data) {
        if (!isset($data['edit']['pin']) 
                || empty($data['edit']['pin']) 
                || mb_strlen((string) $data['edit']['pin']) > 4
                || !isset($data['edit']['sum'])) {
            $this->status = false;
            $this->error = ['error' => 'Неправильные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'token' => $data['edit']['token'],
                'sum' => $data['edit']['sum'],
                'pin' => $data['edit']['pin'],
                'type' => $data['edit']['type']
            ];
        }
    }

    private function validateDelete($data) {
        if (!isset($data['delete']['pin']) || mb_strlen((string) $data['delete']['pin']) > 4) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'pin' => $data['delete']['pin'],
                'token' => $data['delete']['token']
            ];
        }
    }
}
