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


function add_alias(){
  $.ajax({
    type:'POST',
  url:OC.linkTo('user_alias', 'ajax/addalias.php'),
  dataType:'json',
  data:$('form#user_alias').serialize(),
  async:false,
  success:function(s){
    if(s){                                                                                                     
      $("#error").html(s);
    } else {
      $("#error").html(" ");
      document.getElementById("user_alias").reset();
    }
  },      

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


  $(document).ready(function() {

    $("#aliascontainer").on('click', '.deletebutton', function() {
      var proceed = true;
      // do some sanity checks here

      if(proceed){
        del_alias( {alias:$(this).parent().data('alias')} );
        load_aliases();
      }
    });



    $("form#user_alias fieldset.personalblock #add").bind('click', function() {
      var proceed = true;
      // do some sanity checks here

      if(proceed){
        add_alias();
      }
    });
  });

