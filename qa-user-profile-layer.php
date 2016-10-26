<?php

class qa_html_theme_layer extends qa_html_theme_base
{
		// show information about user
	public function post_avatar_meta($post, $class,$post_meta_show = true, $avatarprefix = null, $metaprefix = null, $metaseparator = '<br/>')
	{
		
		$this->output('<span class="'.$class.'-avatar-meta">');
		$this->avatar($post, $class, $avatarprefix);
		if($post_meta_show)
			$this->post_meta($post, $class, $metaprefix, $metaseparator);
		$this->output('</span>');
		
		$userId = $post['raw']['userid'];
		$handle = $post['raw']['handle'];
		if (!isset($userId)) {
			return;
		}
		if ($class !== 'qa-q-view' && $class !== 'qa-a-item') {
			return;
		}

		$profileItems = $this->getUserprofile($userId);
		
		$this->displayUserprofile($post,$profileItems, $userId, $handle);
	}
	public function avatar($item, $class, $prefix=null)
	{
		if (isset($item['avatar'])) {
			if (isset($prefix))
				$this->output($prefix);
			preg_match('/qa_size=([0-9][0-9])/is', $item['avatar'], $qa_size);
			$img_width_height = $qa_size[1]*0.75;
			$this->output(
				'<span class="'.$class.'-avatar" style="background:url(./?qa=image&qa_blobid='.$item['raw']['avatarblobid'].'&qa_size='.$qa_size[1].') no-repeat center center;height: '.$img_width_height.'px;width: '.$img_width_height.'px;display:inline-block;border-radius: 50%;background-color: #757575;margin-right:6px;">',
				
				'</span>'
			);
		}
	}
	private function displayUserprofile($post,$profileItems, $userId, $handle = null)
	{
		if (!count($profileItems)) {
			$message = '';
			$logginUserId = qa_get_logged_in_userid();
			if ($userId == $logginUserId) {
				$message .= 'プロフィールを入力してください';
				$message .= '<a href="'.qa_path('account').'">'.こちら.'</a>';
			} else {
				$message .= 'この質問者はプロフィールを入力していません';
			}
			$this->output($message);

		} else {
			$message = '';
			$profile_max_length = qa_opt('qa_user_profile_max_length');
			$read_more = $this->get_read_more($handle);

			//プロフィール表示
			$this->output('<div class="profile">');

			$this->output('<div class="mdl-typography--subhead">');
			$this->post_meta_who($post, 'meta');
			//活動場所を表示
			$this->output('<span class="mdl-chip__text"><span class="mdl-typography--font-bold">活動場所</span>：'.$profileItems[0]['content'].'</span>');
			$this->output('</div>');
			
			$profile_intro = $profileItems[2]['content'];
			$length = mb_strlen($profile_intro, 'UTF-8');
			$this->output('<div class="profile_intro">');
			//最大文字数になったらもっと読むリンク追加
			$profile_intro_content = $length > $profile_max_length ? $this->output(mb_substr($profile_intro,0,$profile_max_length - 6, 'UTF-8') . $read_more) : $this->output($profile_intro) ;
			
			$this->output($profile_intro_content);
			$this->output('</div>');

			$this->output('</div>');
			/* $this->output('<ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect" for="user-'.$userId.'">');
			foreach ($profileItems as $item) {
				if ($item['content']) {
					
					$this->output('<li>');

					//自己紹介は項目名非表示
					$item_title = $item['title'] === '自己紹介'?'':$item['title'];
					$this->output('<span class="mdl-typography--body-2">');
					$this->output($item_title);
					$this->output('</span>');
					

					//開始タグと閉じタグを指定　未記入ならその項目は表示しない
					$wrap_content_tag = $item['content'] === ''?'':'<span  class="mdl-typography--body-1-color-contrast">';
					$close_content_tag = $item['content'] === ''?'':'</span>';
					$this->output($wrap_content_tag);

					if ($item['content']) {
						if ($item['title'] === '自己紹介') {
							$length = mb_strlen($item['content'], 'UTF-8');
							if ($length > $profile_max_length) {
								$this->output(mb_substr($item['content'],0,$profile_max_length - 6, 'UTF-8') . $read_more);
							} else {
								$this->output($item['content']);
							}
						} else {
							$urlPattern = '/^http.+/';
							if (preg_match($urlPattern, $item['content']) == 0) {
								$this->output($item['content']);
							} else {
								$this->output('<a class="mdl-button mdl-js-button mdl-button--icon" href="'.$item['content'].'" ><i class="material-icons">open_in_new</i></a>');
							}
						}
						
					} 
					$this->output($close_content_tag);
					$this->output('</li>');
				}
			}
			$this->output('</ul>');*/
		}
	}

	private function getUserprofile($userId)
	{
		$sql = $this->createUserprofileSQL($userId);
		$result = qa_db_query_sub($sql);

		return qa_db_read_all_assoc($result);
	}

	private function createUserprofileSQL($userId)
	{
		$sql = 'select fields.content AS title, profile.content from qa_userprofile as profile inner join qa_userfields as fields on profile.title = fields.title where profile.userid = '.$userId;

		return $sql;
	}

	private function get_read_more($handle)
	{
		return '<a href="' . qa_opt('site_url') . 'user/' . $handle . '#avatar" />…もっと読む</a>';
	}
}
