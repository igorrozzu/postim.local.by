<?php

namespace app\models;

use app\models\entities\Gallery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_reviews".
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $rating
 * @property integer $like
 * @property integer $user_id
 * @property string $date
 * @property string $data
 */
class Reviews extends \yii\db\ActiveRecord
{

	public $photos = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'like', 'user_id', 'date'], 'required'],
            [['rating'], 'required','message'=>'Поставте вашу оценку'],
            [['data'], 'required','message'=>'Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов.'],
            [['post_id', 'rating', 'like', 'user_id'], 'integer'],
            [['date','photos'], 'safe'],
            [['data'], 'string','min'=>100,'tooShort'=>'Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'rating' => 'Rating',
            'like' => 'Like',
            'user_id' => 'User ID',
            'date' => 'Date',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }

	public function getReviewsGallery(){
		return $this->hasMany(ReviewsGallery::className(), ['review_id' => 'id']);
	}

	public function getGallery()
	{
		return $this->hasMany(Gallery::className(), ['id' => 'gallery_id'])
			->via('reviewsGallery');
	}

	public function getLastPhoto(){
		return  Gallery::find()
			->innerJoin('tbl_reviews_gallery','tbl_reviews_gallery.review_id = '.$this->id.' AND tbl_reviews_gallery.gallery_id = tbl_gallery.id')
			->orderBy(['date'=>SORT_DESC])
			->one();
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    public function load($data, $formName = null)
	{
		$result = parent::load($data, $formName);
		$this->calcPhoto();
		return $result;
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		$this->savePhotos();
		$this->reCalcRatingPlace();
		//сделать пересчет рейтинга заведения

	}

	private function reCalcRatingPlace(){

    	$arrayRatings =  static::find()
			->select('rating')
			->where(['post_id'=>$this->post_id])
			->asArray()
			->all();
    	$arrayRatings = ArrayHelper::getColumn($arrayRatings,'rating');

		$sum = 0;
		$number = count($arrayRatings);
		for($i=0; $i < $number; $i++){
			$sum+=$arrayRatings[$i];
		}

		$newRating = (float) number_format($sum/$number, 1);
		Posts::updateAll(['rating'=>$newRating],'id='.$this->post_id);

	}

	private function calcPhoto(){
		if ($this->photos && is_array($this->photos)) {
			$this->count_photos = count($this->photos);
		}
	}
	private function savePhotos(){

		if ($this->photos && is_array($this->photos)) {

			$dir = Yii::getAlias('@webroot/post_photo/' . $this->post_id . '/');


			foreach ($this->photos as $photoLink) {

				$photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $photoLink);

				if (file_exists($photoPath)) {
					if(copy($photoPath,$dir.$photoLink)){
						$gallery = new Gallery(['post_id' => $this->post_id,
							'user_id' => Yii::$app->user->getId(),
							'link' => $photoLink,
							'user_status' => 0,
							'status' => 0,
							'date' => time(),
							'source' => '',
						]);
						if($gallery->save()){

							$reviewsGallery = new ReviewsGallery();
							$reviewsGallery->review_id = $this->id;
							$reviewsGallery->gallery_id = $gallery->id;
							$reviewsGallery->save();
						}
						unlink($photoPath);
					}
				}
			}

		}

	}
}
