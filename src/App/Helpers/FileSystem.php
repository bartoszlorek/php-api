<?php

namespace App\Helpers;

use Interop\Container\ContainerInterface;
use Slim\Http\UploadedFile;

class FileSystem {

    protected $uploads;

    public function __construct(ContainerInterface $container) {
        $this->uploads = $container->get('settings')['uploads'];
    }

    public static function path(array $segments) {
        return implode(DS, array_filter($segments));
    }

    public static function normalizeFilename(string $filename) {
        $info = pathinfo($filename);
        $name = preg_replace('/[\s_\-\.]+/', '-', strtolower($info['filename']));
        return sprintf('%s-%s.%s', date('ymdHis'), $name, $info['extension']);
    }

    public function append(UploadedFile $file, string $path = '', bool $normalize = true) {
        $filepath = $this->path([$this->uploads, $path]);
        $filename = $file->getClientFilename();

        if (!file_exists($filepath)) {
            mkdir($filepath, 0777, true);
        }
        if ($normalize) {
            $filename = $this->normalizeFilename($filename);
        }
        $file->moveTo($this->path([$filepath, $filename]));
        return $this->path([$path, $filename]);
    }

    public function unlink($filename) {
        return unlink($this->path([$this->uploads, $filename]));
    }

}
