<?php

class qa_html_theme_layer extends qa_html_theme_base
{
		// show information about user
	public function post_avatar_meta($post, $class, $avatarprefix = null, $metaprefix = null, $metaseparator = '<br/>')
	{
		qa_html_theme_base::post_avatar_meta($post, $class, $avatarprefix, $metaprefix, $metaseparator);

		$userId = $post['raw']['userid'];
		$handle = $post['raw']['handle'];
		if (!isset($userId)) {
			return;
		}
		if ($class !== 'qa-q-view' && $class !== 'qa-a-item') {
			return;
		}

		$profileItems = $this->getUserprofile($userId);
		$this->displayUserprofile($profileItems, $userId, $handle);
	}

	private function displayUserprofile($profileItems, $userId, $handle = null)
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
			foreach ($profileItems as $item) {
				$message .= '<p>・';
				$message .= $item['title'].' : ';
				if ($item['content']) {
					if ($item['title'] === '自己紹介') {
						$length = mb_strlen($item['content'], 'UTF-8');
						if ($length > $profile_max_length) {
							$message .= mb_substr($item['content'],0,$profile_max_length - 6, 'UTF-8') . $read_more;
						} else {
							$message .= $item['content'];
						}
					} else {
						$urlPattern = '/^http.+/';
						if (preg_match($urlPattern, $item['content']) == 0) {
							$message .= $item['content'];
						} else {
							$message .= '<a href="'.$item['content'].'" >'.$item['content'].'</a>';
						}
					}
				} else {
					$message .= '未記入';
				}
				$message .= '</p>';
			}
			$this->output($message);
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
