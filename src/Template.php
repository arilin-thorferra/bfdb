<?php

declare(strict_types=1);

/**
 * Simple template rendering class
 */
class Template
{
    /**
     * Flags for htmlspecialchars() escaping
     */
    const ESCAPE_FLAGS = ENT_HTML5 | ENT_QUOTES | ENT_SUBSTITUTE;

    /**
     * Layout name and arguments
     */
    protected $layout = '';
    protected $layout_args = [];

    /**
     * Render a given template
     *
     * @param string $_template
     * @param array $_args
     * @return string
     */
    public function render(string $_template, array $_args = []): string
    {
        if (strpos($_template, '.') === false) {
            $_template .= '.phtml';
        }
        $_file = BASE_DIR . "templates/$_template";
        $content = $this->getContent($_file, $_args);
        if (!$this->layout) {
            return $content;
        }
        $layout = 'layouts/' . $this->layout;
        $args = $this->layout_args + $_args;
        $args['_content'] = $content;
        $this->resetLayout();
        return $this->render($layout, $args);
    }

    /**
     * Render a partial template
     * 
     * Partials are stored in the partials/ subdirectory, and should not
     * contain layouts.
     *
     * @param string $_partial
     * @param array $_args
     * @return string
     */
    public function partial(string $_partial, array $_args = []): void
    {
        $_file = BASE_DIR . "templates/partials/$_partial.phtml";
        echo $this->getContent($_file, $_args);
    }

    /**
     * Return the content of a file as a string, processing any PHP tags
     * within the file.
     *
     * @param string $_file
     * @param array $_args
     * @return string
     */
    private function getContent(string $_file, array $_args = []): string
    {
        if (!file_exists($_file)) {
            $error = "Template '$_file' not found";
            error_log($error);
            Context::debug($error);
            return '';
        }
        extract($_args, EXTR_SKIP);
        ob_start();
        include $_file;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Escape a string variable
     *
     * @param string $text
     * @return string
     */
    protected function e(string $text): string
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        return htmlspecialchars($text, self::ESCAPE_FLAGS, 'UTF-8');
    }

    /**
     * Set the layout and arguments for the next rendering
     *
     * @param string $layout
     * @param array $args
     * @return void
     */
    protected function layout(string $layout, array $args = []): void
    {
        $this->layout = $layout;
        $this->layout_args = $args;
    }

    /**
     * Reset the layout
     *
     * @return void
     */
    protected function resetLayout(): void
    {
        $this->layout = '';
        $this->layout_args = [];
    }
}
