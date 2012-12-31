<?php
require_once('conf/google.conf');
class  GeoCoding
{

private $googleURL;
private $addresses;

public function  __construct()
{
 $this->googleURL = GOOGLE_URL;
}

var $mapped_cities = array('thane' => 'mumbai');


public function reverseGeo($lat,$lng)
{
 //$lat =  '19.1154908';
 //$lng = '72.87269519999995';
 $geoCodeURL = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&sensor=false"; 

 $results = json_decode(file_get_contents($geoCodeURL), true); 
 $resp = array();
 if($results['status'] != 'OK')
   return;
 $results = $results['results'];
 foreach ($results as $subresult)
 {
        $resp = array();
             $resp['formatted_address'] = $subresult['formatted_address'];
	     $subresult = $subresult['address_components'];
             foreach($subresult as $address)
               {

                if($address['types'][0] == 'sublocality')
                   {
                          $locality_shortname = $address['short_name'];
                         $locality_longname = $address['long_name'];
                          $resp['sublocality_shortname'] = $locality_shortname ;
                          $resp['sublocality_longname'] = $locality_longname;
                   }

		if($address['types'][0] == 'locality')
                   {
			  $locality_shortname = $address['short_name'];
			 $locality_longname = $address['long_name'];
                          $resp['locality_shortname'] = $locality_shortname ;
                          $resp['locality_longname'] = $locality_longname;
		   }
                if($address['types'][0] == 'postal_code')
		 {

			 $resp['postal_code'] = $address['short_name'];
                 }
                if($address['types'][0] == 'administrative_area_level_1')
		{

			$resp['state'] = $address['long_name'];
			$resp['state_code'] = $address['short_name'];

                } 
 		if($address['types'][0] == 'country')
                {
                        $resp['country'] = $address['long_name'];
                        $resp['coutry_code'] = $address['short_name'];
                } 
               
               }
   $this->addresses[] =$resp;
 } 
 return $this->addresses;

}
public function getCity($lat,$lan)
{
  $results = $this->reverseGeo($lat,$lan);
  $city='';
  foreach($results as $result)
  {
    if(isset($result['locality_shortname']))
      {
        $city= $result['locality_shortname'] ;
        break;
      }
  }
  if(isset($this->mapped_cities[strtolower($city)]))
    $city = $this->mapped_cities[strtolower($city)];

  return $city;
}

}
/*
$gc = new GeoCoding();
//$r = $gc->getCity('19.1231172','72.8771669');
$r = $gc->getCity('19.224','72.9177');
print_r($r);
*/
?>
