<?php

namespace FSStack\Framebase\www\Renderers;

class Markdown implements IRenderer
{
    protected $path;
    protected $relative_path;
    protected $extension;
    public function __construct($path, $relative_path, $extension)
    {
        $this->path = $path;
        $this->relative_path = $relative_path;
        $this->extension = $extension;
    }
    public function render()
    {
        return \Michelf\MarkdownExtra::defaultTransform($this->get_file_parts()->content);
    }
    public function get_info()
    {
        $kvp = [];
        foreach (explode("\n", $this->get_file_parts()->info) as $line)
        {
            if ($line === '') {
                continue;
            }
            list($key, $value) = explode(":", $line);
            $key = strtolower(trim($key));
            $value = trim($value);
            $kvp[$key] = $value;
        }

        return $kvp;
    }

    public function get_file_parts()
    {
        $file = file_get_contents($this->path);
        $file = str_replace("\r\n", "\n", $file);
        $file = str_replace("\r", "\n", $file);

        $divider = "---\n";

        $info = '';
        $content = $file;

        if (substr($file, 0, strlen($divider)) === $divider)
        {
            $info = substr($file, strlen($divider));
            $info = substr($info, 0, strpos($info, $divider));

            $content = substr($file, strlen($info) + (2 * strlen($divider)));
        }

        return (Object)[
            'info' => $info,
            'content' => $content
        ];
    }
}
