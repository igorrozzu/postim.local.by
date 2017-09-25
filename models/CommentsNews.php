<?php

namespace app\models;

use app\behaviors\notification\handlers\NewComment;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "tbl_comments_news".
 *
 * @property integer $id
 * @property integer $news_id
 * @property integer $user_id
 * @property integer $main_comment_id
 * @property string $date
 * @property string $data
 * @property integer $like
 *
 * @property CommentsNews $mainComment
 * @property CommentsNews[] $commentsNews
 */
class CommentsNews extends \yii\db\ActiveRecord
{
    public $add_scenarios =[
        'add_main_comment',
        'add_under_comment'
    ];

    public static $ADD_MAIN_COMMENT='add_main_comment';
    public static $ADD_UNDER_COMMENT='add_under_comment';

    public static $STATUS_COMMENT_WAS_DELETED_BY=1;

    public static $status_map=[
        1=>'Комментарий был удален пользователем'
    ];

    public $comment_id = null;
    public $is_like=false;
    public $is_complaint=false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comments_news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id', 'user_id', 'date', 'like'], 'required','on'=>self::$ADD_MAIN_COMMENT],
            [['news_id', 'user_id', 'date', 'data', 'like','main_comment_id'], 'required','on'=>self::$ADD_UNDER_COMMENT],
            [['comment_id'], 'safe','on'=>self::$ADD_UNDER_COMMENT],
            [['data'], 'required','message'=>'Введите текст комментария'],
            [['news_id', 'user_id', 'main_comment_id', 'like'], 'integer'],
            [['date'], 'safe'],
            [['data'], 'string'],
            [['main_comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommentsNews::className(), 'targetAttribute' => ['main_comment_id' => 'id']],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'notification' => [
                'class' => 'app\behaviors\notification\Notification',
                'handlers' => [
                    'afterInsert' => NewComment::className()
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'news_id' => 'News ID',
            'user_id' => 'User ID',
            'main_comment_id' => 'Main Comment ID',
            'date' => 'Date',
            'data' => 'Текст',
            'like' => 'Like',
        ];
    }

    public function beforeValidate()
    {
        if(in_array($this->getScenario(),$this->add_scenarios)){
            if(Yii::$app->user->isGuest){
                $this->addError('user_id','Не авторизованые пользователи не могут добавлять комментарии');
            }

            $this->user_id=Yii::$app->user->id;
            $this->date=time();
            $this->like=0;

            if($this->getScenario() == self::$ADD_UNDER_COMMENT){
                if($this->comment_id){
                    $main_comment = self::find()->where(['id'=>$this->comment_id])->one();
                    if($main_comment){
                        if($main_comment['main_comment_id']==null){
                            $this->main_comment_id=$main_comment['id'];
                        }else{
                            $this->main_comment_id=$main_comment['main_comment_id'];
                        }
                    }else{
                        $this->addError('main_comment_id','Нет ссылки на комментарий');
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

        if($this->isRelationPopulated('likeUser')){
            if($this->likeUser!=null){
                $this->is_like=true;
            }
        }

        if($this->isRelationPopulated('complaintUser')){
            if($this->complaintUser!=null){
                $this->is_complaint=true;
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainComment()
    {
        return $this->hasOne(CommentsNews::className(), ['id' => 'main_comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverComment()
    {
        return $this->hasOne(CommentsNews::className(), ['receiver_comment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderComments()
    {
        return $this->hasMany(CommentsNews::className(), ['main_comment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getLikeUser(){
        return $this->hasOne(CommentsNewsLike::className(),['comment_id'=>'id'])
            ->where(['user_id'=>Yii::$app->user->id]);
    }

    public function getComplaintUser(){
        return $this->hasOne(CommentsComplaint::className(),['comment_id'=>'id'])
            ->where(['user_id'=>Yii::$app->user->id]);
    }
}
