<?php
namespace app\modules\admin\components\mainMenu;

class Config{

    public static function getConfig(){
        return [
            [
                'name' => 'Добавить контент',
                'icon' => 'icon-add-content',
                'url' => '',
                'under' => [
                    [
                        'name' => 'Добавить новость',
                        'url' => '/admin/news'
                    ],
                    [
                        'name' => 'Добавить место\\Категорию',
                        'url' => '/'
                    ],

                ],
            ],
            [
                'name' => 'Модерация',
                'icon' => 'icon-moderation-adm',
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
                'icon' => 'icon-edit-adm',
                'url' => '/admin/edit-page',
                'id' => 'edit-ac'
            ],
            [
                'name' => 'Бизнес-аккаунты',
                'icon' => 'icon-bz-ac-adm',
                'url' => '/admin/biz',
                'id' => 'bs-ac'
            ],

        ];
    }
}