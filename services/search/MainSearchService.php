<?php

namespace app\services\search;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\Pagination;
use app\models\sphinx\search\DiscountSearch;
use app\models\sphinx\search\NewsSearch;
use app\models\sphinx\search\PostSearch;
use app\widgets\cardsDiscounts\CardsDiscounts;
use dosamigos\transliterator\TransliteratorHelper;
use Yii;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\sphinx\MatchExpression;
use yii\web\Request;

/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 2/26/18
 * Time: 10:50 AM
 */
class MainSearchService
{
    /**
     * @var
     */
    protected $searchModels;

    protected $text;

    /**
     * MainSearchService constructor.
     * @param NewsSearch $newsModel
     * @param PostSearch $postModel
     * @param DiscountSearch $discountModel
     */
    public function __construct(
        PostSearch $postModel,
        NewsSearch $newsModel,
        DiscountSearch $discountModel
    ) {
        $this->searchModels = [
            'post' => $postModel,
            'news' => $newsModel,
            'discount' => $discountModel,
        ];
    }

    public static function getWidgetsConfig(array $params, string $widgetName = 'all'): ?array
    {
        $widgets = [
            'post' => [
                'class' => CardsPlaceWidget::class,
                'params' => [
                    'dataprovider' => null,
                    'settings' => [
                        'is-it-sphinx-model' => true,
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-posts',
                        'load-time' => $params['loadTime'],
                    ],
                ],
            ],
            'news' => [
                'class' => CardsNewsWidget::class,
                'params' => [
                    'dataprovider' => null,
                    'settings' => [
                        'is-it-sphinx-model' => true,
                        'replace-container-id' => 'feed-news',
                        'load-time' => $params['loadTime'],
                    ],
                ],
            ],
            'discount' => [
                'class' => CardsDiscounts::class,
                'params' => [
                    'dataprovider' => null,
                    'settings' => [
                        'is-it-sphinx-model' => true,
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-discounts',
                        'load-time' => $params['loadTime'],
                        'show-distance' => true,
                    ],
                ],
            ],
        ];

        return $widgetName === 'all' ? $widgets : ($widgets[$widgetName] ?? null);
    }

    public function getAutoCompleteData(int $limit): array
    {
        $entities = [];

        foreach ($this->searchModels as $entityName => $model) {
            $query = $model->getAutoCompleteQuery($limit);

            $matchExp = $this->createBaseMatchExpression($entityName);
            $matchExp2 = clone $matchExp;

            $matchParams = ['or', ['header,data' => $this->text]];
            $this->setMatchByWordsFromText($matchParams, $this->text, 'header,data');
            $matchExp2->andMatch($matchParams);

            $entities[$entityName] = $query
                ->match($matchExp2)
                ->all();

            if ($entities[$entityName]) {
                continue;
            }

            $matchParams = $this->getMatchParamsOfCorrectedText();
            if ($matchParams) {
                $matchExp->andMatch($matchParams);
                $entities[$entityName] = $query
                    ->match($matchExp)
                    ->all();
            }
        }

        return $this->extractEntities($entities);
    }

    public function getWidgetMetaData(Request $request): array
    {
        $loadTime = $request->get('loadTime', time());
        $type = $request->get('type_feed', 'post');
        $widget = $this->getWidgetsConfig([
            'loadTime' => $loadTime,
        ], $type);

        $model = $this->searchModels[$type];
        $query = $model->getMainSearchQuery($loadTime);

        $widget['params']['dataprovider'] = $this->createDataProvider(
            $request, $query, $type
        );

        return $widget;
    }

    public function getWidgetsMetaData(Request $request): array
    {
        $loadTime = $request->get('loadTime', time());
        $widgets = $this->getWidgetsConfig([
            'loadTime' => $loadTime,
        ]);

        foreach ($this->searchModels as $entityName => $model) {
            $query = $model->getMainSearchQuery($loadTime);

            $widgets[$entityName]['params']['dataprovider'] = $this->createDataProvider(
                $request, $query, $entityName
            );
        }

        return $widgets;
    }

    protected function createBaseMatchExpression(string $modelType): MatchExpression
    {
        $matchExp = (new MatchExpression());
        if ($modelType !== 'news' && $city = Yii::$app->city->getSelected_city()['url_name']) {
            $matchExp->match([
                'or',
                ['city' => $city],
                ['region' => $city],
            ]);
        }

        return $matchExp;
    }

    protected function createDataProvider(Request $request, $query, string $modelType): ActiveDataProvider
    {
        $matchExp = $this->createBaseMatchExpression($modelType);
        $matchExp2 = clone $matchExp;

        $matchParams = ['or', ['header,data' => $this->text]];
        $this->setMatchByWordsFromText($matchParams, $this->text, 'header,data');
        $matchExp2->andMatch($matchParams);

        $provider = new ActiveDataProvider([
            'query' => $query->match($matchExp2),
            'pagination' => $this->createPagination($request),
        ]);

        if ($provider->getTotalCount() > 0) {
            return $provider;
        } else {
            $provider->totalCount = null;
        }

        $matchParams = $this->getMatchParamsOfCorrectedText();

        if ($matchParams) {
            $matchExp->andMatch($matchParams);
            $provider->query = $query->match($matchExp);
        }

        return $provider;
    }

    protected function getMatchParamsOfCorrectedText(): ?array
    {
        $translatedText = TransliteratorHelper::process($this->text);
        $correctedText = Yii::$app->formatter->correctWrongKeyword($this->text);

        $matchParams = ['or'];
        if ($translatedText !== $this->text) {
            $matchParams[] = ['header,data' => $translatedText];
            $this->setMatchByWordsFromText($matchParams, $translatedText, 'header,data');
        }
        if ($correctedText !== $this->text) {
            $matchParams[] = ['header,data' => $correctedText];
            $this->setMatchByWordsFromText($matchParams, $correctedText, 'header,data');
        }

        return count($matchParams) > 1 ? $matchParams : null;
    }

    protected function extractEntities(array &$entities): array
    {
        $data = [];
        foreach ($entities as $entityName => $models) {
            foreach ($models as $model) {

                if (!is_null($model[$entityName])) {
                    $data[$entityName][] = $model[$entityName];
                }
            }
        }

        return $data;
    }

    protected function setMatchByWordsFromText(
        array &$matchParams,
        string $text,
        string $fields,
        int $minWordLength = 2
    ): void {
        $words = explode(' ', $text);
        if (count($words) > 1) {
            foreach ($words as &$word) {
                if (mb_strlen($word) > $minWordLength) {
                    $matchParams[] = [$fields => $word];
                }
            }
        }
    }

    protected function createPagination(Request $request): Pagination
    {
        return new Pagination([
            'pageSize' => $request->get('per-page', 8),
            'page' => $request->get('page', 1) - 1,
            'route' => $request->getPathInfo(),
            'selfParams' => [
                'text' => true,
                'type_feed' => true,
            ],
        ]);
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = Html::encode(mb_strtolower(trim($text)));
    }
}