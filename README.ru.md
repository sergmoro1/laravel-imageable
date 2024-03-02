# Laravel Imageable
Любая модель может иметь изображения. Например `Post`, `Good` или `User`. 
Пакет `Imageable` позволяет быстро и просто подключить загрузку изображений для любой модели.

![Imageable example](./20240228_example.png "Как выглядит работа Imageable")

## Preliminary remarks

### What does it mean - quickly and easily?
Обычно необходимо определить поле в таблице, если предполагается загружать одно изображение 
для одного экземпляра модели или таблицу, если изображений может быть загружено несколько.
Кроме этого нужно предусмотреть обработку загружаемых файлов, их сохранение, удаление.

Пакет `Imageable` позволяет упростить этот процесс. Добавлять необходимую функциональность становится
намного проще. Загрузку файлов, их обработку, сохранение в нужном хранилище берет на себя `Imageble`.

### How is it done?
Для хранения информации о всех загруженных файлах используется одна таблица. Понять, 
к какой модели относится тот или иной файл можно по значению двух полей:
`imageable_type` - в нём хранится название модели и `imageable_id` - это `ID` модели типа `imageable_type`.
Таким образом с конкретным экземпляром модели может быть связан как один, так и множество загруженных
файлов.

### API
Пакет `Imageable` использует `API` для загрузки, обработки и сохранения файлов. Поэтому, для работы пакета,
необходима аутентификация. По умолчанию, для прохождения тестов и быстрого старта, используется `basic` 
аутентификация без сохранения состояния.

Необходимый, для авторизации, параметр определяется в конфигурации приложения `config/app.php`.
```
    'credentials' => [
        'basic' => ('Basic ' . env('APP_BASIC')),
    ],
```
### Changes
`Tailwindcss` используется в `Laravel` по умолчанию, как и в пакете `Imageable`.
Так как `css`, `js`, `views` ресурсы публикуются после установки пакета, то есть возможность менять шаблоны и
настраивать элементы интерфейса.

### Limitation of use
На странице может располагаться только один компонент `<x-imageable-upload />`

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

## Publish resources
```
php artisan vendor:publish --provider="Sergmoro1\Imageable\ImageableServiceProvider"
```

## Usage
Для возможности загрузки изображений добавьте к модели трейты `HasStorage`, `HasImages`.
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
<x-imageable-upload :model="$post" :limit=1/>
```
Обратите внимание - в компоненте используется экземпляр модели, следовательно, он должен быть доступен в шаблоне.

Параметр `limit` определяет количество изображений, которое может быть загружено для модели. По умолчанию `0`, что значит можно загружать любое количество изображений.

Если на страницу уже подключены необходимые `css` и `js` файлы, а это возможно, если вы уже подключали `Imageable` для другой модели, то можно загружать изображения.

Если ещё нет, читайте дальше)

## CSS
В файл `resources/css/app.css`, после строк
```
@tailwind base;
@tailwind components;
@tailwind utilities;
```
добавьте следующую строку
```
@import "./imageable/upload.css";
```
Если необходимо, можно вносить коррективы в определение классов в файле `resources/css/imageable/upload.css` 
так как это копия аналогичного файла пакета.

## JS
Для загрузки изображений используюется `jQuery` плагин [simpleUpload](http://simpleupload.michaelcbrook.com/), 
поэтому необходимо сначала подключить `jQuery` библиотеку в файле `resources/js/app.js`.
```
window.$ = window.jQuery = require('jquery');
```

Следом необходимо загрузить сам плагин и обработчик.
```
require('jquery-simple-upload/simpleUpload');
require('./imageable/simpleUpload.js');
```

Если предполагается, что можно загрузить больше одного файла для конкретной модели, 
то можно добавить плагин [Sortable](https://github.com/SortableJS/Sortable) сортировки порядка изображений. 
Это важно, когда вы хотите менять порядок вывода изображений во frontend или хотите первое изображение использовать, 
как титульное. Сортировка производится перетаскиванием мышью.
```
import Sortable from 'sortablejs';
var el = document.querySelector('#upload ul.table');
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

## JS libs & plugins
Add in `dependencies` section of the `package.json` file two lines.
```
  "jquery-simple-upload": "^1.1.0",
  "sortablejs": "^1.15.1"
```
Then run in the console.
```
npm update
```

## CSS placement
Пакет `Imageable` использует `Google Material Icons`, поэтому необходимо подключить иконки на страницу.
Например в `views/layouts/app.blade.php`.

```
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="{{ url('css/app.css') }}" rel="stylesheet">
```

## JS placement
Так как для загрузки файлов пакет `Imageable` использует `API`, необходимо пройти аутентификацию.
По умолчанию пакет использует `basic` аутентификацию без сохранения состояния для выполнения тестов и 
быстрого старта использования пакета.
Подключите креды на странице, например в `views/layouts/app.blade.php`.

```
  <script>var app_credentials = '<?= config('app.credentials.basic'); ?>';</script>
  {{ $scripts }}
  <script src="/js/app.js"></script>
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
