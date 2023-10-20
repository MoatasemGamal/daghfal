<?php

namespace Core\Utility;

class View
{
    private string $view = "";
    private array $data = [];
    public function __construct(string $view, array $data = [])
    {
        $this->data = $data;
        $this->view = $this->loadViewOrFail($view, $data);
    }

    public function render(): string
    {
        $result = "";
        //check if view extends layout
        $pattern = "/@extends\('([^']{3,50})'\)/";
        if (preg_match($pattern, $this->view, $matches)) {
            if (!$this->loadLayout($matches[1]))
                return $this->view;

            $result = $this->loadLayout($matches[1]);

            $pattern = "/(?:@section\('([^']{3,50})'\)([\w\W]*)@endSection)|(?:@section\('([^']+)'\s*,\s*'([^']+)'\))/";
            if (preg_match_all($pattern, $this->view, $matches)) {
                $sections = array_merge(array_combine($matches[1], $matches[2]), array_combine($matches[3], $matches[4]));
                foreach ($sections as $key => $value) {
                    $result = str_replace("@yield('$key')", $value, $result);
                }
                $result = preg_replace("/@yield\('[^']*'\)/", "", $result);
            }
        } else
            $result = $this->view;
        return $result;
    }






    private function loadViewOrFail($viewName, $data = []): string
    {
        $path = VIEWS_PATH . str_replace(".", "/", $viewName) . ".view.php";
        if (!file_exists($path))
            return throw new \Exception("view not fount!", 404);

        ob_start();
        extract($data);
        include $path;
        return ob_get_clean();
    }
    private function loadLayout($layoutName)
    {
        $path = VIEWS_PATH . "layouts/" . str_replace(".", "/", $layoutName) . ".layout.php";
        if (!file_exists($path))
            return false;

        ob_start();
        include $path;
        return ob_get_clean();
    }
}
