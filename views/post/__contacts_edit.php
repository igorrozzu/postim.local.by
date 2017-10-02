<?php if($postInfo->phones && is_array($postInfo->phones)):?>
	<?php foreach ($postInfo->phones as $phone): ?>
		<div class="block-input-contact"><span class="container-img"><img src="/img/icon-phone-min.png"></span><input
				value="<?= $phone ?>" class="validator" data-error-parents="block-input-contact"
				data-message="Некоректные данные для контактов" placeholder="Номер телефона" name="contacts[phones][]"
				data-regex="^[0-9 +-]{3,20}$">
			<div class="close-input-contact"></div>
		</div>
	<?php endforeach; ?>
<?php endif;?>

<?php if($postInfo->web_site):?>
	<div class="block-input-contact"><span class="container-img"><img src="/img/icon-link-min.png"></span><input value="<?=$postInfo->web_site?>" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="Ссылка на сайт" name="contacts[web_site]" data-regex="^(https?:\/\/)?([\da-z.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$"><div class="close-input-contact"></div></div>
<?php endif;?>

<?php if($postInfo->social_networks && is_array($postInfo->social_networks)): ?>
	<?php foreach ($postInfo->social_networks as $key => $social_network):?>
		<?php if(is_array($social_network)):?>
			<?php foreach ($social_network as $keyItem => $valueItem):?>
				<?=renderSocialItem($keyItem,$valueItem)?>
			<?php endforeach;?>
		<?php else:?>
			<?=renderSocialItem($key,$social_network)?>
		<?php endif;?>
	<?php endforeach;?>
<?php endif;?>



<?php
	function renderSocialItem(string $key_social, string $value):string {
		switch ($key_social) {
			case 'vk': {
				return '<div class="block-input-contact"><span class="container-img"><img src="/img/icon-vk-min.png"></span><input value="'.$value.'" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="https://vk.com/..." name="contacts[social_networks][][vk]" data-regex="^https:\/\/vk.com\/.+$"><div class="close-input-contact"></div></div>';
			}
				break;
			case 'fb': {
				return '<div class="block-input-contact"><span class="container-img"><img src="/img/icon-fb-min.png"></span><input value="'.$value.'" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="https://www.facebook.com/..." name="contacts[social_networks][][fb]" data-regex="^https:\/\/(www\.)?facebook\.com\/.+$"><div class="close-input-contact"></div></div>';
			}
				break;
			case 'inst': {
				return '<div class="block-input-contact"><span class="container-img"><img src="/img/icon-instagram-min.png"></span><input value="'.$value.'" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="https://www.instagram.com/..." name="contacts[social_networks][][inst]" data-regex="^https?:\/\/(www\.)?instagram\.com\/.+$"><div class="close-input-contact"></div></div>';
			}
				break;
			case 'tw': {
				return '<div class="block-input-contact"><span class="container-img"><img src="/img/icon-tw-min.png"></span><input value="'.$value.'" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="https://twitter.com/..." name="contacts[social_networks][][tw]" data-regex="^https:\/\/twitter.com\/.+$"><div class="close-input-contact"></div></div>';
			}
				break;
			case 'ok': {
				return '<div class="block-input-contact"><span class="container-img"><img src="/img/icon-ok-min.png"></span><input value="'.$value.'" class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="https://www.ok.ru/..." name="contacts[social_networks][][ok]" data-regex="^https:\/\/(www\.)?ok\.ru\/.+$"><div class="close-input-contact"></div></div>';
			}
				break;
		}

		return '';
	}

?>
