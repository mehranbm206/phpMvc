<?php
require_once "app.php";

/**
 * Created by PhpStorm.
 * User: Programmer
 * Date: 12/29/2020
 * Time: 11:13 PM
 */
class Router
{
    private static $_routes = [];
    private static $_controller="index";
    private static $_action="index";
    private static $_param;

    public function __construct()
    {
        self::route();
    }

//    manage titles page Site
    public static function getController()
    {
        if (isset($_SESSION['title_url'])) {
            $page = $_SESSION["title_url"];
        } else {
            $page = "index";
        }
        /*set page title*/
        switch ($page) {
            case "index":
                $title = "خوش آمدید | نامبر وان بلوچ";
                return $title;
                break;
            case "continuePost":
                $title = "دانلود و پخش آنلاین | نامبر وان بلوچ";
                return $title;
                break;
            case "contactMe":
                $title = " تماس با ما | نامبر وان بلوچ";
                return $title;
                break;
            case "track":
                $title = " اجرای موزیک | نامبر وان بلوچ";
                return $title;
                break;
            default:
                $title = "نامبروان بلوچ";
                return $title;
        }
    }

    public static function isPageIndex()
    {
        if (isset($_SESSION['title_url'])) {
            $page = $_SESSION["title_url"];
            if ($page == "index")
                return true;
        }
        return false;
    }

//    manage urls
    public static function parseUrl($url)
    {
        filter_var($url, FILTER_SANITIZE_URL);
        $url = rtrim($url, "/");
        $url = explode("/", $url);
        return $url;
    }

    public static function register($route, $routeAction)
    {
        self::$_routes[$route] = $routeAction;
    }

    public static function route()
    {
        $url = isset($_GET['url']) && $_GET['url'] != '' ? $_GET['url'] : 'index';
        $resUrl = self::parseUrl($url);
        if (isset(self::$_routes[$resUrl[0]])) {
            $urlArray = explode("/", self::$_routes[$resUrl[0]]);
            self::$_controller = $urlArray[0];
            self::$_action = $urlArray[1];
            unset($urlArray[0]);
            unset($urlArray[1]);
            self::$_param = array_values($urlArray);
            $controllersUrl = "controllers/" . self::$_controller . "_controller.php";
            if (file_exists($controllersUrl)) {
                require $controllersUrl;
                $object = new self::$_controller;
                $object->model(self::$_controller);
                $_SESSION['title_url'] = $url;
                if (method_exists($object, self::$_action)) {
                    call_user_func_array([$object, self::$_action], self::$_param);
                 } else {
                    echo "Error Not Found";
                }
            }
        }else{
            self::defaultRoute();
        }
    }

    public static function defaultRoute()
    {
        self::$_controller = isset($_GET['url']) && $_GET['url'] != '' ? $_GET['url'] : 'index';
        $resUrl = self::parseUrl(self::$_controller);
        self::$_controller = $resUrl[0];
        if (isset($resUrl['1'])) {
            self::$_action = $resUrl['1'];
        }
        unset($resUrl[0]);
        unset($resUrl[1]);
        $params =self::$_param = array_values($resUrl);
        $controllersUrl = "controllers/" . self::$_controller. "_controller.php";
        if (file_exists($controllersUrl)) {
            require $controllersUrl;
            $object = new self::$_controller();
            $object->model(self::$_controller);
            $_SESSION['title_url'] = self::$_controller;
            if (method_exists($object, self::$_action)) {
                call_user_func_array([$object, self::$_action], $params);
             } else {
                echo "Error Not Found";
            }
        } else {
            echo "Error Not Found";
        }
    }
//    manage urls
}