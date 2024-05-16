# Laravel Imageable
The `Imageable` package allows quickly and easily to enable image uploading for any model.
Uploading files, processing them, and saving them to the desired storage is taken over by `Imageable`.

Each image may have some descriptive fields associated with it, such as a caption, category, date, or something like that. By default, only caption are accepted, but any fields can be set. See the configuration.

## API
The `Imageable` package uses the `API` to download, process and save files. Therefore,
authentication is required for the package to work. By default, `basic` stateless authentication is used to pass tests and make to a quick start.

## Changes
`Tailwindcss` is used in `Imageable` by default, as in the `Laravel`.
Since `css`, `js`, `views` resources are published after installing the package, it is possible to change templates and
customize interface elements.

## Limitation of use
Only one component `<x-imageable-upload />` can be placed on the page.

## Installation
```
composer require sergmoro1/laravel-imageable
```

## Run migration
```
php artisan migrate
```

## Publish resources
```
php artisan vendor:publish --provider="Sergmoro1\Imageable\ImageableServiceProvider"
```

## Usage
For ability images uploading add `HasStorage`, `HasImages` traits to the model.
```
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sergmoro1\Imageable\Traits\HasStorage;
use Sergmoro1\Imageable\Traits\HasImages;

class Post extends Model
{
    use HasFactory, HasStorage, HasImages;
```
Insert upload component in a view.
```
<x-imageable-upload :model="$post" :limit=1/>
```
Please note that the component uses an instance of the model, therefore, it must be available in the template.

The `limit` parameter defines the number of images that can be uploaded for the model. The default is `0`, which means you can upload any number of images.

If the necessary `css` and `js` files are already connected to the page, and this is possible if you have already connected `Imageable` for another model, then you can upload images.

### JS libs & plugins
Add in `dependencies` section of the `package.json` file two lines
```
  "sortablejs": "^1.15.1"
```
Then run in the console
```
npm update
```

### CSS
In a file `resources/css/app.css`, after lines
```
@tailwind base;
@tailwind components;
@tailwind utilities;
```
add line
```
@import "./imageable/upload.css";
```
If necessary, you can make adjustments to the classes definition in the `resources/css/imageable/upload.css` file 
since this is a copy of a similar package file.

### JS
To upload images and work with additional fields related to images, add two lines to app.js
```
require('./imageable/axiosUpload.js');
require('./imageable/imageLine.js');
```

If you want to upload more than one file for a specific model, you can add a plugin for sorting of images [Sortable](https://github.com/SortableJS/Sortable). This is important when you want to change the order of image output in frontend or want to use the first image as the main image. Sorting is performed by drag & drop the mouse. Add code below for images sorting.
```
require('./imageable/sortable.js');
```

### CSS placement
The `Imageable` package uses `Google Material Icons`, so you need to connect the icons to the page.
For example in `views/layouts/app.blade.php `.

```
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="{{ url('css/app.css') }}" rel="stylesheet">
```

### JS placement
Since the `Imageable` package uses the `API` to upload files, authentication is required.
By default, the package uses `basic` stateless authentication to run tests and quickly start using the package.
Place the `app_credentials` variable on the page. The variant of receiving credentials is yours.

```
  <script>var app_credentials = '<?= config('app.credentials.basic') ?>';</script>
  {{ $scripts }}
  <script src="/js/app.js"></script>
```

### Finally
Run in the project directory
```
npm run dev
```

## Configuration
The storage parameters, the view of line associated with the uploaded file, the number and values of additional parameters can be changed.

### Model
By default, the parameters for storing images are set
```
'disk' => 'public',
'path' => '',
'seperatly' => true,
```
An empty `path` parameter means that a subdirectory with the model name will be created on the selected disk. For example, `storage/app/public/post`. The `separately` parameter set to `true` means that a separate directory will be created for each model with the `Id` of the model as the name. For example, `storage/app/public/post/1`.
Storage parameters can be changed according to the principles of Laravel file storage using the `setStorage` method of the `Sergmoro1\Imageable\Traits\HasStorage` class.

Only the `caption` field is set as an additional field for each image. The list of fields, their order and default values can also be redefined using the `setAddonDefaults()` method of the class `Sergmoro1\Imageable\Traits\HasImages`.

Both methods must be called in the constructor of the corresponding model. For example, defining fields:
```
class Post extends Model
{
    use HasFactory, HasStorage, HasImages;
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->setAddonsDefaults([
            'year' => '',
            'category' => 'home',
            'caption' => '',
        ]);
    }
```

### Views
After installing and publishing the package, the component files are copied to the `resources\views\vendor\imageable` directory, where you can freely edit html markup, change styles and add/remove fields to describe each uploaded image.

### Addon fields
To change the list of additional fields of uploaded images, you need to edit the default values in the `addonDefaults` variable of the model, as mentioned above, and the `vendor\imageable\line\fields.blade.php` view, where it is necessary to define additional html markup. An example with possible fields and their values is given in the package in the file `fiealds-example.blade.php`.

If the list of additional fields varies from model to model, then the contents of the files `line\fields.blade.php` should be different and therefore the file names should be different. The model variable `$addonFieldsView` is used to specify the file name. The name can be anything, for example:
```
class Post extends Model
{
    use HasFactory, HasStorage, HasImages;

    protected $addonFieldsView = 'post-fields';
``` 

You need to copy the file `vendor\imageable\line\fields.blade.php `to the file `vendor\imageable\line\post-fields.blade.php` and make changes.

## Tests
```
composer test
```
