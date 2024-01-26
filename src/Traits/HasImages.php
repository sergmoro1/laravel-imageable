<?php

namespace Sergmoro1\Imageable\Traits;

use Illuminate\Database\Eloquent\Model;
use Sergmoro1\Imageable\Models\Image;

/**
 * Images and addon fields for the model
 * 
 * @property Image[] $images
 */
trait HasImages
{
    /**
     * Image additional attributes.
     * 
     * @var array ['attribute' => default_value]
     */
    protected $addonDefaults = [
        'caption' => '',
    ];

    /**
     * Deleting all images associated with the model 
     * after deleting the model itself.
     */
    public static function bootHasImages(): void
    {
        static::deleted(fn (Model $model) =>
            Image::where([
                'imageable_type' => get_class(),
                'imageable_id' => $model->id,
            ])->delete(),
        );
    }

    /**
     * Get the images of the model.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    /**
     * Prepare options for uploading files.
     * 
     * @return string JSON array with uploading options
     */
    public function uploadOptions(): string
    {
        return json_encode([
            'data' => [
                'imageable_type' => get_class(), 
                'imageable_id' => $this->id,
            ],
            'image' => [
                'tools' => view('vendor.imageable.line.tools')->render(),
                'line' => view('vendor.imageable.line.' . $this->getAddonFieldsView(), [
                    'defaults' => $this->addonDefaults
                ])->render(),
                'buttons' => view('vendor.imageable.line.buttons')->render(),
            ],
            'fields' => array_keys($this->addonDefaults),
        ]);
    }

    public function getAddonFieldsView()
    {
        return empty($this->addonFieldsView) ? 'fields' : $this->addonFieldsView;
    }
    
    /**
     * Get addon Image fields.
     * 
     * @return array addon fields and thier defaults
     */
    public function getAddonDefaults(): array
    {
        return $this->addonDefaults;
    }

    /**
     * Set addon Image fields.
     * 
     * @var array $addon fields and thier defaults
     */
    public function setAddonDefaults($addon)
    {
        $this->addonDefaults = $addon;
    }
}
