<?php
class SABoardTableOption extends SAOption {
	public function setDefaultOption() {
		$this->addDefaultOption('board_table_insert_mail_yn', 'Y');
		$this->addDefaultOption('board_table_reply_insert_mail_yn', 'Y');
	}
}
