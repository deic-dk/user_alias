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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.	If not, see <http://www.gnu.org/licenses/>.
 *
 */

function add_alias(){
	$.ajax({
		type:'POST',
		url:OC.linkTo('user_alias', 'ajax/addalias.php'),
		dataType:'json',
		data: {alias: $('#user_alias #alias').val()},
		async:false,
		success:function(s){
			if(s){
				$("#error").html(s);
			}
			else {
				$("#error").html(" ");
				$("#user_alias #alias").val('');
			}
		}
	});
	load_aliases();
	}

	function del_alias(alias){
		$.ajax({
			type:'POST',
			url:OC.linkTo('user_alias', 'ajax/delalias.php'),
			dataType:'json',
			data: alias ,
			async:false,
			success:function(s){
				if(s){
					$("#error").html(s);
				}
			},
			error:function(s){
				if(s){
					$("#error").html(s);
				}
			}
		});
	}

	function load_aliases(){
		$.ajax({
			url:OC.linkTo('user_alias','ajax/search.php'),
			type:'GET',
			success: function(result){
					$('#aliascontainer').html(result);
			}
		});
	}
	
	function getParam(href, key) {
	  var results = new RegExp('[\?&]' + key + '=([^&#]*)').exec(href);
	  if (results==null){
	     return '';// null;
	  }
	  else{
	     return results[1] || 0;
	  }
	}

	function getGetParam(key) {
	  return this.getParam(window.location.href, key);
	}

	$(document).ready(function() {
		
		$("#aliascontainer").on('click', '.deletebutton', function() {
			var proceed = true;
			// do some sanity checks here
			if(proceed){
				del_alias( {alias:$(this).parent().data('alias')} );
				load_aliases();
			}
		});

		$("#user_alias #add").bind('click', function() {
			var proceed = true;
			// do some sanity checks here

			if(proceed){
				add_alias();
			}
		});
		
		var code = getGetParam('code');
		if(code){
			if($('.aliasaction[verified_alias_code='+code+']').length){	
				OC.dialogs.alert( 'New email alias added!' , 'Email alias' ) ;
			}
			else{
				OC.dialogs.alert( 'Invalid code for email alias' , 'Email alias' ) ;
			}
			// This is theme-specific, but shouldn't hurt with other themes
			window.location.href = '#userapps';
			//
			$('html, body').animate({
        scrollTop: $("#user_alias").offset().top
    }, 2000);
		}
		
	});

