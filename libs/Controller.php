<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Controller;

class NoModuleException extends \Exception {
    
}

class NoControllerException extends \Exception {
    
}

class NoActionException extends \Exception {
    
}

class Controller {

    /**
     *
     * @var \Hiano\View\View
     */
    protected $view;
    
    /**
     *
     * @var \Hiano\Request\Request
     */
    protected $request;

    /**
     * 
     * @param \Hiano\Request\Request $request
     * @param \Hiano\View\View $view
     */
    public function __construct($request, $view) {
        $this->view = $view;
        $this->request = $request;
    }

    /**
     * 
     * @return \Hiano\View\View
     */
    function getView() {
        return $this->view;
    }

    /**
     * 
     * @return \Hiano\Request\Request
     */
    function getRequest() {
        return $this->request;
    }

    /**
     * 派遣动作
     * @param string $action_name 动作名
     * @return mix 返回动作方法的返回值
     * @throws  NoActionException
     */
    function dispatch($action_name) {
        if (method_exists($this, '_init')) {
            $this->_init();
        }
        $action = self::getActionMethodName($action_name);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            throw new NoActionException;
        }
    }

    /**
     * 调用其他动作，后面的代码继续执行
     * @param string $controller_name 控制器名
     * @param string $action_name 动作名
     * @return mixed
     */
    function execute($controller_name, $action_name) {
        return self::getController($controller_name)->dispatch($action_name);
    }

    /**
     * 转到其他动作，调用此函数后，后面的代码将不再执行
     * @param string $controller_name 控制器名
     * @param string $action_name 动作名
     * @throws spStopException
     */
    function forward($controller_name, $action_name) {
        if ($this->execute($controller_name, $action_name) !== false) {
            \Hiano\View\View::display();
        }
        throw new StopException;
    }

    /**
     * 设置出错信息，后面的代码继续执行
     * @param string $description 错误描述
     * @param int $code 错误代号
     */
    function setError($description, $code = 0) {
        $app = $this->view->get('app');
        $app['error'] = array(
            'code' => $code,
            'description' => $description
        );
        $this->view->set('app',$app);
    }

    /**
     * 抛出错误，后面的代码不再执行
     * @param string $description 错误描述
     * @param int $code 错误代号
     * @throws spStopException
     */
    function error($description, $code = 0) {
        $this->setError($description, $code);
        $this->view->display();
        throw new \Hiano\App\StopException();
    }
    
    static function getActionMethodName($action_name){
        return $action_name . 'Action';
    }

}
