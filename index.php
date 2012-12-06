<html>
<head><meta content="text/html; charset=ISO-8859-1" http-equiv="content-type"><title>Hopin</title><style type="text/css"></style></head>

<body>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/_c/hopin.css">
<script src="http://code.jquery.com/jquery-1.7.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.8.21/jquery-ui.min.js" type="text/javascript"></script>
<script src="http://jquery-ui.googlecode.com/svn/tags/latest/external/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>


    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7B_JmOOy1C9lCXhRJhdLXUeZ36P_1RuM&libraries=places&sensor=false">
    </script>
    <script type="text/javascript">

      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(19.1250, 72.9510),
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
       var directionsDisplay;
       var directionsService = new google.maps.DirectionsService();
        var map = new google.maps.Map(document.getElementById('map_canvas'),
            mapOptions);
	
          
    directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
    directionsDisplay.setMap(map);
    directionsDisplay.suppressMarkers = true;
    var input = document.getElementById('source');
    var input1 = document.getElementById('destination');
    var options = {
 		 types: ['establishment']
	};

	autocomplete = new google.maps.places.Autocomplete(input, options);
 	autocomplete1 = new google.maps.places.Autocomplete(input1, options);

    var marker = new google.maps.Marker({
      map: map,
      title:  "Your Source Location",
      draggable: true
    });

    var marker1 = new google.maps.Marker({
      map: map,
      title:  "Your Destination Location",
      draggable: true
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(16);
      }

      var image = "http://www.google.com/mapfiles/marker_green.png";
      marker.setIcon(image);
      marker.setPosition(place.geometry.location);
   
    });
     google.maps.event.addListener(autocomplete1, 'place_changed', function() {
      var place = autocomplete1.getPlace();
      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        //map.setZoom(16);
      }

      var image = "http://www.google.com/mapfiles/marker.png";
      marker1.setIcon(image);
      marker1.setPosition(place.geometry.location);
      var selectedMode = "DRIVING";	
      var request = {
      origin: autocomplete.getPlace().geometry.location ,
      destination: autocomplete1.getPlace().geometry.location,
      // Note that Javascript allows us to access the constant
      // using square brackets and a string value as its
      // "property."
      travelMode: google.maps.TravelMode.DRIVING
      };
	console.log(autocomplete1.getPlace().geometry.location);
      directionsService.route(request, function(response, status) {
              console.log(response);
	    if (status == google.maps.DirectionsStatus.OK) {
              console.log(status);
      		directionsDisplay.setDirections(response);
    		}
 	 });
   
    });

    }
    $(document).ready(function(){
    initialize();
    console.log("maps");
    });
    </script>
     <style>p { color:red; }</style>
    <div class="logo"> hOpIn </div>
    <div class="loc_div">
    <input class="location" type="text" id="source" name="source" placeholder="Source" />
    <br><br>
    <input class="location" type="text" id="destination" name="dest" placeholder="Destination" />
    </div>
    <div id="map_canvas"></div>
 </body>
</html>   
   
