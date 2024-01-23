<?php

namespace Sergmoro1\Imageable\Traits;

trait HasStorage
{
    protected $storage = [
        'disk' => 'public',
        'path' => '',
        'seperatly' => true,
    ];

    /**
     * Set storage information.
     * 
     * @param string $disk
     * @param string $path
     * @param bool $seperatly Should files for all models saved in a one directory or seperatly {$path}/{$id}
     */
    public function setStorage(string $disk, string $path, bool $seperatly)
    {
        $this->storage = [
            'disk' => $disk,
            'path' => $path,
            'seperatly' => $seperatly,
        ];
    }

    /**
     * Get the disk.
     */
    public function getDisk()
    {
        return $this->storage['disk'];
    }

    /**
     * Get full path to image.
     */
    public function getFullPath()
    {
        if (!$this->storage['path']) { 
            $this->storage['path'] = strtolower(class_basename(get_class($this)));
        }
        return $this->storage['path'] . ($this->storage['seperatly'] ? '/' . $this->id : '');
    }
}
