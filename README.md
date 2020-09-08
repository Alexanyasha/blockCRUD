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

1. After installation run migrations 
```
php artisan migrate
```

2. Run publish command for scripts and styles (files will be in 'blockcrud' section of your public folder)
```
php artisan vendor:publish --tag=blockcrud --force
```

3. Add blocks section to your Backpack Adminpanel side menu (optional)
```
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('blocks') }}'><i class='nav-icon la la-cube'></i> <span>Blocks</span></a></li>"
```


### Laravel (рус.)

1. После установки аддона запустите миграции 
```
php artisan migrate
```

2. Запустите команду для публикации скриптов и стилей (они будут в разделе 'blockcrud' вашей публичной папки)
```
php artisan vendor:publish --tag=blockcrud --force
```

3. Добавьте пункт "Блоки" в меню вашей админпанели BackPack (необязательно)
```
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('blocks') }}'><i class='nav-icon la la-cube'></i> <span>Blocks</span></a></li>"
```


### Usage (english)

Now you can create and update new blocks in adminpanel section of your site. 
**Title** - name of block in adminpanel. 
**Slug** - name of block for using in Blade directive (i.e. block with slug 'my-block' will look like `@customblock('my-block')`). 
**Type** - type of block (only HTML available now). 
**Content** - HTML code of block. You can see live preview on the right with encapsulated hardcoded css-file /css/style.css. Applying custom styles will be set in config in future versions of add-on. 
**Active** - show on pages flag.  

After creating a block you can insert directive (`@customblock('my-block')`) in any place of your page content.


### Использование (рус.)

Теперь вы можете создавать и изменять новые блоки в админпанели вашего сайта. 
**Название** - название блока в админпанели. 
**Обозначение** - название блока для использования в директиве Blade (например, блок с обозначением 'my-block' будет выглядеть в коде как `@customblock('my-block')`). 
**Тип** - тип блока (на данный момент доступен только HTML). 
**Содержание** - HTML-код вашего блока. Справа находится живое превью вашего блока с изолированным css-файлом /css/style.css. Применение своих стилей будет реализовано в будущих версиях аддона. 
**Активен** - флаг показывать/не показывать на странице.  

После сохранения блока вы можете вставить его в любое место контента страницы с помощью директивы Blade (`@customblock('my-block')`). 



## LICENSE
GNU GPLv3  
Copyright Alexanyasha