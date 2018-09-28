<?php

namespace app\services\recommendedPosts;

use app\repositories\PostRepository;
use app\repositories\RecommendedPostRepository;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

class RecommendedPosts
{
    /**
     * @var PostRepository
     */
    protected $_postRepository;
    /**
     * @var RecommendedPostRepository
     */
    protected $_recommendedPostRepository;

    /**
     * @var array
     */
    protected $queue = [];

    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $selectedPostIds = [];

    /**
     * AccountService constructor.
     * @param PostRepository $postRepository
     * @param RecommendedPostRepository $recommendedPostRepository
     */
    public function __construct(
        PostRepository $postRepository,
        RecommendedPostRepository $recommendedPostRepository
    ) {
        $this->_postRepository = $postRepository;
        $this->_recommendedPostRepository = $recommendedPostRepository;
    }

    public function getPostsIds(Model $post, int $limit): ?array
    {
        $this->selectedPostIds = $this->_postRepository->getRecommendedPostsIds($post);

        if (empty($this->selectedPostIds)) {
            return null;
        } else {
            if (count($this->selectedPostIds) <= $limit) {
                return $this->selectedPostIds;
            }
        }

        $key = $this->getKey($post);
        $model = $this->_recommendedPostRepository::findOne(['key' => $key]);

        $resultIds = [];
        try {
            if (!isset($model)) {
                $this->queue = $this->selectedPostIds;
                $resultIds = $this->getIdsFromQueue($limit);
                $model = new \app\models\entities\RecommendedPosts([
                    'key' => $key,
                    'queue' => Json::encode($this->queue),
                    'updating_at' => time(),
                ]);
                $model->save();

            } else {
                $this->queue = Json::decode($model->queue);
                $this->processQueue();
                $resultIds = $this->getIdsFromQueue($limit);

                $model->queue = Json::encode($this->queue);
                $model->updating_at = time();
                $model->save();
            }
        } catch (Exception $e) {
            return null;
        }

        return $resultIds;
    }

    protected function processQueue(): void
    {
        $this->queue = array_intersect($this->queue, $this->selectedPostIds);
        $newIds = array_diff($this->selectedPostIds, $this->queue);
        $this->queue = array_merge($this->queue, $newIds);
    }

    protected function getIdsFromQueue(int $limit): array
    {
        $ids = array_splice($this->queue, 0, $limit);
        $this->queue = array_merge($this->queue, $ids);
        return $ids;
    }

    protected function getKey(Model $post): string
    {
        if (!isset($this->key)) {
            $criteria[] = $post->city->url_name;

            if (!empty($post->categories)) {
                foreach ($post->categories as $category) {
                    $criteria[] = $category->url_name;
                }
            }

            $this->key = Json::encode($criteria);
        }

        return $this->key;
    }
}