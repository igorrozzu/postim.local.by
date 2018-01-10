<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 12/29/17
 * Time: 3:42 PM
 */

namespace app\modules\admin\controllers;


use app\components\Pagination;
use app\components\UserHelper;
use app\models\Discounts;
use app\models\entities\GalleryDiscount;
use app\models\search\DiscountSearch;
use app\modules\admin\components\AdminDefaultController;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class DiscountController extends AdminDefaultController
{
    public function actionIndex()
    {
        $searchModel = new DiscountSearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
        ]);

        $dataProvider = $searchModel->getDiscountsInModeration($pagination);

        return $this->render('discounts', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionEdit(int $id, string $redirect_back = null)
    {
        $discount = Discounts::findOne($id);

        if (!isset($discount)) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isPost) {

            $discount->load(Yii::$app->request->post(), 'discount');
            $discount->photos = Yii::$app->request->post('photos');

            if ($discount->edit()) {
                Yii::$app->session->setFlash('success', 'Редактирование скидки произведено успешно');

                if (isset($redirect_back)) {
                    $redirectUrl = Url::to(['/discount/read', 'url' => $discount->url_name,
                        'discountId' => $discount->id]);
                } else {
                    $redirectUrl = Url::to(['/admin/discount/index']);
                }
                return $this->redirect($redirectUrl);
            }
        }

        $photos = GalleryDiscount::find()
            ->where(['discount_id' => $discount->id])
            ->all();

        return $this->render('edit', [
            'photos' => $photos,
            'discount' => $discount,
            'errors' => array_values($discount->getFirstErrors()),
        ]);
    }

    public function actionConfirm(int $id)
    {
        $result = Discounts::updateAll(['status' => Discounts::STATUS['active']], ['id' => $id]);

        if ($result !== 1) {
            Yii::$app->session->setFlash('error', 'Подтверждение скидки не удалось');
        } else {
            Yii::$app->session->setFlash('success', 'Подтверждение скидки произведено успешно');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionHide()
    {
        $response = new \stdClass();
        $response->success = true;
        $response->message = 'Скидка успешно скрыта';

        $message = Yii::$app->request->post('message',false);
        $id = Yii::$app->request->post('id',false);

        if ($message && $id) {
            $discount = Discounts::find()
                ->innerJoinWith(['user'])
                ->where([Discounts::tableName() . '.id' => $id])
                ->one();

            if (isset($discount)) {
                $link = Url::to(['/discount/read', 'url' => $discount->url_name, 'discountId' => $discount->id]);

                $templateMessage = Yii::$app->params['notificationTemplates']['discount'];
                $messageNotice = sprintf($templateMessage['cancel'], $link, $discount->header, $message);
                $messageEmail = sprintf($templateMessage['emailCancel'], $discount->header, $message);

                UserHelper::sendNotification($discount->user_id, [
                    'type' => '',
                    'data' => $messageNotice,
                ]);
                UserHelper::sendMessageToEmailCustomReward($discount->user, $messageEmail, $link);
            } else {
                $response->success = false;
                $response->message = 'Скидка не найдена';
            }
        } else {
            $response->success = false;
            $response->message = 'Введите текст сообщения';
        }

        return $this->asJson($response);
    }
}