# Laravel Imageable
Любая модель может иметь изображения. Например Статья, Товар или Пользователь. Пакет Imageable позволяет подключить загрузку изображений для любой модели единым образом.

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

## Puplish views
```
php artisan vendor:publish --provider="Sergmoro1\Imageable\ImageableServiceProvider"
```

## Usage
Для возможности загрузки изображений добавьте `HasStorage`, `HasImages` трейты к модели.
```
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sergmoro1\Imageable\Traits\HasStorage;
use Sergmoro1\Imageable\Traits\HasImages;

class Post extends Model
{
    use HasFactory, HasStorage, HasImages;
```
Всавьте компонент для загрузки изображений в любое представление.
```
<x-imageable-upload :model="$post"/>
```
Подключите необходимые `css` и `js` файлы на страницу и можно загружать изображения (внимательно ознакомьтесь со всеми главами ниже, которые начинаются с CSS, JS).

## CSS
Скопируйте файл из каталога `resources/css` пакета в такой же каталог проекта.

Добавьте следующие строки в файл `resources/css/app.css`
```
@import url('./fileinput.css');
@import url('./simpleUpload.css');
```

## JS
Скопируйте файл `resources/js/simpleUpload.js` пакета в такой же каталог проекта.

Для загрузки изображений используюется jQuery плагин, поэтому необходимо подключить эту библиотеку в файле `resources/js/app.js`.
```
window.$ = window.jQuery = require('jquery');
```

Потом загрузить сам плагин и обработчик.
```
require('jquery-simple-upload/simpleUpload');
require('./simpleUpload.js');
```

Предполагается, что можно загрузить больше одного файла для конкретной модели, поэтому можно добавить плагин сортировки порядка изображений. Это важно, когда вы хотите менять порядок вывода изображений во frontend или хотите первое изображение использовать, как титульное. Сортировка производится перетаскиванием мышью.
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
Конечно, используемые плагины необходимо предварительно установить. 

## JS options

Разместите, в одном из blade-шаблонов, определение параметров для загрузки изображений для модели. Разместить определение необходимо до скрипта `app.js`.
Обратите внимание, что экземпляр модели (в примере ниже - `$post`) должен быть доступен в шаблоне.
```
    <x-slot name="scripts">
      <script>var uploadOptions = <?= $post->uploadOptions() ?>;</script>
    </x-slot>
```

## Finally
Выполните в каталоге проекта:
```
npm run dev
```

## Configure model
По умолчанию заданы параметры для хранения изображений.
```
'disk' => 'public',
'path' => '',
'seperatly' => true,
```
Пустой параметр `path`, означает, что на выбранном диске будет создан подкаталог с названием модели. Например `storage/app/public/post`. Параметр `seperatly` установленный в `true` означает, что для каждой модели будет создаваться отдельный каталог с `Id` модели в качестве названия. Например `storage/app/public/post/1`.
Параметры хранения можно поменять в соответствии принципами файлового хранилища Laravel методом `setStorage` класса `Sergmoro1\Imageable\Traits\HasStorage`.

В качестве дополнительного поля для каждого изображения задано только поле `caption`. Список полей, их порядок и значения по умолчанию тоже можно переопределить, используя метод `setAddonDefaults()` класса `Sergmoro1\Imageable\Traits\HasImages`.

Оба метода нужно вызывать в конструкторе соответствующей модели. Например определение полей:
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
После установки пакета файлы компонента копируются в каталог `resources\views\vendor\imageable`, где можно свободно редактировать html-разметку, менять стили и добавлять/удалять поля для описания каждого загружаемого изображения.

Компонент для загрузки изображений может быть вставлен в любом месте представления. Не обязательно внутри тега `form`.

```
<x-imageable-upload :model="$post"/>
```

## Configure fields view
Для изменения списка дополнительных полей загружемых изображений, необходимо отредактировать значения по умолчанию в переменной `addonDefaults` модели, как упоминалось выше, и представление `vendor\imageable\line\fields.blade.php`, где необходимо определить дополнительную html разметку. Пример с возможными полями и их значениями приведён в пакете в файле `fiealds-example.blade.php`.

Если список дополнительных полей разный от модели к модели, то и содержимое файлов `line\fields.blade.php` должно быть разным и, следовательно, названия файлов должны отличаться. Для указания имени файла используется переменная модели `$addonFieldsView`. Имя может быть любым, например:
```
class Post extends Model
{
    use HasFactory, HasStorage, HasImages;

    protected $addonFieldsView = 'post-fields';
``` 

Необходимо скопировать файл `vendor\imageable\line\fields.blade.php` в файл `vendor\imageable\line\post-fields.blade.php` и внести изменения.

## Tests
```
composer test
```
