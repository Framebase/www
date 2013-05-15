<?php

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
        $real_path = implode('/', [CONTENT_DIR, $path]);

        // Set up the format processors

        $twig_render_inline = function($html, $relative)
        {
            $parts = explode('/', $relative);
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

            $full_html = $html;

            $rendered_title = null;
            if (substr(ltrim($html), 0, 4) === '<h1>') {
                $trimmed_html = ltrim($html);
                $rendered_title = substr($trimmed_html, 4, strpos($trimmed_html, '</h1>') - 4);
                $html = substr($html, strpos($html, '</h1>') + 5);
            }

            return $this->twig->render(
                $template,
                [
                    'path' => $relative,
                    'rendered' => $html,
                    'full_rendered' => $full_html,
                    'rendered_title' => $rendered_title
                ]
            );
        };

        $process_raw = function($file, $ext, $relative)
        {
            if ($ext === 'txt') {
                header('Content-type: text/plain');
            }
            return file_get_contents($file);
        };
        $process_semiraw = function($file, $ext, $relative) use ($twig_render_inline)
        {
            $html = file_get_contents($file);
            return $twig_render_inline($html, $relative);
        };
        $process_twig = function($file, $ext, $relative)
        {
            $ext_parts = explode('.', $ext);
            $relevant_ext = $ext_parts[0];
            if ($relevant_ext === 'txt') {
                header('Content-type: text/plain');
            }
            return $this->twig->render(
                implode('.', [$relative, $ext]),
                [
                    'path' => $relative
                ]
            );
        };
        $process_markdown = function($file, $ext, $relative) use ($twig_render_inline)
        {
            $html = \Michelf\MarkdownExtra::defaultTransform(file_get_contents($file));
            return $twig_render_inline($html, $relative);
        };

        // Format ordering
        $format_processors = [
            [
                'extensions' => ['txt', 'htm'],
                'processor' => $process_raw
            ],
            [
                'extensions' => ['html'],
                'processor' => $process_semiraw
            ],
            [
                'extensions' => ['markdown', 'md'],
                'processor' => $process_markdown
            ],
            [
                'extensions' => ['html.twig', 'txt.twig', 'twig'],
                'processor' => $process_twig
            ]
        ];


        foreach ($format_processors as $processor) {
            foreach ($processor['extensions'] as $ext) {
                $full_path = implode('.', [$real_path, $ext]);
                if (file_exists($full_path)) {
                    return call_user_func_array($processor['processor'], [$full_path, $ext, $path]);
                } else {
                    $full_path = implode('.', [$real_path . '/index', $ext]);
                    if (file_exists($full_path)) {
                        return call_user_func_array($processor['processor'], [$full_path, $ext, $path . '/index']);
                    }
                }
            }
        }

        throw new \CuteControllers\HttpError(404);
    }
}
