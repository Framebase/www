<?php

use \FSStack\Framebase\www\Renderers;

define('CONTENT_DIR', implode('/', ['', 'var', 'www', 'content']));
class main_controller
{
    use \CuteControllers\Base\Rest;

    protected $twig;

    public function __before()
    {
        // Set-up Twig
        $loader = new Twig_Loader_Filesystem(CONTENT_DIR);
        $this->twig = new Twig_Environment($loader, []);

        // We'll load the files from here!
        $path = $this->request->path;

        // Remove leading slashes to prevent possible directory traversal
        $path = ltrim($path, "/");

        // Remove dots because we're going to ignore the extension
        $path = str_replace('.', '', $path);

        echo $this->process_request($path);
        exit;
    }

    private function process_request($path)
    {
        $file_path = $this->get_file_path($path);
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);

        if ($ext === 'twig') {
            $x = substr($file_path, strlen(CONTENT_DIR));
            return $this->twig->render($x, [
                'path' => $path,
                'full_path' => $file_path
            ]);
        } else {
            $renderer = $this->get_renderer($path);
            $template = $this->get_template_url($path);

            $rendered = $renderer->render();
            $file_info = $renderer->get_info();

            return $this->twig->render($template, [
                'path' => $path,
                'full_path' => $file_path,
                'rendered' => $rendered,
                'info' => $file_info
            ]);
        }

        throw new \CuteControllers\HttpError(404);
    }

    public function get_template_url($relative_path)
    {
        $parts = explode('/', $relative_path);
        if ($parts[count($parts) - 1] !== '') {
            $parts[] = '';
        }
        $template = 'templates/rendered.html.twig';
        while(count($parts) > 0) {
            array_pop($parts);
            array_push($parts, 'template.html.twig');
            $pass_tpl_path = implode('/', $parts);
            $check_tpl_path = implode('/', [CONTENT_DIR, $pass_tpl_path]);
            if (file_exists($check_tpl_path)) {
                $template = $pass_tpl_path;
                $parts = [];
            }
            array_pop($parts);
        }

        return $template;
    }

    protected $processors = [
        'Markdown' => ['md', 'markdown'],
        'Raw' => ['htm', 'html', 'txt']
    ];

    public function get_file_path($relative_path)
    {
        $real_path = implode('/', [CONTENT_DIR, $relative_path]);
        $extensions = ['md', 'markdown', 'html', 'htm', 'txt', 'html.twig'];

        foreach ($extensions as $ext) {
            $test_full_path = implode('.', [$real_path, $ext]);

            if (file_exists($test_full_path)) {
                return $test_full_path;
            } else {
                $test_full_path = implode('.', [$real_path . '/index', $ext]);
                if (file_exists($test_full_path)) {
                    return $test_full_path;
                }
            }
        }
    }

    public function get_renderer($relative_path)
    {
        $file_path = $this->get_file_path($relative_path);
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);

        foreach ($this->processors as $processor=>$exts) {
            if (in_array($ext, $exts)) {
                $class = "\\FSStack\\Framebase\\www\\Renderers\\$processor";
                return new $class($file_path, $relative_path, $ext);
            }
        }

        throw new \Exception("Processor not found.");
    }
}
