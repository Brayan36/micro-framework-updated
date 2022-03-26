<?php
namespace Core;

class View extends Response {

    static $dir = '../public/resources/views/';
    static $ext = '.bm.php';
    static $locals = [];

    private $view;
    private $attrs;

    /**
     *
     * @param String $view name od template HTML.
     */
    public function __construct($view)
    {
        $this->view = $view;
        $this->attrs = [];
        $this->with('helper', new Helper());
    }

    /**
     * @param String $html HTML of the view selected.
     * @return String HTML complete.
     */
    private function extend_view($html)
    {
        $pattern = '/@extends\s*\(?\s*[\'"]{1}(?<view>[A-Za-z0-9-_\/]+)[\'"]{1}\s*\)?/m';

        if (preg_match($pattern, $html, $matches)) {
            $html = preg_replace($pattern, '', $html);
            $parent_view = View::make($matches['view']);
            $parent_view->with('child', $html);
            $html = $parent_view->get();
        }
        return $html;
    }

    /**
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->attrs[$k] = $v;
            }
        } else {
            $this->attrs[$key] = $value;
        }
    }

    /**
     * @return String El HTML processed
     */
    public function get()
    {
        extract(self::$locals);
        extract($this->attrs);

        ob_start();
        include self::$dir . $this->view . self::$ext;
        $html = ob_get_contents();
        ob_end_clean();

        return $this->extend_view($html);
    }

    /**
     * Print View.
     */
    public function execute()
    {
        echo $this->get();
    }

    /**
     *
     * @param String $view Nombre de la pantilla HTML.
     * @return View Nueva vista.
     */
    static function make($view)
    {
        return new self($view);
    }

    /**
     *
     * @param mixed $key .
     * @param mixed $value
     */
    static function add_locals($key, $value)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::$locals[$k] = $v;
            }
        } else {
            self::$locals[$key] = $value;
        }
    }

    /**
     *
     * @param String $dir
     */
    static function set_dir($dir)
    {
        self::$dir = $dir;
    }

    /**
     *
     * @param String $ext
     */
    static function set_ext($ext)
    {
        self::$ext = $ext;
    }

}
