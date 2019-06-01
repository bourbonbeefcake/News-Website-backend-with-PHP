<?php


/**
 * userQueries.class.php
 *
 * Contains all functions and queries related to users
 *
 * @author     Triantafyllidis Antonios
 * @copyright  2017 Triantafyllidis Antonios
 */

//class User contains all functions and querries related to the user
class User
{
    private $db;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //gets all variables from the fields of the register form and inserts the user to the database
    public function register($username,$password,$email,$newsletter)
    {
       try
       {
           $stmt_insert = $this->db->prepare('INSERT INTO users (user_name, user_password, user_email, on_newsletter) VALUES(:username, :password, :email, :newssigned)');

           //of course use encryption to their password and salt it with their username
           $criteriaInsert = [
             'username' => $username,
             'password' => password_hash($password, PASSWORD_DEFAULT),
             'email' => $email,
             'newssigned' => $newsletter
           ];
           $stmt_insert->execute($criteriaInsert);
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//gets the username and the password from the login form and checks which of the hashes in the databases matches with the password inserted.
//when there is a match this function returns true.
//if there is no match this function returns false.
    public function login($username,$password)
    {
      $stmt = $this->db->prepare("SELECT * FROM users WHERE is_deleted ='n'");
      $stmt->execute();
      $users = $stmt->fetchAll();


      foreach ($users as $thisUser) {
        if ($thisUser['user_name'] === $username && password_verify($password,$thisUser['user_password'])) {
          $_SESSION['loggedin'] = $thisUser['user_ID'];
          $_SESSION['username'] = $thisUser['user_name'];
          return true;
        }
      }
      return false;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
//returns true if there is a user logged in
   public function is_loggedin()
   {
      if(isset($_SESSION['loggedin']))
      {
         return true;
      }
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //takes the url as an arguement and redirects to it
   public function redirect($url)
   {
       header("Location: $url");
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //destroys and unsets the session, succesfuly logging the user out
   public function logout()
   {
        session_destroy();
        unset($_SESSION['loggedin']);
        unset($_SESSION['username']);
        return true;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //fetches all the users from the database and returns them
   public function getAllUserAccounts()
   {
     $stmt = $this->db->prepare("SELECT * FROM users");
     $stmt->execute();
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }

   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //takes a user's ID as an arguement, and checks if that user exist
   public function doesUserExist($userID){
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID = :id");

     $criteria = [
       'id' => $userID
     ];

     $stmt->execute($criteria);
     $result=$stmt->fetch();
     if (!empty($result)) {
       return true;
     }else {
       return false;
     }
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //fetches all the roles that exist in the database
   public function getAllRoles()
   {
     $stmt = $this->db->prepare("SELECT * FROM roles");
     $stmt->execute();
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //fetches all user attributes of the user CURRENTLY logged in
   public function getUserDetails()
   {
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID= :id");
     $stmt->execute(array(':id' => $_SESSION['loggedin']));
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //gets a user's ID as an arguement and returns that user's fetched attributes from the database
   public function getUserDetailsByID($id){
     $stmt = $this->db->prepare("SELECT * FROM users WHERE user_ID= :id");
     $stmt->execute(array(':id' => $id));
     $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //returns: the user name of the currently logged in user
   public function getUserName()
   {
     $userDetails = $this->getUserDetails();

     foreach ($userDetails as $value) {
       return $value['user_name'];
     }
   }
   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //gets: a user's ID
   //returns: the user's email
   public function getUserEmailByID($userID){
     $userDetails = $this->getUserDetailsByID($userID);

     foreach ($userDetails as $value) {
       return $value['user_email'];
     }
   }



   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //gets:a user's ID
   //returns: that user's user name
   public function getUserNameByID($id)
   {
     $userDetails = $this->getUserDetailsByID($id);
     foreach ($userDetails as $value) {
       return $value['user_name'];
     }
   }

   /////////////////////////////////////////////////////////////////////////////////////////////////////////
   //gets: the role level to check
   //returns: true if the CURRENT user's role level is equal or higher than the arguement
   //false if the CURRENT user's role level is lower than the arguement
   public function hasPermissions($roleLevel)
   {
     //admin - 1
     //author - 2
     //registered user - 3
     //else un registered user

     if($this->is_loggedin()){
       $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
       $stmt->execute(array(':id' => $_SESSION['loggedin']));
       $results=$stmt->fetch(PDO::FETCH_ASSOC);

       //if the logged in user has role level equal or higher than required, return true
       if($roleLevel >= (int)$results['user_role'])
       {
         return true;
       }
     }
   }


      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //returns: the CURRENT user's role ID
      public function getUserRole()
      {
        $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
        $stmt->execute(array(':id' => $_SESSION['loggedin']));
        $results=$stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$results['user_role'];
      }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: the role ID
      //returns: the CURRENT user's role name
      public function getUserRoleByNumber($roleID)
      {
        $stmt = $this->db->prepare("SELECT role_name FROM roles WHERE role_id= :id");
        $stmt->execute(array(':id' => $roleID));
        $results=$stmt->fetch(PDO::FETCH_ASSOC);
        return $results['role_name'];
      }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
  //gets: a user's ID
  //returns: that user's role ID
    public function getUserRoleByID($userID)
    {
      $stmt = $this->db->prepare("SELECT user_role FROM users WHERE user_ID= :id");
      $stmt->execute(array(':id' => $userID));
      $results=$stmt->fetch(PDO::FETCH_ASSOC);
      return $results['user_role'];
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
      //returns: an array with all the permissions of the curent user depending on his role
      public function getPermissions()
      {
        $userRole = $this->getUserRole();
        if($userRole === 1){
          $perms = array(
            array(
              'link' => 'addArticle.php',
              'name' => 'Add Article'
            ),
            array(
              'link' => 'addCategory.php',
              'name' => 'Add Category'
            ),
            array(
              'link' => 'editCatName.php',
              'name' => 'Edit Category Name'
            ),
            array( //edit delete add user
              'link' => 'manageUsers.php',
              'name' => 'Manage Users'
            ),
            array( //change article category, editing title or text. deleting articles
              'link' => 'manageArticles.php',
              'name' => 'Manage Articles'
            ),
            array(
              'link' => 'deleteCategory.php',
              'name' => 'Delete Category'
            ),
            array( //make visible, delete
              'link' => 'approveComments.php',
              'name' => 'Approve Comments'
            ),
          );
        }else if($userRole === 2){
          $perms = array(
            array(
              'link' => 'addArticle.php',
              'name' => 'Add Article'
            ),
            array(
              'link' => 'addCategory.php',
              'name' => 'Add Category'
            ),
            array( //change article category, editing title or text. deleting articles (ONLY HIS ARTICLES)
              'link' => 'manageArticles.php',
              'name' => 'Manage Articles'
            ),
            array( //make visible, delete (COMMENTS ON HIS ARTICLES)
              'link' => 'approveComments.php',
              'name' => 'Approve Comments'
            ),
          );
        }else {
          return null;
        }
        return $perms;
      }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets: a user names
        //returns: true if that user name exists in the database
        //false if the given name is unique
        public function hasSameName($name){
          $stmt_username = $this->db->prepare('SELECT * FROM users WHERE user_name= :username');

          $criteria = [
           'username' => $name
          ];

          $stmt_username->execute($criteria);
          $results = $stmt_username->fetchAll();

          //if there is another username with the same name, let the user know that they should try again with a different one
          if(sizeof($results) > 0){
            return true;
        }else{
          return false;
        }
      }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //gets: a user's email
        //returns: true if the givem email exists
        //false if the given email is unique
        public function hasSameEmail($email){
          $stmt_email = $this->db->prepare('SELECT * FROM users WHERE user_email= :email');

          $criteria = [
           'email' => $email
          ];

          $stmt_email->execute($criteria);
          $results = $stmt_email->fetchAll();

          //if there is another email with the same address, let the user know that they should try again with a different one
          if(sizeof($results) > 0){
            return true;
        }else{
          return false;
        }
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //returns: the CURRENT user's ID
      public function getUserID(){
        if($this->is_loggedin()){
          return $_SESSION['loggedin'];
        }else {
          return 0;
        }
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: a user's ID
      //Updates the attribute "is_deleted" of user with the given ID, to "y", marking that user's account as deleted.
      //The entry of the user though is still in the database.
      public function deleteUser($userID){
        $stmt = $this->db->prepare('UPDATE users SET is_deleted = "y" WHERE user_id = :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: a user's ID
      //returns: true if that user's "is_deleted" attribute is "y" hence the user's account is marked as deleted
      //false if it is "n"
      public function isDeleted($userID){
        $stmt = $this->db->prepare('SELECT is_deleted FROM users WHERE user_ID= :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
        $result = $stmt->fetch();

        if ($result['is_deleted'] === 'y') {
          return true;
        }elseif($result['is_deleted'] === 'n') {
          return false;
        }
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: a user's ID
      //Updates the attribute "is_deleted" of user with the given ID, to "n", marking that user's account as active
      public function restoreUser($userID){
        $stmt = $this->db->prepare('UPDATE users SET is_deleted = "n" WHERE user_id = :id');

        $criteria = [
          'id' => $userID
        ];
        $stmt->execute($criteria);
      }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: a role ID, a user's ID
      //Updates that user's role to the role given
      public function changeRole($newRole, $userID)
      {
        $stmt = $this->db->prepare("UPDATE users SET user_role = :newRole WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newRole' => $newRole));
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: the name that will be set, a user's ID
      //Updates that user's name to the new one
      public function changeName($newName, $userID){
        $stmt = $this->db->prepare("UPDATE users SET user_name = :newName WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newName' => $newName));
      }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: the email that will be set, a user's ID
      //Updates that user's email to the new one
      public function changeEmail($newMail, $userID){
        $stmt = $this->db->prepare("UPDATE users SET user_email = :newMail WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newMail' => $newMail));
      }

      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //gets: the state that will be set, a user's ID, STATES:
      //Updates that user's name to the new one
      public function changeNewsletter($newState, $userID){
        $stmt = $this->db->prepare("UPDATE users SET on_newsletter = :newState WHERE user_id = :id");
        $stmt->execute(array('id' => $userID, 'newState' => $newState));
      }
      /////////////////////////////////////////////////////////////////////////////////////////////////////////
      //sends mail to newsletter subscribed users providing a link to the letter of the new article posted.
      //https://www.tutorialrepublic.com/php-tutorial/php-send-email.php to send a mail with PHP
      public function sendMailToSubscribedUsers($articleObj){
        $stmt = $this->db->prepare('SELECT user_email FROM users WHERE on_newsletter ="on"');
        $stmt->execute();
        $emails = $stmt->fetchAll();


        $subject = 'New article posted at Northampton News!';
        $from = 'admin@northnews.com';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Create email headers
        $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();

        // Compose a simple HTML email message
        $message = '<html><body>';
        $message .= '<h1 style="color:#f40;">New Article!</h1>';
        $message .= '<p>A new article has been posted in Northampton news. Follow the link to read:</p>';
        $message .= '<a href="article.php?articleID='.$articleObj->getID().'">Click Here</a>';
        $message .= '</body></html>';

        foreach ($emails as $email) {
          mail($email['user_email'], $subject, $message, $headers);
        }
      }


    }



?>
