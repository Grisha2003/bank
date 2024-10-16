<?php

namespace Shared;

/**
 * Description of Rest
 *
 * @author vladimir
 */
class Rest {
    private $namespace,
            $error,
            $method,
            $request,
            $class,
            $status,
            $params,
            $url;
   
    
    public function __construct($data)
    {
        $this->status = true;
        $this->error = null;
        $this->request = $data;
        $this->url = $data['REQUEST_URI'];
        $this->method = $this->getMethod();
        $this->params = $this->getParams();
        $this->class = $this->getClass();
        $this->namespace = $this->getNamespace();
    }
    
    private function getMethod()
    {
        $retMethod = null;
        $method = $this->request['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $retMethod = 'read';
                break;
            case 'POST':
                $retMethod = 'create';
                break;
            case 'PUT':
                $retMethod = 'edit';
                break;
            case 'DELETE':
                $retMethod = 'delete';
                break;
            default:
                $this->status = false;
                $this->error = ['error' => 'Неверный вид запроса.'];
        }
        return $retMethod;
    }
    
    private function getParams()
    {
        $retData = null;
        if ($this->status) {
            $retData = [];
            $method = $this->request['REQUEST_METHOD'];
            $url = $this->request['QUERY_STRING'];
            if (isset($this->url)) {
                switch ($method) {
                    case 'GET':
                        $retData = $this->openParamsProcessing($url);
                        break;
                    case 'POST':
                        $retData = $this->closeParamsProcessing();
                        break;
                    case 'PUT':
                        $retData = $this->closeParamsProcessing();
                        break;
                    case 'DELETE':
                        $retData = $this->openParamsProcessing($url);
                        break;
                    default :
                        $this->status = false;
                        $this->error = ['error' => 'Неверный вид запроса.'];
                }
            } else {
                $this->status = false;
                $this->error = ['error' => 'Пустая строка URL'];
            }
        }
        return $retData;
    }
    
    private function openParamsProcessing($url)
    {
        if (is_string($url)) {
            parse_str($url, $result);
            $params = [];
            foreach ($result as $key=>$value) {
                $params[$key] = $value;
            }
        } else {
            $this->status = false;
            $this->error = ['error' => 'Тип параметров неверный.'];
        }
        return $this->status ? $params : null;
    }
    
    private function closeParamsProcessing()
    {
        $postData = json_decode(file_get_contents("php://input"), true);
        if (!is_array($postData)) {
            $this->status = false;
            $this->error = ['error' => 'Ошибка json.'];
        }
        
        return $this->status ? $postData : null;
    }
    
    private function getNamespace()
    {
        $retNamespace = null;
        if ($this->status) {
            $arrUrl = explode('/', $this->url);
            if (isset($arrUrl[1])) {
                $retNamespace = ucfirst($arrUrl[1]);
            } else {
                $this->status = false;
                $this->error = ['error' => 'Неверный url.(namespace)'];
            }
        }
        return $retNamespace;
    }
    
    private function getClass()
    {
        $retClass = null;
        if ($this->status) {
            $arrUrl = explode('/', $this->url);
            if (isset($arrUrl[2])) {
                $retClass = ucfirst($arrUrl[2]);
            } else {
                $this->status = false;
                $this->error = ['error' => 'Неверный url.(class)'];
            } 
        }
        return strtok($retClass, '?');
    }
    
    public function getData()
    {
        $ret = [
            'namespace' => $this->namespace,
            'method' => $this->method,
            'params' => $this->params,
            'class' => $this->class,
            'status' => $this->status,
            'error' => $this->error
            
        ];
        return $ret;
    }
}
