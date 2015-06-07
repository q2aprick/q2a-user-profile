<?php
class qa_html_theme_layer extends qa_html_theme_base {

	public function post_avatar_meta($post, $class, $avatarprefix=null, $metaprefix=null, $metaseparator='<br/>') {
		qa_html_theme_base::post_avatar_meta($post, $class, $avatarprefix, $metaprefix, $metaseparator);

		$userId = $post['raw']['userid'];
		if(!isset($userId) || $class !== 'qa-q-view') {
			return;	
		}

		$profileItems = $this->getUserprofile($userId);
		$this->displayUserprofile($profileItems, $userId);
	}

	private function displayUserprofile($profileItems,$userId){

		echo '<p>質問者の基本情報</p>';

		if(!count($profileItems)) {
			$logginUserId = qa_get_logged_in_userid();
			if($userId == $logginUserId){
				echo 'プロフィールを入力してください';
				echo '<a href="'.qa_path('account').'">' .こちら . '</a>';
			}else{
				echo 'この質問者はプロフィールを入力していません';
			}
			return;
		}
		foreach($profileItems as $item)  {
			echo '<p>・';
			echo $item['title'] . ' : ';
			if($item['content']){
				echo $item['content'];
			} else {
				echo '未記入';
			}
			echo '</p>';
		}
	}

	private function getUserprofile($userId){
		$sql = $this->createUserprofileSQL($userId, array_keys($fields));
		$result = qa_db_query_sub($sql); 
		return qa_db_read_all_assoc($result);
	}

	private function createUserprofileSQL($userId){
		$sql = "select fields.content AS title, profile.content from qa_userprofile as profile inner join qa_userfields as fields on profile.title = fields.title where profile.userid = " . $userId;
		return $sql;
	}
}
