<?php

class qa_user_profile_admin {

	function option_default($option) {
		switch($option) {
			case 'qa_user_profile_max_length':
				return 80;
			default:
				return null;
		}
	}

	function allow_template($template) {
		return ($template != 'admin');
	}

	function admin_form(&$qa_content){
		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('qa_user_profile_save')) {
			qa_opt('qa_user_profile_length', qa_post_text('qa_user_profile_length'));
			$ok = qa_lang('admin/options_saved');
		}

		// form fields to display frontend for admin
		$fields = array();

		$fields[] = array(
			'label' => 'Max Length: ',
			'type' => 'number',
			'value' => (int)qa_opt('qa_user_profile_length'),
			'tags' => 'name="qa_user_profile_length"',
		);

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => $fields,
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="qa_user_profile_save"',
				),
			),
		);
	}
}
