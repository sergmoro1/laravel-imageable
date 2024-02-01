<?php

namespace Sergmoro1\Imageable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

/**
 * Image model class
 *
 * @param integer   $id
 * @param integer   $imageable_id
 * @param string    $parent_type
 * @param string    $disk
 * @param string    $url
 * @param string    $original
 * @param integer   $size
 * @param string    $mime_type
 * @param json      $addons
 * @param integer   $position
 * @param timestamp $created_at
 * @param timestamp $updated_at
 *
 * @property Model $imageable
 */
class Image extends Model
{
    /**
     * Thumbnail.
     *
     * @var string
     */
    protected $thumbnail;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable= [
        'imageable_type',
        'imageable_id',
        'disk',
        'url',
        'size',
        'mime_type',
        'original',
        'position',
        'addons',
    ];
    
    /**
     * The attributes that are auto assignable.
     *
     * @var string[]
     */
    protected $hidden = [
        'created_at', 
        'updated_at',
    ];

    protected $casts = [
        'addons' => 'object',
    ];

    /**
     * Get parent model for image.
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Make thumbnail.
     * 
     * @param int $width thumbnail
     * @param int $height thumbnail
     * @return string
     */
    public function makeThumbnail(int $width = 100, int $height = 100): string
    {
        $path = base_path() . '/storage/app/public/';
        $imagine = new Imagine();
        $this->setThumbnail();
        $imagine->open($path . $this->url)
            ->thumbnail(new Box($width, $height))
            ->save($path . $this->thumbnail, ['flatten' => false]);
        return Storage::url($this->thumbnail);
    }
    
    /**
     * Set thumbnail.
     */
    public function setThumbnail(): void
    {
        if (!$this->thumbnail) {
            $this->thumbnail = substr($this->url, 0, strpos($this->url, '.')) . '.thumb.png';
        }
    }

    /**
     * Get url.
     * 
     * @return string
     */
    public function getUrl(): string
    {
        return Storage::url($this->url);
    }

    /**
     * Get thumbnail url.
     * 
     * @param bool $includeStorage
     * @return string
     */
    public function getThumbnailUrl($includeStorage = true): string
    {
        $this->setThumbnail();
        return $includeStorage ? Storage::url($this->thumbnail) : $this->thumbnail;
    }

    /**
     * Delete all about image - delete all files associated with image and image information itself.
     * 
     * @param string $disk
     */
    public function deleteAllAbout(string $disk): void
    {
        // delete all files associated with image
        Storage::disk($disk)->delete($this->url);
        Storage::disk($disk)->delete($this->getThumbnailUrl(false));
        // delete image information
        $this->delete();
    }
}
