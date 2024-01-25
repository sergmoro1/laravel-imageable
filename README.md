# Laravel Imageable
Any model can have images. For example, an Article, a Product, or a User. The Imageable package allows you to enable image uploading for any model in a single way.

## Installation
Either run
```
composer require sergmoro1/laravel-imageable
```

or add to the `require` section of your `composer.json`.
```
"sergmoro1/laravel-imageable": "^1.0"
```

## Run migration
```
php artisan migrate
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
<x-imageable-upload :model="$post"/>
```
Place the necessary `css` and `js` files on the page and you can upload images.

## CSS
Copy the file from the `resources/css` directory of the package to the same project directory.

Add the following lines to the `resources/css/app.css` file:
```
@import url('./fileinput.css');
@import url('./simpleUpload.css');
```

## JS
Copy the `resources/js/simpleUpload.js` file of the package to the same project directory.

The jQuery plugin is used to upload images, so you need to add this library in the file `resources/js/app.js`.
```
window.$ = window.jQuery = require('jquery');
```

Then should be loaded the plugin and thier handler.
```
require('jquery-simple-upload/simpleUpload');
require('./simpleUpload.js');
```

It is assumed that you can upload more than one file for a specific model, so you can add a plugin for sorting of images. This is important when you want to change the order of image output in frontend or want to use the first image as the main image. Sorting is performed by drag & drop the mouse.
```
import Sortable from 'sortablejs';
el = document.querySelector('ul.table');
if (el) {
  var sortable = Sortable.create(el, {
    onEnd: function (evt) {
      axios.put('/api/images/' + evt.item.id, {
        oldIndex: evt.oldIndex,
        newIndex: evt.newIndex,
      })
      .then(response => {
        console.log(response);
      })
      .catch(err => {
        console.log(err);
      });
    },    
  });
}
```
Of course, the plugins used must be pre-installed.

The use of these plugins is not necessary, you can use any others. In this case, you need to rewrite the handler for uploaded images `resources\js\simpleUpload.js`.

## Finally
Run in the project directory:
```
npm run dev
```

## Configure model
By default, the parameters for storing images are set.
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

## Configure views
After installing the package, the component files are copied to the `resources\views\vendor\imageable` directory, where you can freely edit html markup, change styles and add/remove fields to describe each uploaded image.

The component for loading images can be inserted anywhere in the view. Not necessarily inside the `form` tag.

```
<x-imageable-upload :model="$post"/>
```

## Configure fields view
To change the list of additional fields of uploaded images, you need to edit the default values in the `addonDefaults` variable of the model, as mentioned above, and the `vendor\imageable\line\fields.blade.php` view, where it is necessary to define additional html markup. An example with possible fields and their values is given in the package in the file `fiealds-example.blade.php`.

If the list of additional fields varies from model to model, then the contents of the files `line\fields.blade.php` should be different and therefore the file names should be different. The model variable `$addonFieldsView` is used to specify the file name. The name can be anything, for example:
```
class Post extends Model
{
    use HasFactory, HasStorage, HasImages;

    protected $addonFieldsView = 'post-fields';
``` 

You need to copy the file `vendor\imageable\line\fields.blade.php `to the file `vendor\imageable\line\post-fields.blade.php` and make changes.
