
function geocodePlaceId(geocoder, map, infowindow) {
        var placeId   = document.getElementById('placeid').value;
        var placename = document.getElementById('pac-input').value;
        
        geocoder.geocode({'placeId': placeId}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {
              map.setZoom(16);
              map.setCenter(results[0].geometry.location);
              var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
              });
                infowindow.setContent('<div><strong>' + placename + '</strong><br>' +
     results[0].formatted_address);
     infowindow.open(map, marker);
            } else {
              console.log('No results found');
            }
          } else {
           // console.log('Geocoder failed due to: ' + status);
          }
        });
      }

 function initAutocomplete() {

   if(document.body.contains(document.querySelector( '#map'  )))
    {
  var map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: -33.8688, lng: 151.2195},
    zoom: 13,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  if(document.body.contains(document.querySelector( '#placeid'  )))
  {
    placeId = document.getElementById('placeid').value;
  }
  else
  {
    placeId = 'undefined';
  }

var geocoder = new google.maps.Geocoder;
var infowindow = new google.maps.InfoWindow;

var input = document.getElementById('pac-input');
var searchBox = new google.maps.places.SearchBox(input);
map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

 
    


map.addListener('bounds_changed', function() {
searchBox.setBounds(map.getBounds());
});

var markers = [];

searchBox.addListener('places_changed', function() {
var places = searchBox.getPlaces();

if (places.length == 0) {
  return;
}

markers.forEach(function(marker) {
  marker.setMap(null);
});
markers = [];

var bounds = new google.maps.LatLngBounds();
places.forEach(function(place) {
  var icon = {
    url: place.icon,
    size: new google.maps.Size(71, 71),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(17, 34),
    scaledSize: new google.maps.Size(25, 25)
  };


  markers.push(new google.maps.Marker({
    map: map,
    icon: icon,
    placeId: place.place_id,
    title: place.name,
    position: place.geometry.location,
    anchorPoint: new google.maps.Point(0, -29)
  }));
  var marker = new google.maps.Marker({
    map: map,
    icon: icon,
    placeId: place.place_id,
    title: place.name,
    position: place.geometry.location,
    anchorPoint: new google.maps.Point(0, -29)
  })

  if (place.geometry.viewport) {

    bounds.union(place.geometry.viewport);
  } else {
    bounds.extend(place.geometry.location);
  }
  var placename   = place.name;
  var coordinates = place.geometry.location;
  var placeid     = place.place_id;
  var icon        = place.icon;
  var adress      = place.formatted_address;

//document.querySelector( '#adress_preview').innerHTML = adress;
if(document.body.contains(document.querySelector( '#place'  ))){
document.querySelector( '#place'  ).value = JSON.stringify({"placename":place.name,"placeadress": place.formatted_address,"placeid":place.place_id});
}
if(document.body.contains(document.querySelector( '#placename'  ))){document.querySelector( '#placename'    ).value = placename;};
if(document.body.contains(document.querySelector( '#placeadress'))){document.querySelector( '#placeadress'  ).value = adress;};
if(document.body.contains(document.querySelector( '#placeid'    ))){document.querySelector( '#placeid'      ).value = placeid;};


var infowindow = new google.maps.InfoWindow();


  infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
    'Place ID: ' + place.place_id + '<br>' + place.formatted_address);
infowindow.open(map, marker);

});
map.fitBounds(bounds);
});

  
setTimeout(function(){
geocodePlaceId(geocoder, map, infowindow,placeId) 
google.maps.event.trigger(map, 'resize');
},1000)

    

}

 }

  
window.onhashchange = function() { 
 var path     = window.location.href.split('/');
  if( path[5].match("editprofile|createlabel|privatelabel|sweetvelcard"))
  {
setTimeout(function(){
initAutocomplete();  
google.maps.event.trigger(map, 'resize');
   },500);
  }
}
