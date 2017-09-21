<?php

	namespace app\models;

	use app\models\entities\Gallery;
	use Yii;
	use yii\base\Model;
	use yii\helpers\FileHelper;


	class AddPost extends Model
	{
		public $name,
			$article, $address_text,
			$comments_to_address,$coords_address;

		public $categories, $city, $contacts,
			$time_work, $features, $photos, $engine;

		private $cover = null;


		public function rules()
		{
			return [
				[['name','coords_address', 'address_text','categories','time_work','city'], 'required','message'=> 'Поле обязательно для заполнения'],
				[['name','address_text'], 'match', 'pattern'=>'/^\S.{3,}/i'],
				[['article','comments_to_address','contacts','features','photos','engine'],'safe']
			];
		}

		public function save(){

			if ($this->validate()) {

				$total_view = new TotalView(['count' => 0]);
				if ($total_view->save()) {
					$post = new Posts();
					$post->city_id = $this->city;

					if ($this->cover) {
						$post->cover = $this->cover;
					}else{
						$post->cover = '/post-img/default.png';
					}

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
						$priority = 1;
						foreach ($this->categories as $category) {
							$post_under_category = new PostUnderCategory(['post_id' => $post->id,
									'under_category_id' => $category,
									'priority' => $priority===1?$priority++:0
								]
							);
							$post_under_category->save();
						}

						if ($this->features && is_array($this->features)) {
							foreach ($this->features as $idFeature => $feature) {
								if (!is_array($feature)) {
									$feature = (double) str_replace(',','.',$feature);
									$post_feature = new PostFeatures(['features_id' => $idFeature,
										'post_id' => $post->id,
										'value' => $feature,
									]);
									$post_feature->save();
								} elseif ($idFeature == 'additionally') {
									foreach ($feature as $additionally) {
										$post_feature = new PostFeatures(['features_id' => $additionally,
											'post_id' => $post->id,
											'value' => 1,
										]);
										$post_feature->save();
									}
								} else {
									$post_main_feature = new PostFeatures([
										'features_id' => $idFeature,
										'post_id'     => $post->id,
										'value'       => 1,
									]);
									$post_main_feature->save();
									foreach ($feature as $item) {
										$post_feature = new PostFeatures([
											'features_id'      => $item,
											'post_id'          => $post->id,
											'value'            => 1,
											'features_main_id' => $idFeature,
										]);
										$post_feature->save();
									}
								}
							}
						}

						$postInfo = new PostInfo();
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
						$postInfo->post_id = $post->id;

						if ($postInfo->save()) {
							foreach ($this->time_work as $index => $item) {
								if($item['start'] !== null){
									$workingHours = new WorkingHours();
									$workingHours->day_type = $index;
									$workingHours->time_start = $item['start'];
									$workingHours->time_finish = $item['finish'];
									$workingHours->post_id = $post->id;
									$workingHours->save();
								}
							}

							if ($this->photos) {
								$dir = Yii::getAlias('@webroot/post_photo/' . $post->id . '/');
								if (!is_dir($dir)) {
									FileHelper::createDirectory($dir);
								}
								foreach ($this->photos as $photo) {

									$tmpLink = Yii::getAlias('@webroot/post_photo/tmp/' . $photo['link']);
									if (file_exists($tmpLink)) {
										if (copy($tmpLink, $dir . $photo['link'])) {
											$gallery = new Gallery(['post_id' => $post->id,
												'user_id' => Yii::$app->user->getId(),
												'link' => $photo['link'],
												'user_status' => 1,
												'status' => 0,
												'date' => time(),
												'source' => $photo['src'],
											]);
											$gallery->save();
										}
									}
								}
							}
							if($this->cover){
								$post->cover = '/post_photo/'.$post->id.'/'. $this->cover;
								$post->update();
							}

						}

					}


				}

			}

			$this->updateCountUserPlace();
			return true;
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

		private function getTimeByTextTime($textTime){
			$currentTimestamp =  Yii::$app->formatter->asTimestamp($textTime);
			$currentTime = idate('H',$currentTimestamp)*3600+idate('i',$currentTimestamp)*60+idate('s',$currentTimestamp);
			return $currentTime;
		}

		private function updateCountUserPlace(){
			$user  = UserInfo::find()->where(['user_id' => Yii::$app->user->getId()])->one();

			$count = Posts::find()->where(['user_id'=>Yii::$app->user->getId(),'status'=>1])->count();
			$countModeration = Posts::find()->where(['user_id'=>Yii::$app->user->getId(),'status'=>0])->count();

			$user->count_places_added = $count;
			$user->count_place_moderation = $countModeration;
			$user->update();

		}

	}

