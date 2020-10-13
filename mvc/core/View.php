<?php

namespace Core;

/**
 * Class View
 *
 * @package Core
 * @todo: comment
 */
class View
{

    public static function render (string $template, array $params = [], string $layout = '')
    {
        if ($layout === '') {
            $layout = Config::get('app.default-layout');
        }

        extract($params);

        $viewPath = __DIR__ . "/../resources/views";
        $templatePath = "$viewPath/templates/$template.php";
        require_once "$viewPath/layouts/$layout.php";
    }

}
