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


// this function will get your google ID and compare this to the alias database
// and log you in it there is a possitive match.
function getprofile(){
  var user = gapi.client.plus.people.get( {'userId' : 'me'} );
    user.execute( function(profile) {
      $.ajax({
        type: 'POST',
        url:OC.linkTo('user_alias', 'ajax/google.php'),
        dataType:'json',
        data: 'email='+profile.emails[0].value,
        async:false,
        success:function(s){
          if(s){
            location.reload();
          }
        },
      });
    });
}

function signinCallback(authResult) {
  if (authResult['status']['signed_in']) {
    gapi.client.load('plus','v1', function(){
      getprofile();
    });
 } else {
  }
}



$(document).ready(function(){
  // this is some google stuff
  (function() {
    var po = document.createElement('script');
    po.type = 'text/javascript'; po.async = false;
    po.src = 'https://apis.google.com/js/client:platform.js';
    var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(po, s);
  })();

  // the actual google login button.
  $('<div id=\"gConnect\"><span id=\"signinButton\"><span class=\"g-signin\" data-callback=\"signinCallback\" data-clientid=\"601574550986-1os7p2hifih30m227otefjbqc6qmo9gi.apps.googleusercontent.com\" data-cookiepolicy=\"single_host_origin\"  data-scope=\"https://www.googleapis.com/auth/plus.login\"></span></span></div>').appendTo('#login form fieldset');
  $('<div id=\"gConnect\"><span id=\"signinButton\"><span class=\"g-signin\" data-callback=\"signinCallback\" data-clientid=\"601574550986-1os7p2hifih30m227otefjbqc6qmo9gi.apps.googleusercontent.com\" data-cookiepolicy=\"single_host_origin\"  data-scope=\"https://www.googleapis.com/auth/plus.login\"></span></span></div>').appendTo('span#logout').hide();


  if(window.location.href.indexOf("lostpassword") > -1){
    $('#gConnect').hide();
  }

  // log out of google if the user used google to login. This will open a new
  // window (not so elegant).
  $('#logout').bind('click', function(){
    gapi.auth.signOut();
  });

});

