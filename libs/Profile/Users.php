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
        $params = [
            'create' => [
                'name' => isset($data['name']) && $data['name'] != '' ? $data['name'] : null,
                'surname' => isset($data['surname']) && $data['surname'] != '' ? $data['surname'] : null,
                'group' => isset($data['group']) && $data['group'] != '' ? $data['group'] : null,
                'sum' => $data['pin'] >= 0 ? (int) $data['sum'] : null,
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'read' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ],
            'edit' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null,
                'sum' => isset($data['sum']) && (int) $data['sum'] > 0 ? (int) $data['sum'] : null,
                'type' => isset($data['type']) && $data['type'] != '' ? $data['type'] : null,
            ],
            'delete' => [
                'pin' => isset($data['pin']) && (int) $data['pin'] > 0 ? (int) $data['pin'] : null
            ]
        ];
        return $params;
    }

    protected function read() {
        if ($this->status) {
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
    }

    protected function create() {
        if ($this->status) {
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
        }
    }

    protected function delete() {
        if ($this->status) {
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
        }
    }

    protected function edit() {
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
                'group' => $data['create']['group']
            ];
        }
    }

    private function validateRead($data) {
        if (!isset($data['read']['pin']) || mb_strlen((string) $data['read']['pin']) > 4) {
            $this->status = false;
            $this->error = ['error' => 'Неверные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'pin' => $data['read']['pin']
            ];
        }
    }

    private function validateEdit($data) {
        if (!isset($data['edit']['pin']) || empty($data['edit']['pin']) || mb_strlen((string) $data['edit']['pin']) > 4) {
            $this->status = false;
            $this->error = ['error' => 'Неправильные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'sum' => $data['edit']['sum'],
                'pin' => $data['edit']['pin'],
                'type' => $data['edit']['type']
            ];
        }
    }

    private function validateDelete($data) {
        if (!isset($data['delete']['pin']) || mb_strlen((string) $data['delete']['pin']) > 4) {
            $this->status = false;
            $this->error = ['error'=>'Неверные параметры'];
        }

        if ($this->status) {
            $this->params = [
                'pin' => $data['delete']['pin']
            ];
        }
    }
}
