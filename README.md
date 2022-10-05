Google Tag Manager extension for the Yii2 framework
===================================================
[![Latest Stable Version](https://poser.pugx.org/ezoterik/yii2-google-tag-manager/v/stable)](https://packagist.org/packages/ezoterik/yii2-google-tag-manager)
[![Total Downloads](https://poser.pugx.org/ezoterik/yii2-google-tag-manager/downloads)](https://packagist.org/packages/ezoterik/yii2-google-tag-manager)
[![Latest Unstable Version](https://poser.pugx.org/ezoterik/yii2-google-tag-manager/v/unstable)](https://packagist.org/packages/ezoterik/yii2-google-tag-manager)
[![License](https://poser.pugx.org/ezoterik/yii2-google-tag-manager/license)](https://packagist.org/packages/ezoterik/yii2-google-tag-manager)

Integration Google Tag Manager in your application

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ezoterik/yii2-google-tag-manager "*"
```

or add

```
"ezoterik/yii2-google-tag-manager": "*"
```

to the require section of your `composer.json` file.

Setup
-----

Add this code in your *@app/config/main.php* config file  with the other previous config:
 ```php
 'bootstrap' => ['googleTagManager'],
 'components' => [
     'googleTagManager' => [
         'class' => 'ezoterik\googleTagManager\GoogleTagManager',
         'tagManagerId' => 'GOOGLE_TAG_MANAGER_ID', //Your Google Tag Manager ID without "GTM-" prefix
     ],
 ],
 ```

Usage
-----

You can generate events:

 ```php
 Yii::$app->googleTagManager->dataLayerPushItemDelay('event', 'example_event');
 ```

Or push event as object:

```php
Yii::$app->googleTagManager->dataLayerPushItem(null, (object)[
    'event' => 'view_item',
    'ecommerce' => (object)[
        'currency' => 'UAH',
        'value' => '500',
        'items' => [
            (object)[
                'item_name' => 'Product name',
                'item_id' => '12345',
                'price' => '500',
                'item_brand' => 'Analytics',
                'item_category' => 'Very Good Category',
                'item_category2' => 'Very Good Category 2',
                'item_category3' => 'Very Good Category 3',
                'item_category4' => 'Very Good Category 4',
                'item_variant' => 'full',
                'item_list_name' => 'sales',
                'item_list_id' => 'sales2022',
            ],
        ],
    ],
]);
```
