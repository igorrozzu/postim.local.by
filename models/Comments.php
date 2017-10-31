<?php

namespace app\models;

use app\behaviors\notification\handlers\NewUnderComment;
use app\behaviors\notification\handlers\NewCommentToReview;
use app\models\entities\Complaints;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "tbl_comments".
 *
 * @property integer $id
 * @property integer $entity_id
 * @property integer $user_id
 * @property integer $main_comment_id
 * @property string $date
 * @property string $data
 * @property integer $like
 *
 * @property Comments $mainComment
 * @property Comments[] $comments
 */
class Comments extends \yii\db\ActiveRecord
{
    public $add_scenarios = [
        'add_main_comment',
        'add_under_comment'
    ];

    public static $ADD_MAIN_COMMENT = 'add_main_comment';
    public static $ADD_UNDER_COMMENT = 'add_under_comment';

    public static $STATUS_COMMENT_WAS_DELETED_BY = 1;

    public static $status_map = [
        1 => 'Комментарий был удален пользователем'
    ];

    public $comment_id = null;
    public $is_like = false;
    public $is_complaint = false;
    public $is_official_answer = false;

    public $official_answer = null;

    const TYPE = [
        'news' => 1,
        'reviews' => 2,
    ];

    public function isRelatedWithNews(): bool
    {
        return (int) $this->type_entity === self::TYPE['news'];
    }

    public function isRelatedWithReviews(): bool
    {
        return (int) $this->type_entity === self::TYPE['reviews'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_id', 'type_entity', 'user_id', 'date', 'like'], 'required', 'on' => self::$ADD_MAIN_COMMENT],
            [['entity_id', 'type_entity', 'user_id', 'date', 'data', 'like', 'main_comment_id'], 'required', 'on' => self::$ADD_UNDER_COMMENT],
            [['comment_id'], 'safe', 'on' => self::$ADD_UNDER_COMMENT],
            [['data'], 'required', 'message' => 'Введите текст комментария'],
            [['entity_id', 'user_id', 'main_comment_id', 'like'], 'integer'],
            [['date', 'entity_id', 'official_answer'], 'safe'],
            [['data'], 'string'],
            [['main_comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comments::className(), 'targetAttribute' => ['main_comment_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            NewUnderComment::className(),
            NewCommentToReview::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_id' => 'entity ID',
            'user_id' => 'User ID',
            'main_comment_id' => 'Main Comment ID',
            'date' => 'Date',
            'data' => 'Текст',
            'like' => 'Like',
        ];
    }

    public function beforeValidate()
    {
        if (in_array($this->getScenario(), $this->add_scenarios)) {
            if (Yii::$app->user->isGuest) {
                $this->addError('user_id', 'Не авторизованые пользователи не могут добавлять комментарии');
            }


            $this->user_id = Yii::$app->user->id;
            $this->date = time();
            $this->like = 0;

            if ($this->getScenario() == self::$ADD_UNDER_COMMENT) {
                if ($this->comment_id) {
                    $main_comment = self::find()->where(['id' => $this->comment_id])->one();
                    if ($main_comment) {
                        if ($main_comment['main_comment_id'] == null) {
                            $this->main_comment_id = $main_comment['id'];
                        } else {
                            $this->main_comment_id = $main_comment['main_comment_id'];
                        }
                    } else {
                        $this->addError('main_comment_id', 'Нет ссылки на комментарий');
                    }
                }
            }
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        $this->receiver_comment_id = $this->comment_id;
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->isRelationPopulated('likeUser')) {
            if ($this->likeUser != null) {
                $this->is_like = true;
            }
        }

        if ($this->isRelationPopulated('complaintUser')) {
            if ($this->complaintUser != null) {
                $this->is_complaint = true;
            }
        }

        if ($this->isRelationPopulated('hasOfficialAnswer')) {
            if ($this->hasOfficialAnswer != null) {
                $this->is_official_answer = true;
            }
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->getScenario() == self::$ADD_MAIN_COMMENT && $this->official_answer) {
            OfficialAnswer::deleteAll(['user_id' => Yii::$app->user->getId(),
                'entity_id' => $this->entity_id
            ]);
            $newOfficialAnswer = new OfficialAnswer(['comment_id' => $this->id,
                'user_id' => Yii::$app->user->getId(),
                'entity_id' => $this->entity_id]);
            $newOfficialAnswer->save();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainComment()
    {
        return $this->hasOne(Comments::className(), ['id' => 'main_comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverComment()
    {
        return $this->hasOne(Comments::className(), ['receiver_comment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderComments()
    {
        return $this->hasMany(Comments::className(), ['main_comment_id' => 'id'])->orderBy(['date' => 4]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'entity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReview()
    {
        return $this->hasOne(Reviews::className(), ['id' => 'entity_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getHasOfficialAnswer()
    {
        return $this->hasOne(OfficialAnswer::className(), ['comment_id' => 'id', 'user_id' => 'user_id']);
    }

    public function getLikeUser()
    {
        return $this->hasOne(CommentsLike::className(), ['comment_id' => 'id'])
            ->where(['user_id' => Yii::$app->user->id]);
    }

    public function getComplaintUser()
    {
        return $this->hasOne(Complaints::className(), ['entities_id' => 'id'])
            ->onCondition([
                Complaints::tableName() . '.user_id' => Yii::$app->user->getId(),
                Complaints::tableName() . '.type' => Complaints::$COMMENTS_TYPE
            ]);

    }

    public function getLink(){
        $link = '';

        if($this->isRelatedWithNews()){
            $link = "/{$this->news->url_name}-n{$this->news->id}?comments_id={$this->id}";
        }elseif ($this->isRelatedWithReviews()){
            $link = "/{$this->review->post->url_name}-p{$this->review->post->id}?review_id={$this->review->id}";
        }


        return $link;
    }
}
