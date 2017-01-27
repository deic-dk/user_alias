<?php
/*
 * ownCloud user email alias app
 *
 * @author Christian Brinch
 * @copyright 2014 Christian Brinch, DeIC.dk
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class OC_User_Alias
{
	/*
	 * @brief  Search for user aliases
	 * @param  Owncloud user ID
	 * @return array of aliases
	 * Search for all aliases belonging to the user
	 *
	 */
	public static function searchAlias($uid) {
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbSearchAlias($uid);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('search_alias', array('uid'=>$uid),
					false, true, null, 'user_alias');
		}
		return $result;
	}
	
	public static function dbSearchAlias($uid){
		$query = \OCP\DB::prepare('SELECT * FROM `*PREFIX*user_alias` where `OC_username` = ?');
		$result = $query->execute( array( $uid ));
		$aliases = array();
		while ( $row = $result->fetchRow()) {
			$aliases[$row['email_alias']] = $row['verified'];
		}
		return $aliases;
	}


	/*
	 * @brief  Check if alias exists
	 * @param  alias
	 * @return bool
	 * Search database for alias
	 */
	public static function checkAlias($alias){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbCheckAlias($alias);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('check_alias', array('alias'=>$alias),
					false, true, null, 'user_alias');
		}
		return $result;
	}

	public static function dbCheckAlias($alias){
		$query = \OCP\DB::prepare('SELECT * FROM `*PREFIX*user_alias` where `email_alias` = ?');
		$result = $query->execute( array( $alias ));
		return $result->fetchRow() ? true : false ;
	}

	/*
	 * @brief  Add alias to user
	 * @param  New alias, Owncloud user ID
	 * @return bool
	 * Add new alias
	 * */
	public static function addAlias($alias, $uid){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbAddAlias($alias, $uid);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('add_alias', array('alias'=>$alias, 'uid'=>$uid),
					false, true, null, 'user_alias');
		}
		return $result;
	}

	public static function dbAddAlias($alias, $uid){
		if(!OC_User_Alias::checkAlias( $alias ) & !OC_User::userExists( $_POST['alias'])) {
			$activation= md5($alias.time());
			self::sendVerification($uid, $_POST['alias'], $activation);
			$query = \OCP\DB::prepare("INSERT INTO `*PREFIX*user_alias` ( `email_alias`,`OC_username`, `activation`, `verified`  ) VALUES( ?, ?, ?, ? )");
			$query->execute( array( $alias, $uid , $activation, '0'));
			return true;
		}
		else {
			return false;
		}
	}

 /*
	* @brief  Delete alias
	* @param  Alias to be deleted, Owncloud user ID
	* @return bool
	*
	* Delete alias
	*
	*/
	public static function deleteAlias($alias) {
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbDeleteAlias($alias);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('delete_alias', array('alias'=>$alias),
					false, true, null, 'user_alias');
		}
		return $result;
	}
	
  public static function dbDeleteAlias($alias) {
		$query = \OCP\DB::prepare("DELETE FROM `*PREFIX*user_alias` WHERE `email_alias` = ?");
		$result = $query->execute( array( $alias ));
		// Notify user
		if(\OCP\App::isEnabled('user_notification')){
			$user = \OC_User::getUser();
			\OCA\UserNotification\Data::send('user_alias', 'You removed an email alias: '.$alias, array(),
					/*Hack - should be a tag for translation()*/'You removed an email alias: '.$alias,
					array(), '', '', $user, \OCA\FilesSharding\Lib::TYPE_SERVER_SYNC,
					\OCA\UserNotification\Data::PRIORITY_MEDIUM, $user);
		}
		return $result;
  }


	/*
	 * @brief  Send verification email
	 * @param $uid user ID
	 * @param  Target email address
	 * @return bool
	 * This function will send an email with a one-time link for verification
	 */
	public static function sendVerification($uid, $alias, $activation){
		$to      = $alias;
		$name    = OCP\User::getDisplayName($uid);
		$subject = 'Plese verify your email alias';
		$from    = \OCP\Config::getSystemValue('fromemail', '');
		$from    = \OCP\Config::getSystemValue('fromemail', '');
		$message = 'Click on this link to verify your alias: '.
		//OC::$WEBROOT.'/index.php/settings/personal?code='.$activation;
			OCP\Util::linkToAbsolute('', 'index.php/settings/personal', array('code' => $activation));
		$defaults = new \OCP\Defaults();
		$senderName = $defaults->getName();
		\OCP\Util::sendMail($to, $name, $subject, $message, $from, $senderName);
  }

	/*
	* @brief Verify user
	* @param activation code
	* @return bool
	*
	* This function will activate an email alias
	*
	*/
	public static function verifyAlias($code) {
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbVerifyAlias($code);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('verify_alias', array('code'=>$code),
					false, true, null, 'user_alias');
		}
		return $result;
	}


	public static function dbVerifyAlias($code) {
		$query = \OCP\DB::prepare('SELECT * FROM `*PREFIX*user_alias` where `activation` = ?');
		$result = $query->execute( array($code));
		$row = $result->fetchRow();
		if(empty($row)){
			return false;
		}
		$alias = $row['email_alias'];
		$query = \OCP\DB::prepare("UPDATE `*PREFIX*user_alias` SET `verified` = true WHERE `activation` = ?");
		$result = $query->execute(array($code));
		// Notify user
		if(\OCP\App::isEnabled('user_notification')){
			$user = \OC_User::getUser();
			\OCA\UserNotification\Data::send('user_alias', 'You added an email alias: '.$alias, array(),
					/*Hack - should be a tag for translation()*/'You added an email alias: '.$alias,
					array(), '', '', $user, \OCA\FilesSharding\Lib::TYPE_SERVER_SYNC,
					\OCA\UserNotification\Data::PRIORITY_MEDIUM, $user);
		}
		return $result;
 	}

	/*
	 * @brief Get a list of all user aliases
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return array associative array with all aliases (value) and corresponding uids (key)
	 * Get a list of all user aliases and user ids.
	 */
	public static function getAliases($search = '', $limit='', $offset='') {
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			$result = self::dbGetAliases($search, $limit, $offset);
		}
		else{
			$result = \OCA\FilesSharding\Lib::ws('get_aliases',
					array('search'=>$search, 'limit'=>$limit, 'offset'=>$offset),
					false, true, null, 'user_alias');
		}
		return $result;
	}

	public static function dbGetAliases($search = '', $offset=null, $offset=null) {
		$limit = empty($limit)?null:$limit;
		$limit = empty($offset)?null:$offset;
		$displayNames = array();
		$query = \OCP\DB::prepare("SELECT `OC_username`,`email_alias` FROM `*PREFIX*user_alias` WHERE `verified` = '1' AND `email_alias` LIKE ?", $limit, $offset);
		$users = $query->execute( array('%'.$search.'%'));
		while ( $row = $users->fetchRow()) {
			$displayNames[$row['OC_username']] = $row['email_alias'];
		}
		return $displayNames;
	}

}
