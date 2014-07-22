user_alias
==========

Alias app which allows google and/or alias login


IMPORTANT:
In order to share with an alias, the following line needs to be added to getShareWith in core/ajax/share.php:

...
  if ($sharePolicy == 'groups_only') {                                                                        
    $users = OC_Group::DisplayNamesInGroups($usergroups, $_GET['search'], $limit, $offset);                 
  } else {                                                                                                    
    $users = OC_User::getDisplayNames($_GET['search'], $limit, $offset);                                    
    // to share with alias, add the following line mock-up; 
    $users += OC_User_Alias::getAliases($_GET['search']);                                  
  }                                                                                                           
...
