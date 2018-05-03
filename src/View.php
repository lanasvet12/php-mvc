<?php
namespace PhpMvc;

/**
 * Represents the properties and methods that are needed to render a view.
 */
final class View {

    /**
     * Defines ViewContext.
     * 
     * @var ViewContext
     */
    private static $viewContext;

    /**
     * Gets the context of the current view.
     * 
     * @return ViewContext
     */
    public static function getViewContext() {
        return self::$viewContext;
    }

    /**
     * Sets layout.
     * 
     * @param string $path The layout file name in the shared folder or full path to layout file.
     * 
     * @return void
     */
    public static function setLayout($path) {
        self::$viewContext->layout = $path;
    }

    /**
     * Sets page title.
     * 
     * @return void
     */
    public static function setTitle($title) {
        self::$viewContext->title = $title;
    }

    /**
     * Injects model to state.
     * 
     * @param mixed &$model Model to injection.
     * 
     * @return void
     */
    public static function injectModel(&$model) {
        $actionResult = self::$viewContext->actionResult;

        if (!empty($actionResult)) {
            if ($actionResult instanceof ViewResult && !empty($actionResult->model)) {
                $model = $actionResult->model;
            }
        }
    }

    /**
     * Sets data to view.
     * 
     * @param string $key Key associated with the data entry.
     * @param string $value The value to set.
     * 
     * @return void
     */
    public static function setData($key, $value) {
        self::$viewContext->viewData[$key] = $value;
    }

    /**
     * Gets the data with the specified key.
     * If the specified key does not exist, function returns null.
     * If no key is specified, returns all data.
     * 
     * @param string $key The key to get the data.
     * 
     * @return mixed|array|null
     */
    public static function getData($key = null) {
        if (!isset($key)) {
            return self::$viewContext->viewData;
        }
        else {
            return isset(self::$viewContext->viewData[$key]) ? self::$viewContext->viewData[$key] : null;
        }
    }

    /**
     * Gets model.
     * 
     * @return mixed|null
     */
    public static function getModel() {
        return self::$viewContext->model;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public static function getModelState() {
        return self::$viewContext->getModelState();
    }

    /**
     * Gets view file name.
     * 
     * @return string
     */
    public static function getViewFile() {
        return self::$viewContext->viewFile;
    }

}