<?php
namespace PhpMvc;

/**
 * The main class that works wonders.
 */
class Make {

    /**
     * Magic!
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    public static function magic($appNamespace) {
        self::init($appNamespace);
        self::include();
        self::dispatch();
    }

    /**
     * Performs the initialization of the engine.
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    private static function init($appNamespace) {
        define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        define('PHPMVC_ROOT_PATH', getcwd() . PHPMVC_DS);
        define('PHPMVC_CORE_PATH', __DIR__ .PHPMVC_DS);
        define('PHPMVC_CONFIG_PATH', PHPMVC_ROOT_PATH . 'config' . PHPMVC_DS);
        define('PHPMVC_CONTROLLER_PATH', PHPMVC_ROOT_PATH . 'controllers' . PHPMVC_DS);
        define('PHPMVC_MODEL_PATH', PHPMVC_ROOT_PATH . 'models' . PHPMVC_DS);
        define('PHPMVC_VIEW_PATH', PHPMVC_ROOT_PATH . 'views' . PHPMVC_DS);
        define('PHPMVC_SHARED_PATH', PHPMVC_VIEW_PATH . 'shared' . PHPMVC_DS);
        define('PHPMVC_UPLOAD_PATH', PHPMVC_ROOT_PATH . 'upload' . PHPMVC_DS);
        
        define('PHPMVC_APP_NAMESPACE', $appNamespace);

        define('PHPMVC_CONTROLLER', isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'Home');
        define('PHPMVC_ACTION', isset($_REQUEST['action']) ? $_REQUEST['action'] : 'index');
        define('PHPMVC_VIEW', strtolower(PHPMVC_CONTROLLER));
        define('PHPMVC_CURRENT_CONTROLLER_PATH', PHPMVC_CONTROLLER_PATH . PHPMVC_VIEW . PHPMVC_DS);
        define('PHPMVC_CURRENT_VIEW_PATH', PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . PHPMVC_ACTION . '.php');
    }

    /**
     * Includes the required files. 
     */
    private static function include() {
        require_once PHPMVC_CORE_PATH . 'Loader.php';
        require_once PHPMVC_CORE_PATH . 'Controller.php';
    }

    private static function dispatch() {
        $controller_name = PHPMVC_APP_NAMESPACE . '\\Controllers\\' . PHPMVC_CONTROLLER . 'Controller';
        $controllerInstance = new $controller_name();
        $actionName = PHPMVC_ACTION;
        $requestModel = self::getRequestModel();
        $viewFile = '';
        $result = null;
        $modelState = null;
        $actionResult = null;

        try {
            $actionResult = $controllerInstance->$actionName($requestModel);
        }
        catch (\Exception $ex) {
            // TODO: 500
            // TODO: object
            $modelState = array(
                'exception' => $ex
            );
        }

        if ($actionResult instanceof View) {
            !empty($actionResult->layout) ? ViewContext::$layout = $actionResult->layout : null;
            !empty($actionResult->title) ? ViewContext::$title = $actionResult->title : null;
            
            if (!empty($actionResult->viewData)) {
                ViewContext::$viewData = array_unique(array_merge(ViewContext::$viewData, $actionResult->viewData), SORT_REGULAR);
            }

            $actionResult = $actionResult->model;
        }

        ViewContext::$actionResult = $actionResult;

        // get view
        if (($viewFile = self::getViewFilePath(PHPMVC_CURRENT_VIEW_PATH)) !== false) {
            ViewContext::$content = self::getView($viewFile, ViewContext::$actionResult, $modelState);
        }
        else {
            ViewContext::$content = ViewContext::$actionResult;
        }

        // get layout
        if (!empty(ViewContext::$layout)) {
            $result = self::getView(self::getViewFilePath(ViewContext::$layout), ViewContext::$actionResult);
        }
        else {
            $result = ViewContext::$content;
        }

        // preparation for render
        if (!is_string($result)) {
            if (($result = json_encode(ViewContext::$actionResult)) === false) {
                throw new \ErrorException('JSON encode error #' . json_last_error() . ': ' . json_last_error_msg());
            }

            header('Content-Type: application/json');
        }

        // render
        echo $result;
    }

    public static function getView($path, $actionResult = NULL, $modelState = NULL) {
        ob_start();

        require($path);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    private static function getRequestModel() {
        // TODO: GET and other:
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return NULL;
        }

        return (object)$_POST;
    }

    /**
     * Searches for a file with a specified name and returns the correct path.
     * If the file is not found, returns FALSE. 
     * 
     * @param string $path The file name or file path.
     * @return string|bool
     */
    public static function getViewFilePath($path) {
        if (empty($path)) {
            return false;
        }
        elseif (is_file($result = $path)) {
            return $result;
        }
        elseif (is_file($result = PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . $path)) {
            return $result;
        }
        elseif (is_file($result = PHPMVC_SHARED_PATH . $path)) {
            return $result;
        }

        if (strlen($path) > 4 && substr($path, -4) != '.php') {
            return self::getViewFilePath($path . '.php');
        }

        return false;
    }
    
}