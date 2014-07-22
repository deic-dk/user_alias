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
   *                                                                                                                       
   * Search for all aliases belonging to the user
   *
   */   
  public static function searchAlias( $uid )
  {
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
   *                                                                                                                       
   * Search database for alias
   *
   */      
  public static function checkAlias( $alias )
  {
    $query = \OCP\DB::prepare('SELECT * FROM `*PREFIX*user_alias` where `email_alias` = ?'); 
    $result = $query->execute( array( $alias ));          

    return $result->fetchRow() ? true : false ;
  }


  /*                                                                                                                      
   * @brief  Add alias to user
   * @param  New alias, Owncloud user ID                                                                   
   * @return bool                                                                                                        
   *                                                                                                                       
   * Add new alias
   *
   */      
  public static function addAlias( $alias, $uid )
  {
    if ( !OC_User_Alias::checkAlias( $alias ) & !OC_User::userExists( $_POST['alias'])) { 
      $activation= md5($alias.time());
      OC_User_Alias::sendVerification( $_POST['alias'], $activation);
      $query = \OCP\DB::prepare("INSERT INTO `*PREFIX*user_alias` ( `email_alias`,`OC_username`, `activation`, `verified`  ) VALUES( ?, ?, ?, ? )");                   
      $query->execute( array( $alias, $uid , $activation, '0')); 
      return true;                                                                                                     
    } else {                                                                                                             
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
  public static function deleteAlias( $alias )
  {
    $query = \OCP\DB::prepare("DELETE FROM `*PREFIX*user_alias` WHERE `email_alias` = ?");                                           
    $query->execute( array( $alias ));            

    return true;
  }


  /*                                                                                                                      
   * @brief  Send verification email
   * @param  Target email address                                                                   
   * @return bool                                                                                                          
   *                                                                                                                       
   * This function will send an email with a one-time link for verification
   *
   */      
  public static function sendVerification( $alias, $activation )
  {
    $to      = $alias;
    $subject = 'Plese verify your DeIC storage alias';
    $message = 'Click on this link to verify your alias:
      https://data.deic.dk/index.php/settings/personal?code='.$activation;
    $headers = 'From: nocloud@data.deic.dk' . "\r\n" .
      'Reply-To: nocloud@data.deic.dk' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
  }

  /*                                                                                                                      
   * @brief  Verify user
   * @param  activation code                                                                   
   * @return bool                                                                                                          
   *                                                                                                                       
   * This function will activate an email alias
   *
   */      
  public static function verifyAlias( $code )
  {
    $query = \OCP\DB::prepare("UPDATE `*PREFIX*user_alias` SET `verified` = true WHERE `activation` = ?");                                           
    $result = $query->execute( array( $code ));            

    return $result;
  }

  /*                                                                                                                         
   * @brief Get a list of all user aliases                                                                              
   * @param string $search                                                                                                    
   * @param int $limit                                                                                                        
   * @param int $offset                                                                                                       
   * @return array associative array with all aliases (value) and corresponding uids (key)                              
   *                                                                                                                          
   * Get a list of all user aliases and user ids.                                                                            
   */                                                                                                                         
  public static function getAliases($search = '') {                                       
    $displayNames = array();                                                                                                

    $query = \OCP\DB::prepare("SELECT `OC_username`,`email_alias` FROM `*PREFIX*user_alias` WHERE `verified` = ? AND `email_alias` LIKE ?  ");                                           
    $users = $query->execute( array( '1', $search.'%'  ));            

    while ( $row = $users->fetchRow()) {
      $displayNames[$row['OC_username']] = $row['email_alias'];
    }

    return $displayNames;                                                                                                   
  }   






}
