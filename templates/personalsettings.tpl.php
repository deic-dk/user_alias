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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details. 
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>. 
 *
 */

OCP\Util::addStyle('user_alias', 'personalsettings'); 

?>

<fieldset class="section"  id="user_alias">
	<h2><?php p($l->t('Email aliases'));?></h2>

	<br />

	<div id="aliascontainer">
<?php
$aliases = OC_User_Alias::searchAlias(OC_User::getUser());
$result = ""; 

foreach ($aliases as $alias => $verified) {
if($verified){
	$status = $l->t('Verified');
} else {
	$status = $l->t('Not verified');
}
$result .= "<div".
	(!empty($_['verified_alias_code'])?" verified_alias_code=".$_['verified_alias_code']:'').
	" class='aliasaction' data-alias=\"".$alias."\" > ".$alias.
	"<img class='deletebutton' title='".$l->t('Delete alias')."' src=" .
	OCP\Util::imagePath('core', 'actions/delete.png') . " /> ".$status."</div>";
}

echo $result;
?>
	</div>

	<input type="text" name='alias' id="alias" placeholder="<?php p($l->t('New alias'));?>"/>
	<label class="button" id='add'><?php p($l->t('Add alias'));?></label> 
	<p id="error"></p>

</fieldset>

