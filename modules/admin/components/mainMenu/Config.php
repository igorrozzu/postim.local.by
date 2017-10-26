<?php
namespace app\modules\admin\components\mainMenu;

class Config{

    public static function getConfig(){
        return [
            [
                'name' => 'Добавить контент',
                'icon' => '',
                'url' => '',
                'under' => [
                    [
                        'name' => 'Добавить новость',
                        'url' => '/admin/add-news'
                    ],
                    [
                        'name' => 'Добавить место\\Категорию',
                        'url' => '/'
                    ],

                ],
            ],
            [
                'name' => 'Модерация',
                'icon' => '',
                'url' => '',
                'under' => [
                    [
                        'name' => 'Отзывы',
                        'url' => '/'
                    ],
                    [
                        'name' => 'Комментарии',
                        'url' => '/'
                    ],
                    [
                        'name' => 'Места',
                        'url' => '/'
                    ],
                    [
                        'name' => 'Жалобы',
                        'url' => '/'
                    ],

                ],
            ],
            [
                'name' => 'Редактирование',
                'icon' => '',
                'url' => '',
                'under' => [
                    [
                        'name' => 'Редактирование страниц',
                        'url' => '/admin/edit-page'
                    ],
                    [
                        'name' => 'Редактирование новостей',
                        'url' => '/'
                    ],

                ],
            ],
            [
                'name' => 'Бизнес-аккаунты',
                'icon' => '',
                'url' => '',
                'id' => 'bs-ac'
            ],

        ];
    }
}