jQuery(document).ready(function ()
{
    //function initMap() {
//        var geocode = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + wccm_map.address + '&sensor=false&key=AIzaSyArOnqf0FHb0EAAxpNt5OmqnNS-FldclHk';
//        alert(geocode);
//        jQuery.getJSON(geocode, function (data)
//        {
//            alert(data);
//            if (data.results[0].geometry.location.length > 0)
//            {
//                alert(data.results[0].geometry.location.lat);
//                // The location of Uluru
//                var uluru = {lat: data.results[0].geometry.location.lat, lng: data.results[0].geometry.location.lng};
//                // The map, centered at Uluru
//                var map = new google.maps.Map(
//                        document.getElementById('map-container'), {zoom: 4, center: uluru});
//                // The marker, positioned at Uluru
//                var marker = new google.maps.Marker({position: uluru, map: map});
//            }
//        });
    //}
//	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
//	var osmAttrib='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
//	var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib});
//	var map = new L.Map('map-container').addLayer(osm).setView([51.505, -0.09], wccm_map.zoom_level);
//	
//	//geocoding
//	var geocode = 'https://www.mapquestapi.com/geocoding/v1/address?key=AIzaSyArOnqf0FHb0EAAxpNt5OmqnNS-FldclHk&location=' + wccm_map.address;
//
//	// use jQuery to call the API and get the JSON results
//	jQuery.getJSON(geocode, function(data) 
//	{
//		if(data.results[0].locations.length > 0)
//		{
//			//console.log(data.results[0].locations[0].latLng.lat);
//		
//			map.setView([data.results[0].locations[0].latLng.lat, data.results[0].locations[0].latLng.lng], wccm_map.zoom_level);
//			var marker = L.marker([data.results[0].locations[0].latLng.lat, data.results[0].locations[0].latLng.lng]).addTo(map);
//		}
//	});
});

function wccm_map_on_geocoding_completed(result)
{
    console.log(results);
}