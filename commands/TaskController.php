<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\behaviors\notification\handlers\NotificationHandler;
use app\commands\cron\siteMap\models\SiteMap;
use app\components\user\ExperienceCalc;
use app\models\Category;
use app\models\City;
use app\models\entities\NotificationUser;
use app\models\entities\Task;
use app\models\News;
use app\models\Notification;
use app\models\PostUnderCategory;
use app\models\UnderCategory;
use app\models\User;
use app\models\UserInfo;
use app\models\Posts;
use app\modules\admin\models\Reviews;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TaskController extends Controller
{
    public function actionHandleNotifications()
    {
        $tasksQuery = Task::find()->where([
            Task::tableName() . '.type' => Task::TYPE['notification']
        ]);

        $mailer = Yii::$app->getMailer();
        $mailer->htmlLayout = 'layouts/notification';
        $ids = [];

        foreach ($tasksQuery->each() as $task) {
            try {
                $ids[] = $task->id;
                $data = json_decode($task->data);
                $class = 'app\commands\cron\notifications\\' . $data->class;
                $handler = new $class($mailer);
                $handler->params = $data->params;
                $handler->run();
            } catch (\Exception $e) {
                continue;
            }
        }

        if (count($ids) > 0) {
            Task::deleteAll([Task::tableName() . '.id' => $ids]);
        }

        return true;
    }

    public function actionChargeEverydayBonus()
    {
        $dayBefore = time() - 3600 * 24;
        $usersQuery = User::find()
            ->select([
                User::tableName() . '.id',
                User::tableName() . '.last_visit',
            ])
            ->innerJoinWith('userInfo')
            ->where(['>=', User::tableName() . '.last_visit', $dayBefore]);

        $template = Yii::$app->params['notificationTemplates']['reward.everyday'];
        $message = sprintf($template['text'],Yii::$app->params['site.hostName'].'/bonus', $template['exp'], $template['money']);

        $userIds = new \stdClass();
        $transaction = Yii::$app->db->beginTransaction();

        $notification = new Notification([
            'message' => json_encode([
                'type' => '',
                'data' => $message,
            ]),
            'date' => time(),
        ]);
        $notification->save();

        try {
            foreach ($usersQuery->each() as $user) {
                $userIds->all[] = $user->id;
                $userIds->dataToInsert[] = [
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                ];
                $oldLevel = $user->userInfo->level;
                $newLevel = ExperienceCalc::getLevelByExperience(
                    $user->userInfo->exp_points + $template['exp']);

                if ($oldLevel !== $newLevel) {
                    NotificationHandler::sendNotification($user->id, [
                        'type' => '',
                        'data' => sprintf(
                            Yii::$app->params['notificationTemplates']['common.newUserLevel'],
                            $newLevel
                        ),
                    ]);
                    $userIds->levelUpdate[] = $user->id;
                }

                if ($user->userInfo->hasExperienceAndBonusSub()) {
                    $userIds->mailSending[] = $user->id;
                }
            }

            if (isset($userIds->all)) {
                UserInfo::updateAllCounters([
                    'exp_points' => $template['exp'],
                    'mega_money' => $template['money'],
                ], [UserInfo::tableName() . '.user_id' => $userIds->all]);

                if (isset($userIds->levelUpdate)) {
                    UserInfo::updateAllCounters([
                        'level' => 1,
                    ], [UserInfo::tableName() . '.user_id' => $userIds->levelUpdate]);
                }

                Yii::$app->db->createCommand()
                    ->batchInsert(NotificationUser::tableName(), ['notification_id', 'user_id'],
                        $userIds->dataToInsert)
                    ->execute();
            }

            $transaction->commit();
        } catch(\Throwable $e) {
            $transaction->rollBack();

            return false;
        }

        if (isset($userIds->mailSending)) {
            $mailer = Yii::$app->getMailer();
            $mailer->htmlLayout = 'layouts/notification';

            $messages = [];
            $usersQueryForMailSending = User::find()
                ->select([
                    User::tableName() . '.name',
                    User::tableName() . '.id',
                    User::tableName() . '.email',
                ])
                ->where(['id' => $userIds->mailSending]);

            foreach ($usersQueryForMailSending->each() as $user) {
                $messages[] = $mailer->compose(['html' => 'reward'], [
                    'user' => $user,
                    'message' => $message,
                ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                    ->setTo($user->email)
                    ->setSubject('Уведомление Postim.by');
            }

            $mailer->sendMultiple($messages);
        }

        return true;
    }

    public function actionCreatingSiteMap(){

        $cities = City::find()->all();
        $siteMapMain = new SiteMap();
        $host = Yii::$app->params['site.hostName'];

        foreach ($cities as $city){

            $siteMapCity = new SiteMap();

            $isIssetSection = false;

            $pathToCitySiteMap = '/sitemaps/sitemap_city_'.$city['id'].'.xml';

            if (!is_dir(Yii::getAlias('@webroot').'/sitemaps')) {
                FileHelper::createDirectory(Yii::getAlias('@webroot').'/sitemaps');
            }

            $newDate = 0;

            $underCategoryCityQuery = PostUnderCategory::find()
                ->select('tbl_under_category.url_name, tbl_category.url_name as category_url_name, tbl_posts.date')
                ->innerJoinWith(['post.city','underCategory.category'])
                ->where(['tbl_city.url_name'=>$city->url_name])
                ->orderBy(['tbl_posts.date'=>SORT_DESC])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;

            $underCategoryCity = Yii::$app->db->createCommand($underCategoryCityQuery)->queryAll();
            $underCategoryUncl = [];
            $category = [];

            foreach ($underCategoryCity as $item){
                if(!isset($underCategoryUncl[$item['url_name']])){
                    $underCategoryUncl[$item['url_name']] = true;
                    $siteMapCity->addUrl(
                        '/'.$city->url_name.'/'.$item['url_name'],
                        SiteMap::DAILY,
                        0.7,
                        $item['date']
                    );
                    $category[$item['category_url_name']] = ['url_name'=> $item['category_url_name'],'date'=>$item['date']];
                    $isIssetSection = true;
                }

            }

            foreach ($category as $item){
                $siteMapCity->addUrl(
                    '/'.$city->url_name.'/'.$item['url_name'],
                    SiteMap::DAILY,
                    0.7,
                    $item['date']
                );
            }


            $posts = Posts::find()
                ->select(['city_id','tbl_posts.url_name','tbl_posts.id','date'])
                ->innerJoinWith(['city'])
                ->where(['tbl_city.url_name'=>$city->url_name])
                ->orderBy(['date' => SORT_DESC])
                ->all();

            if($posts){
                $siteMapCity->addUrl('/'.$city->url_name,SiteMap::WEEKLY,1,$posts[0]->date);
                $siteMapCity->addModels($posts,SiteMap::WEEKLY,1);
                $newDate = $posts[0]->date > $newDate ? $posts[0]->date : $newDate;
                $isIssetSection = true;
            }


            $news = News::find()
                ->select(['city_id','tbl_news.url_name','tbl_news.id','date'])
                ->innerJoinWith(['city'])
                ->where(['tbl_city.url_name'=>$city->url_name])
                ->orderBy(['date' => SORT_DESC])
                ->all();

            if($news){
                $siteMapCity->addUrl('/'.$city->url_name.'/novosti',
                    SiteMap::DAILY,
                    0.6,
                    $news[0]->date
                );
                $siteMapCity->addModels($news,SiteMap::WEEKLY,0.6);
                $newDate = $news[0]->date;
                $isIssetSection = true;
            }


            $reviewsQuery = Reviews::find()
                ->innerJoinWith(['post.city'])
                ->where(['tbl_city.url_name'=>$city->url_name])
                ->orderBy(['tbl_reviews.date'=>SORT_DESC])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;

            $reviews = Yii::$app->db->createCommand($reviewsQuery)->queryOne();

            if($reviews){
                $siteMapCity->addUrl(
                    '/'.$city->url_name.'/otzyvy',
                    SiteMap::DAILY,
                    0.7,
                    $reviews['date']
                );
                $newDate = $reviews['date'];
                $isIssetSection = true;
            }


            $item = [];
            $item['loc'] = $host. $pathToCitySiteMap;

            if($newDate){
                $item['lastmod'] = $newDate;
            }

            if($isIssetSection){
                $siteMapMain->addSections([$item]);

                $xmlCity = $siteMapCity->render();
                file_put_contents(Yii::getAlias('@webroot').$pathToCitySiteMap,$xmlCity);
            }

        }

        $mainXml = $siteMapMain->renderSections();
        file_put_contents(Yii::getAlias('@webroot'.'/sitemap.xml'),$mainXml);

    }

}
