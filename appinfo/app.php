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

if(isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI']=='/' ||
		strpos($_SERVER['REQUEST_URI'], "/js/")>0 ||
		(strpos($_SERVER['REQUEST_URI'], "/apps/")>0 ||
				strpos($_SERVER['REQUEST_URI'], "/apps/")===0 || strpos($_SERVER['REQUEST_URI'], "apps/")===0) &&
		strpos($_SERVER['REQUEST_URI'], "/user_alias")==false)){
	return;
}

OCP\App::checkAppEnabled('user_alias');

OC::$CLASSPATH['OC_User_Alias']='apps/user_alias/lib/user_alias.php';
OC::$CLASSPATH['aliasLookup']='apps/user_alias/lib/auth.php';

require_once 'user_alias/lib/auth.php';

aliasLookup::tryAliasLogin();

OCP\Util::addScript('user_alias','script');
// This seems to trigger strange save password popups on Firefox
OCP\Util::addScript('user_alias','google');
OCP\Util::addStyle('user_alias','google');

OCP\App::register(Array(
    'order' => 40,
    'id' => 'user_alias',
    'name' => 'user_alias'
));

OCP\App::registerPersonal('user_alias', 'personalsettings');

