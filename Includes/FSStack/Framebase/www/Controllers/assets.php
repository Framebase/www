<?php
define('SASS_DIR', implode('/', ['', 'var', 'www', 'assets', 'sass']));
define('SASS_CACHE_DIR', implode('/', ['', 'var', 'www', 'assets', 'sass_cache']));
define('SASS_CACHE_DIR', implode('/', ['', 'var', 'www', 'assets', 'framebase-js_cache']));
class assets_controller
{
    use \CuteControllers\Base\Rest;

    public function __get_framebase_js($file)
    {
        if ($file === 'framebase') {
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
            header("Cache-control: public, max-age=3600, must-revalidate");
        }

        header("Content-type: text/javascript");
        echo file_get_contents('http://js.fss.int/' . $file . '.js');
    }

    public function __get_css()
    {
        header("Content-type: text/css");
        $path = implode('/', func_get_args());
        $path = ltrim($path, '/');
        $path = str_replace('.', '', $path);

        $source_dir = implode('/', [SASS_DIR, $path]);
        $cache_dir = implode('/', [SASS_CACHE_DIR, $path]);

        $this->check_cache();

        $cache_file = implode('.', [$cache_dir, 'css']);

        if (!file_exists($cache_file)) {

            // Check if there even is a source file for this!
            $extensions = ['scss', 'sass', 'css'];
            $source_file = null;
            foreach ($extensions as $ext) {
                $source_file_test = implode('.', [$source_dir, $ext]);
                if (file_exists($source_file_test)) {
                    $source_file = $source_file_test;
                    break;
                }
            }
            if ($source_file === null) {
                throw new \CuteControllers\HttpError(404);
            }

            try {
                // Render the source file
                require_once(INCLUDES_DIR . '/phpsass/SassParser.php');
                $sass = new SassParser(['style'=>'nested']);
                $generated_css = $sass->toCss($source_file);

                // Store it in the cache
                file_put_contents($cache_file, $generated_css);
            } catch (\Exception $ex) {
                echo "/*\n" . $ex->getMessage() . "\n*/";
                exit;
            }

        }

        echo "/* Copyright (c) FS Stack, Inc */\n";
        echo file_get_contents($cache_file);
    }

    protected function check_cache()
    {
        $last_mod = $this->get_most_recently_modified_time();
        $lmod_file = SASS_CACHE_DIR . '/last_mod.txt';
        if (!file_exists($lmod_file) || $last_mod != file_get_contents($lmod_file)) {
            $this->clear_directory(SASS_CACHE_DIR);
            file_put_contents($lmod_file, $last_mod);
        }
    }

    private function clear_directory($dir)
    {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = implode('/', [$dir, $file]);
                if(is_dir($path)) {
                    $this->clear_directory($path);
                    rmdir($path);
                } else if (is_file($path)) {
                    unlink($path);
                }
            }
            closedir($dh);
        }
    }

    private function get_most_recently_modified_time($dir = SASS_DIR, $max = 0) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file === '.' || $file === '..' ||
                    ($dir === SASS_DIR && $file === 'index.html') /* So git will keep the folder */) {
                    continue;
                }
                $path = implode('/', [$dir, $file]);
                if(is_dir($path)) {
                    $sub_max = $this->get_most_recently_modified_time($path, $max);
                    if ($sub_max > $max) {
                        $max = $sub_max;
                    }
                } else if (is_file($path)) {
                    if (filemtime($path) > $max) {
                        $max = filemtime($path);
                    }
                }
            }
            closedir($dh);
        }

        return $max;
    }
}
