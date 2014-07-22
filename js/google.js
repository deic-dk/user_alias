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

function getprofile(){
  var user = gapi.client.plus.people.get( {'userId' : 'me'} );   
  document.cookie = 'GLIN=LoggedinwithGoogle; expires=0; path=/'
  user.execute( function(profile) {
    $.ajax({                                                                                                                          
      type: 'POST',                                                                                                                   
      url:OC.linkTo('user_alias', 'ajax/google.php'),                                                                                 
      dataType:'json',                                                                                                                
      data: 'email='+profile.emails[0].value,                                                                                         
      async:false,                                                                                                                    
      success:function(s){                                                                                                            
      },                                                                                                                              
    });
    location.reload();
  });
}

function onSignInCallback(authResult) {
  if (authResult['access_token']) {    
    $('#gConnect').hide(); 
    gapi.client.load('plus','v1', function(){
      getprofile();
    });
  } else if (authResult['error']) {
    console.log('There was an error: ' + authResult['error']);
  } 
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}



$(document).ready(function(){  

  (function() {                                                                                                            
    var po = document.createElement('script');                                                                  
    po.type = 'text/javascript'; po.async = true;                                                               
    po.src = 'https://plus.google.com/js/client:plusone.js';                                                    
    var s = document.getElementsByTagName('script')[0];                                                         
  s.parentNode.insertBefore(po, s);                                                                           
  })();                                                                                                        

  $('<div id=\"gConnect\"> <button class=\"g-signin\" data-clientId=\"601574550986-1os7p2hifih30m227otefjbqc6qmo9gi.apps.googleusercontent.com\" data-accesstype=\"offline\" data-callback=\"onSignInCallback\" data-theme=\"dark\" data-cookiepolicy=\"single_host_origin\"></button> </div>  ').css({'margin-left': 'auto','margin-right': 'auto'}).appendTo('#login form ');

  $('#gConnect').hide();
  $('#login-guest-img').click(function(){                                                                            
    $('#gConnect').toggle('slow', 'linear');                                                                                                             
    /* This clumsy hack is neccessary for firefox to render the google button */
    $('#___signin_0').css({'width':'114px', 'height': '36px'})
    $('#___signin_0').children("iframe").css({'width':'114px', 'height': '36px'})
    /* End of hack */
  });  

  $('#logout').bind('click', function(){
    if(readCookie('GLIN')){
      window.open('https://accounts.google.com/Logout', "Sign out", "status=1,width=450,height=650");
      document.cookie = 'GLIN=""; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';       
    }
  });

});

