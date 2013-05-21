<?php

namespace FSStack\Framebase\www\Renderers;

interface IRenderer
{
    public function __construct($path, $relative_path, $extension);
    public function render();
    public function get_info();
}
