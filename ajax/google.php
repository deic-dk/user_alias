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

OCP\JSON::checkAppEnabled('user_alias');

if(OC_User::getUser()){
  return false;
} else {
  $query =  \OCP\DB::prepare('SELECT `OC_username` FROM `*PREFIX*user_alias` where `email_alias` = ? AND `verified` = ?');
  $result = $query->execute( array( $_POST["email"], '1' ));

  if($result){                                                                                                                                                               
    $row = $result->fetchRow();                                                                                                                                              
    OCP\JSON::encodedPrint($row['OC_username']);                                                                                                                                    
    $_SESSION['user_id']=$row['OC_username'];
    return true;                                                                                                                                                             
  }                
} 

