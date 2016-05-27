<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    public function post_avatar_meta($post, $class, $avatarprefix = null, $metaprefix = null, $metaseparator = '<br/>')
    {
        qa_html_theme_base::post_avatar_meta($post, $class, $avatarprefix, $metaprefix, $metaseparator);

        $userId = $post['raw']['userid'];
        if (!isset($userId) || $class !== 'qa-q-view') {
            return;
        }

        $profileItems = $this->getUserprofile($userId);
        $this->displayUserprofile($profileItems, $userId);
    }

    private function displayUserprofile($profileItems, $userId)
    {
        $this->output('<p>質問者の基本情報</p>');

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
            foreach ($profileItems as $item) {
								$message .= '<p>・';
								$message .= $item['title'].' : ';
                if ($item['content']) {
                    $urlPattern = '/^http.+/';
                    if (preg_match($urlPattern, $item['content']) == 0) {
												$message .= $item['content'];
                    } else {
												$message .= '<a href="'.$item['content'].'" >'.$item['content'].'</a>';
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
        $sql = $this->createUserprofileSQL($userId, array_keys($fields));
        $result = qa_db_query_sub($sql);

        return qa_db_read_all_assoc($result);
    }

    private function createUserprofileSQL($userId)
    {
        $sql = 'select fields.content AS title, profile.content from qa_userprofile as profile inner join qa_userfields as fields on profile.title = fields.title where profile.userid = '.$userId;

        return $sql;
    }
}
