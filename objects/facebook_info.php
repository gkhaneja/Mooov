<?php

class FBInfo
{
private $username;
private $firstname;
private $lastname;
private $email;
private $gender;
private $workplace;
private $study;
private $current_city;
private $hometown;
private $pic;


public function __construct ($row)
{
  
      $work_data =  unserialize($row['workplace']);
      $hometown_data = unserialize($row['hometown']);
      $location_data =  unserialize($row['location']);
      $edu_data = unserialize($row['education']);
      $this->getworkplaces($work_data);
      $this->getstudy($edu_data);
                        $this->hometown  = $hometown_data['name'];
                        $this->current_city   = $location_data['name'];
                        $this->pic = 'http://graph.facebook.com/' . $row['fbid'] . '/picture';
     $this->gender= $row['gender'];
     $this->email = $row['email'];
     $this->firstname = $row['firstname'];
     $this->lastname =  $row['lastname'];
     $this->username = $row['username'];
}
public function getData()
{

 $fb_array = array( "firstname" => $this->firstname,  "lastname" => $this->lastname , "username" => $this->username,
                           "works_at" => $this->workplace,"lives_in" => $this->current_city , "hometown" => $this->hometown, "study_at" => $this->study,"image_url" =>  $this->pic, "gender" => $this->gender);

return $fb_array;
}
public function getworkplaces($workplace)
{
// for now get the first one
$this->workplace = $workplace[0]['employer']['name'];

}

public function getstudy($education)
{

foreach($education as $e)
{
  $priority = 4;
  if($e['type'] == 'Graduate School')
   {
              $this->study = $e['school']['name'];
               return;
   }
   else if($e['type'] == 'High School' && $priority >= 0)
   {
              $this->study = $e['school']['name'];
               $priority = 1;
   }
   else if($e['type'] == 'School' && $priority >= 1)
   {
              $this->study = $e['school']['name'];
               $priority = 2;
   }
  else if($priority >= 2)
  {
   	      $this->study = $e['school']['name'];
              $priority = 3;      
  }
}

}
}
/*
 $db1 = new PDO('mysql:dbname=hopon;host=localhost', 'root', 'root');
 
$q = "select * from user_details where user_id=10";
 foreach($db1->query($q)  as $row)
   $result = $row;
 print_r($result);
 $fb = new FBInfo($result);
 print_r($fb->getData());
 
*/
?>

