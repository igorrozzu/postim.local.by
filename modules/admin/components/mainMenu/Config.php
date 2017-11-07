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
                        'name' => 'Добавить категорию',
                        'url' => '/admin/post/categories'
                    ],
                    [
                        'name' => 'Добавить страницу',
                        'url' => '/admin/post/other-page'
                    ],
                    [
                        'name' => 'Удалить страницу',
                        'url' => '/admin/post/other-page-delete'
                    ],
                    [
                        'name' => 'Удалить категорию',
                        'url' => '/admin/post/delete-categories'
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
                        'url' => '/admin/moderation/reviews'
                    ],
                    [
                        'name' => 'Фото',
                        'url' => '/admin/moderation/photo'
                    ],
                    [
                        'name' => 'Места',
                        'url' => '/admin/moderation/post'
                    ],
                    [
                        'name' => 'Жалобы',
                        'url' => '/admin/moderation/complaints'
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