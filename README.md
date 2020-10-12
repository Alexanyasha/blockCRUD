# Backpack\BlockCRUD for Laravel Backpack

An admin panel for block items on Laravel 7, using [Backpack\CRUD](https://github.com/Laravel-Backpack/crud). Add, edit blocks of code or model widgets to [Backpack\PageManager](https://github.com/Laravel-Backpack/pagemanager) pages. 

# BlockCRUD

## Requirements
- PHP >= 7.2
- Laravel >= 5.8
- BackPack >= 4.1
 
## Description
This addon allows you to add custom blocks on pages via Blade directive syntax i.e.
```
@customblock('my-custom-block')
```


## Installation

### Composer
```
composer require designcoda/backpack-blockcrud
```

### Laravel (english)

0. After installation run migrations 
```
php artisan migrate
```

1. Publish main add-on file
```
php artisan vendor:publish --provider="Backpack\BlockCRUD\BlockCRUDServiceProvider"
```

2. Run publish command for scripts and styles (files will be in 'blockcrud' section of your public folder) (optional). Run these commands after every add-on upgrade
```
php artisan vendor:publish --tag=blockcrud --force
php artisan view:clear
```

3. Add blocks section to your Backpack Adminpanel side menu (optional)
```
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('blocks') }}'><i class='nav-icon la la-cube'></i> <span>Blocks</span></a></li>"
```


### Laravel (рус.)

0. После установки аддона запустите миграции 
```
php artisan migrate
```

1. Опубликуйте основной файл аддона командой
```
php artisan vendor:publish --provider="Backpack\BlockCRUD\BlockCRUDServiceProvider"
```

2. Запустите команду для публикации скриптов и стилей (они будут в разделе 'blockcrud' вашей публичной папки) (необязательно). Эти же команды надо запускать после обновления версии аддона  
```
php artisan vendor:publish --tag=blockcrud --force
php artisan view:clear
```

3. Добавьте пункт "Блоки" в меню вашей админпанели BackPack (необязательно)
```
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('blocks') }}'><i class='nav-icon la la-cube'></i> <span>Blocks</span></a></li>"
```  


![alt text](http://dl4.joxi.net/drive/2020/09/08/0003/2602/219690/90/ce45fd6f72.png "Live preview")  


### Usage (english)

Now you can create and update new blocks in adminpanel section of your site.  
**Title** - name of block in adminpanel.  
**Slug** - name of block for using in Blade directive (i.e. block with slug 'my-block' will look like `@customblock('my-block')`).  
**Type** - type of block (HTML, Entity or Template).  
**Content** (visible if type is HTML) - HTML code of block. You can see live preview on the right with encapsulated hardcoded css-file /css/style.css. Applying custom styles will be set in config in future versions of add-on.  
**Entity** (visible if type is Entity) - list of all available models in your project (folder App\Models temporarily is hardcoded for searching). You can manage block from Model itself by adding new model properties. For example, in file App\Models\User:  
```
public $blockcrud_title = 'Employees'; //Model title for list in block editing
public $blockcrud_template = 'blocks.template'; //Template for show instead of HTML code in content. Model items will be available in this template as $items
public $blockcrud_ignore = true; //Ignore this model in block entities list
```
**Template** (visible if type is Template) - Blade template for include (temporarily readonly field). You can use any standard features of Blade and @customblock directive as well.  
**Active** - show on pages flag.  

After creating a block you can insert directive (`@customblock('my-block')`) in any place of your page content. If you use code from database (for example, in PageManager), wrap your code in Blade templates with directive
```
@pageblocks ($page->content)
```
For Entity you can use scope parameter  
```
@customblock('my-block', 'active')
```
For Entity and Template types you can use array with parameters  
```
@customblock('my-block', 'active', ['parameter' => 'value'])
@customblock('my-block', ['parameter' => 'value'])
```

### Использование (рус.)

Теперь вы можете создавать и изменять новые блоки в админпанели вашего сайта.  
**Название** - название блока в админпанели.  
**Обозначение** - название блока для использования в директиве Blade (например, блок с обозначением 'my-block' будет выглядеть в коде как `@customblock('my-block')`).  
**Тип** - тип блока (HTML, Сущность или Шаблон).  
**Содержание** (показывается, если выбран тип HTML) - HTML-код вашего блока. Справа находится живое превью вашего блока с изолированным css-файлом /css/style.css. Применение своих стилей будет реализовано в будущих версиях аддона. 
**Сущность** (показывается, если выбран тип Сущность) - список всех доступных моделей вашего проекта (временно для поиска захардкожена только папка App\Models). Свойствами блока можно управлять прямо из файла модели, добавляя новые свойства. Например, в файл App\Models\User:  
```
public $blockcrud_title = 'Employees'; //Человеческое название для списка моделей в редактировании блока
public $blockcrud_template = 'blocks.template'; //Файл шаблона вместо HTML-кода в содержании. Объекты модели будут доступны в переменной $items
public $blockcrud_ignore = true; //Игнорировать модель в списке сущностей в редактировании блока
``` 
**Шаблон** (показывается, если выбран тип Шаблон) - шаблон Blade (поле временно только для чтения). Можно использовать весь функционал Blade и директиву @customblock.  
**Активен** - флаг показывать/не показывать на странице.  

После сохранения блока вы можете вставить его в любое место контента страницы с помощью директивы Blade (`@customblock('my-block')`). Если вы используете код из базы данных (например, из аддона PageManager), оберните код в дополнительную директиву Blade
```
@pageblocks ($page->content)
```
В Сущности можно использовать параметр scope  
```
@customblock('my-block', 'active')
```
Для типов Сущность и Шаблон вы можете указать массив параметров  
```
@customblock('my-block', 'active', ['parameter' => 'value'])
@customblock('my-block', ['parameter' => 'value'])
```


## LICENSE
GNU GPLv3  
Copyright Alexanyasha