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
private $current_location;
private $hometown;


public function __construct ($arguments)
{
  
      $work_data =  unserialize($row['workplace']);
      $hometown_data = unserialize($row['hometown']);
      $location_data =  unserialize($row['location']);
                        $work_place  = $work_data[0]['employer']['name'];
                        $hometown  = $hometown_data['name'];
                        $current_city   = $location_data['name'];
                         $pic = 'http://graph.facebook.com/' . $row['fbid'] . '/picture';
                        $fb_array = array( "firstname" => stripslashes($row['firstname']), "lastname" => stripslashes($row['lastname']),
                           "works_at" => $work_place,"lives_in" => $current_city , "hometown" => $hometown, "image_url" =>  $pic);



}

public function getworkplaces($workplace)
{
// for now get the first one
$this->workplace = $work_data[0]['employer']['name'];

}

public function getstudy($education)
{

foreach($education as $e)
{
  if($e['type'] == 'Graduate School')
   {
              $this->study = $e['school']['name'];
               return;
   }
   if($e['type'] == 'High School')
   {
              $this->study = $e['school']['name'];
               return;
   }
   if($e['type'] == 'School')
   {
              $this->study = $e['school']['name'];
               return;
   }
   $this->study = $e['school']['name'];
}

}


