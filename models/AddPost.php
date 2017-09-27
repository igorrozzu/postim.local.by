<?php

	namespace app\models;

	use app\models\entities\Gallery;
	use app\models\moderation_post\PostModerationFeatures;
	use app\models\moderation_post\PostModerationInfo;
	use app\models\moderation_post\PostModerationUnderCategory;
	use app\models\moderation_post\PostsModeration;
	use app\models\moderation_post\WorkingHoursModeration;
	use Yii;
	use yii\base\Model;
	use yii\helpers\FileHelper;


	class AddPost extends Model
	{
		public $name,
			$article, $address_text,
			$comments_to_address,$coords_address,$id;

		public $categories, $city, $contacts,
			$time_work, $features, $photos, $engine ,
			$moderation;

		private $cover = null;

		public static $SCENARIO_ADD_MODERATOR = 'add-moderator';
		public static $SCENARIO_EDIT_MODERATOR = 'edit-moderator';
		public static $SCENARIO_ADD_USER = 'add-user';
		public static $SCENARIO_EDIT_USER = 'edit-user';

		private $PostUnderCategory;
		private $PostFeatures;
		private $PostInfo;
		private $WorkingHours;
		private $Posts;


		public function init()
		{
			parent::init();

			if(Yii::$app->user->identity->role > 1){
				$this->PostUnderCategory = PostUnderCategory::className();
				$this->PostFeatures = PostFeatures::className();
				$this->PostInfo = PostInfo::className();
				$this->WorkingHours = WorkingHours::className();
				$this->Posts = Posts::className();
			}else{
				$this->PostUnderCategory = PostModerationUnderCategory::className();
				$this->PostFeatures = PostModerationFeatures::className();
				$this->PostInfo = PostModerationInfo::className();
				$this->WorkingHours = WorkingHoursModeration::className();
				$this->Posts = PostsModeration::className();
			}
		}

		public function rules()
		{
			return [
				[['name','coords_address', 'address_text','categories','time_work','city','id'], 'required','message'=> 'Поле обязательно для заполнения'],
				[['name','address_text'], 'match', 'pattern'=>'/^\S.{3,}/i'],
				[['article','comments_to_address','contacts','features','photos','engine','moderation'],'safe']
			];
		}

		public function scenarios()
		{
			return [
				self::$SCENARIO_ADD_USER => ['name','photos','engine', 'coords_address','address_text','categories','time_work','city','article','comments_to_address','contacts','features'],
				self::$SCENARIO_EDIT_USER => ['id','name','photos','engine', 'coords_address','address_text','categories','time_work','city','article','comments_to_address','contacts','features','moderation'],
				self::$SCENARIO_EDIT_MODERATOR => ['id','name','photos','engine', 'coords_address','address_text','categories','time_work','city','article','comments_to_address','contacts','features'],
				self::$SCENARIO_ADD_MODERATOR => ['name','photos','engine', 'coords_address','address_text','categories','time_work','city','article','comments_to_address','contacts','features'],
			];
		}

		public function save(){

			if ($this->validate()) {

				switch ($this->getScenario()){
					case self::$SCENARIO_ADD_MODERATOR:$this->addPost();break;
					case self::$SCENARIO_ADD_USER:$this->addPost();break;
					case self::$SCENARIO_EDIT_MODERATOR:$this->editPost();break;
					case self::$SCENARIO_EDIT_USER:$this->editPostUser();break;
				}

			}

			$this->updateCountUserPlace();
			return true;
		}

		public function addPost(int $main_id = 0){
			$total_view = new TotalView(['count' => 0]);
			if ($total_view->save()) {

				$post = new $this->Posts();
				$post->city_id = $this->city;

				$latLng = explode(',', $this->coords_address);
				$post->lat = $latLng[0];
				$post->lon = $latLng[1];

				$post->rating = 0;
				$post->data = $this->name;
				$post->address = $this->address_text;
				$post->additional_address = $this->comments_to_address;
				$post->user_id = Yii::$app->user->getId();
				$post->status = Yii::$app->user->identity->role > 1 ? 1 : 0;
				$post->date = time();
				$post->total_view_id = $total_view->id;
				$post->count_favorites = 0;
				$post->count_reviews = 0;
				$post->priority = 0;

				$oldPost = null;
				if ($main_id != 0) {
					$post->main_id = $main_id;
					$oldPost = Posts::find()->where(['id'=>$main_id])->one();
				}

				if(Yii::$app->user->identity->role > 1){
					$post->title = $this->engine['title'];
					$post->description = $this->engine['description'];
					$post->key_word = $this->engine['key_word'];
				}


				for ($i = 1; $i < 8; $i++) {
					if ($this->time_work[ $i ]['finish'] > 0) {
						$post->priority = 1;
						break;
					}
				}

				if ($post->save()) {

					$this->addCategories($post->id);
					$this->addFeatures($post->id);
					$this->addPostInfo($post->id);
					$this->addWorkTime($post->id);

					if(Yii::$app->user->identity->role > 1){
						$this->addPhotos($post->id);
					}

					if($this->cover){
						$post->cover = '/post_photo/'.$post->id.'/'. $this->cover;
						$post->update();
					}elseif($oldPost != null){
						$post->cover = $oldPost->cover;
						$post->update();
					}

				}

			}
		}

		public function editPost($post = null){

			if($post == null){
				$post = Posts::find()
					->with(['postCategory','info'])
					->where(['id'=>$this->id])->one();
			}

			$this->removeRelations($post);

			$post->city_id = $this->city;

			$latLng = explode(',', $this->coords_address);
			$post->lat = $latLng[0];
			$post->lon = $latLng[1];

			$post->data = $this->name;
			$post->address = $this->address_text;
			$post->additional_address = $this->comments_to_address;
			$post->status = $this->getScenario()==self::$SCENARIO_EDIT_MODERATOR?1:0;
			$post->priority = 0;

			if($this->getScenario()==self::$SCENARIO_EDIT_MODERATOR){
				$post->title = $this->engine['title'];
				$post->description = $this->engine['description'];
				$post->key_word = $this->engine['key_word'];
			}

			for ($i = 1; $i < 8; $i++) {
				if ($this->time_work[ $i ]['finish'] > 0) {
					$post->priority = 1;
					break;
				}
			}

			if ($post->update()) {
				$this->addCategories($post->id);
				$this->addFeatures($post->id);
				$this->addPostInfo($post->id);
				$this->addWorkTime($post->id);

				if($this->getScenario()==self::$SCENARIO_EDIT_MODERATOR){
					$this->addPhotos($post->id);
					$this->editPhotos($post->id);
				}

				if ($this->cover) {
					$post->cover = '/post_photo/' . $post->id . '/' . $this->cover;
					$post->update();
				}
			}

		}

		public function editPostUser(){
			if ($this->moderation != null) {
				$post = PostsModeration::find()->with(['postCategory','info'])->where(['id' => $this->id])->one();
				$this->editPost($post);

			} else {
				$oldPost = PostsModeration::find()->with(['postCategory','info'])->where(['main_id'=>$this->id,'user_id'=>Yii::$app->user->getId()])->one();
				if($oldPost!= null){
					$this->editPost($oldPost);
				}else{
					$this->addPost($this->id);
				}
			}

		}


		public function beforeValidate()
		{

			if (is_array($this->city)) {
				if (isset($this->city[0])) {
					$this->city = $this->city[0];
				}
			}

			if (is_array($this->time_work)) {
				$newWorkTime = [];
				if ($this->time_work['btns'] == 'unselect' || $this->time_work['btns'] == 'select') {
					$timeStart = 0;
					$timeFinish = $this->time_work['btns'] == 'unselect' ? null : 86400;
					for ($i = 1; $i < 8; $i++) {
						if($timeFinish == null){
							$timeStart = $timeFinish;
						}
						$newWorkTime[$i] = ['start' => $timeStart, 'finish' => $timeFinish];
					}
				}else{
					for ($i = 1; $i < 8; $i++) {

						$timeStart = isset($this->time_work[$i]['start']) && $this->time_work[$i]['start'] ?$this->getTimeByTextTime($this->time_work[$i]['start']):0;
						$timeFinish = isset($this->time_work[$i]['finish']) && $this->time_work[$i]['finish']?$this->getTimeByTextTime($this->time_work[$i]['finish']):0;

						if($timeFinish <= $timeStart){
							$timeFinish +=(24*3600);
						}

						if(!$this->time_work[$i]['start'] || !$this->time_work[$i]['finish']){
							$timeFinish = $timeStart = null;
						}

						$newWorkTime[$i] = ['start' => $timeStart, 'finish' => $timeFinish];
					}
				}

				$this->time_work = $newWorkTime;
			}

			if(is_array($this->photos)){
				$photos = [];
				foreach ($this->photos as $link => $photo){
					$arr = [];
					$arr['link'] = $link;
					$arr['src'] = $photo['src'];
					$arr['description'] = $photo['description'];
					$arr['confirm'] = $photo['confirm'];
					if($arr['confirm']=='true'){
						$this->cover = $link;
					}
					array_push($photos,$arr);
				}
				$this->photos = $photos;
			}

			if(isset($this->features) && $this->features){
				foreach ($this->features as $key => $feature){
					if(!is_array($feature) && !((int)$feature)){
						unset($this->features[$key]);
					}
				}
			}

			return parent::beforeValidate();
		}


		private function addCategories(int $post_id){
			$priority = 1;

			foreach ($this->categories as $category) {
				$post_under_category = new $this->PostUnderCategory(['post_id' => $post_id,
						'under_category_id' => $category,
						'priority' => $priority===1?$priority++:0
					]
				);
				$post_under_category->save();
			}
		}

		private function addFeatures(int $post_id){

			if ($this->features && is_array($this->features)) {
				foreach ($this->features as $idFeature => $feature) {
					if (!is_array($feature)) {
						$feature = (double) str_replace(',','.',$feature);
						$post_feature = new $this->PostFeatures(['features_id' => $idFeature,
							'post_id' => $post_id,
							'value' => $feature,
						]);
						$post_feature->save();
					} elseif ($idFeature == 'additionally') {
						foreach ($feature as $additionally) {
							$post_feature = new $this->PostFeatures(['features_id' => $additionally,
								'post_id' => $post_id,
								'value' => 1,
							]);
							$post_feature->save();
						}
					} else {
						$post_main_feature = new $this->PostFeatures([
							'features_id' => $idFeature,
							'post_id'     => $post_id,
							'value'       => 1,
						]);
						$post_main_feature->save();
						foreach ($feature as $item) {
							$post_feature = new $this->PostFeatures([
								'features_id'      => $item,
								'post_id'          => $post_id,
								'value'            => 1,
								'features_main_id' => $idFeature,
							]);
							$post_feature->save();
						}
					}
				}
			}
		}

		private function addPostInfo(int $post_id){
			$postInfo = new $this->PostInfo();

			if($this->contacts && is_array($this->contacts)){
				foreach ($this->contacts as $key => $contact) {
					if ($key == 'phones') {
						$postInfo->phones = $contact;
					}

					if ($key == 'web_site') {
						$postInfo->web_site = $contact;
					}
					if ($key == 'social_networks') {
						$postInfo->social_networks = $contact;
					}

				}
			}

			$postInfo->article = $this->article?$this->article:null;
			$postInfo->editors =  [0=>Yii::$app->user->getId()];
			$postInfo->post_id = $post_id;
			$postInfo->save();

		}

		private function addWorkTime(int $post_id){

			foreach ($this->time_work as $index => $item) {
				if($item['start'] !== null){
					$workingHours = new $this->WorkingHours();
					$workingHours->day_type = $index;
					$workingHours->time_start = $item['start'];
					$workingHours->time_finish = $item['finish'];
					$workingHours->post_id = $post_id;
					$workingHours->save();
				}
			}
		}

		private function addPhotos(int $post_id){

			if ($this->photos) {
				$dir = Yii::getAlias('@webroot/post_photo/' . $post_id . '/');
				if (!is_dir($dir)) {
					FileHelper::createDirectory($dir);
				}
				if($this->photos && is_array($this->photos)){
					foreach ($this->photos as $photo) {

						$tmpLink = Yii::getAlias('@webroot/post_photo/tmp/' . $photo['link']);
						if (file_exists($tmpLink)) {
							if (copy($tmpLink, $dir . $photo['link'])) {
								$gallery = new Gallery(['post_id' => $post_id,
									'user_id' => Yii::$app->user->getId(),
									'link' => $photo['link'],
									'user_status' => 1,
									'status' => 0,
									'date' => time(),
									'source' => $photo['src'],
								]);
								$gallery->save();
								unlink($tmpLink);
							}
						}
					}
				}

			}
		}
		private function editPhotos(int $pos_id){

			$galleries = Gallery::find()->where(['post_id'=>$pos_id,'user_status'=>1])->all();

			if($galleries && is_array($galleries)){
				foreach ($galleries as $gallery){
					$is_isset = false;
					if($this->photos && is_array($this->photos)){
						foreach ($this->photos as $photo){
							if($photo['link'] == $gallery['link']){
								$is_isset = true;
							}
						}
					}

					if(!$is_isset){
						$tmpLink = Yii::getAlias('@webroot/post_photo/'.$pos_id.'/' . $gallery['link']);
						if(file_exists($tmpLink)){
							unlink($tmpLink);
						}
						$gallery->delete();
					}

				}
			}

		}

		private function removeRelations($post){

			foreach ($post->postCategory as $item){
				$item->delete();
			}
			($this->WorkingHours)::deleteAll(['post_id'=>$post->id]);
			($this->PostFeatures)::deleteAll(['post_id'=>$post->id]);

			$post->info->delete();
		}

		private function getTimeByTextTime($textTime){
			$currentTimestamp =  Yii::$app->formatter->asTimestamp($textTime);
			$currentTime = idate('H',$currentTimestamp)*3600+idate('i',$currentTimestamp)*60+idate('s',$currentTimestamp);
			return $currentTime;
		}

		private function updateCountUserPlace(){
			$user  = UserInfo::find()->where(['user_id' => Yii::$app->user->getId()])->one();

			$count = Posts::find()->where(['user_id'=>Yii::$app->user->getId(),'status'=>1])->count();
			$countModeration = PostsModeration::find()->where(['user_id'=>Yii::$app->user->getId(),'status'=>0])->count();

			$user->count_places_added = $count;
			$user->count_place_moderation = $countModeration;
			$user->update();

		}

	}

