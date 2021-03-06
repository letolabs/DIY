<?php
/**
 * Email For Download element
 *
 * @package diy.org.cashmusic
 * @author CASH Music
 * @link http://cashmusic.org/
 *
 * Copyright (c) 2011, CASH Music
 * Licensed under the Affero General Public License version 3.
 * See http://www.gnu.org/licenses/agpl-3.0.html
 *
 **/
class SignIn extends ElementBase {
	public $type = 'signin';
	public $name = 'Sign-In';
	
	protected $hide = false;
	
	protected function init() {
		if ($this->status_uid == 'people_signintolist_200' && !$this->unlocked) {
			$this->unlock(); // unlock the element
		}
		if ($this->sessionGet('initialized_element_' . $this->element_id,'script')) {
			// element is initialized, meaning this is the closing embed
			// unset element initialized state:
			$this->sessionClear('initialized_element_' . $this->element_id,'script');
			$this->hide = true;
			if ($this->unlocked) {
				// unlocked, so clean out the buffer and don't display anything further
				if (ob_get_level()) ob_end_flush();
			} else {
				// locked, delete the protected output and send an empty string
				if (ob_get_level()) ob_end_clean();
			}
		} else {
			if (!$this->unlocked) {
				// element is locked. mark as initialized, start output buffering
				$this->sessionSet('initialized_element_' . $this->element_id,true,'script');
				ob_start();
			}
		}
	}

	public function getData() {
		if ($this->unlocked || $this->hide) {
			$this->setTemplate('empty');
		} else {
			if ($this->status_uid == 'people_signintolist_400') {
				$this->element_data['error_message'] = 'Could not verify your login. Please try again.';
			}
			$this->element_data['browserid_js'] = CASHSystem::getBrowserIdJS($this->element_id);
		}
		return $this->element_data;
	}
} // END class 
?>