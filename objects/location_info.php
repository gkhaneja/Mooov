<?php
define(LATITUDE, '_latitude');
define(LONGITUDE, '_longitude');
define(ADDRESS, '_address');
define(LOCALITY, '_locality');
define(SRC, 'src');
define(DEST, 'dst');

class LocationInfo
{

private $type; // SRC or DST
private $latitude;
private $longitude;
private $address;
private $locality;

public function __construct($type,$arguments)
{
        $this->type = $type;

        if($type == SRC)
           $id = SRC;
        else
           $id = DEST;
	print_r($arguments);
	if(isset($arguments[$id.LATITUDE]))
                 $this->latitude = $arguments[$id.LATITUDE];

         if(isset($arguments[$id.LONGITUDE]))
                 $this->longitude = $arguments[$id.LONGITUDE];

	 if(isset($arguments[$id.ADDRESS]))
                 $this->address = $arguments[$id.ADDRESS];

         if(isset($arguments[$id.LOCALITY]))
                 $this->locality = $arguments[$id.LOCALITY];


}

public function get()
{
$type  = $this->type;
return array($type.LATITUDE => $this->latitude , $this.LONGITUDE => $this->longitude , $this.ADDRESS => $this->address , $this.LOCALITY => $this->locality);
}

}
/*
$var = array('src_latitude' => 19, 'src_longitude' => 20 , 'src_address' => 'B1701, SHimmering Heights, Powai' ,'src_locality' => 'Powai');
$obj = new LocationInfo(SRC,$var);
print_r($obj->get());
*/
?>
