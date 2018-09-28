<?php

namespace app\components\user;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\models\PostsSearch;
use app\models\search\DiscountSearch;
use app\models\search\NewsSearch;
use app\widgets\cardsDiscounts\CardsDiscounts;

class FavoritesFeedsHelper
{
    /**
     * @var string
     */
    private $feedName;

    /**
     * FavoritesFeedsHelper constructor.
     * @param string $feedName
     */
    public function __construct(string $feedName)
    {
        $this->feedName = $feedName;
    }

    public function getModel()
    {
        switch ($this->feedName) {
            case 'posts':
                return new PostsSearch();
            case 'news':
                return new NewsSearch();
            case 'discounts':
                return new DiscountSearch();
        }

        return null;
    }

    public function getWidgetClassName()
    {
        switch ($this->feedName) {
            case 'posts':
                return CardsPlaceWidget::className();
            case 'news':
                return CardsNewsWidget::className();
            case 'discounts':
                return CardsDiscounts::className();
        }

        return null;
    }

    public function getWidgetWrapAttributes()
    {
        switch ($this->feedName) {
            case 'posts':
                return 'class"cards-block"';
            case 'news':
                return 'class"block-news"';
            case 'discounts':
                return 'class="cards-block-discount row-3 main-pjax" ' .
                    'data-favorites-state-url="/discount/favorite-state"';
        }

        return null;
    }
}