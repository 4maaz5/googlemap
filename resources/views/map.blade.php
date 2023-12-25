<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Google Maps</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <style>
        #map {
            width: 100%;
            height: 600px;
            margin-top: 10px;
        }

        #info-container {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h1 class="text-center">Laravel Google Maps</h1>
    <div class="row w-50" style="margin-left: 350px;">
        <input id="location-search" type="text" class="form form-control" placeholder="Search for a location">

        <button onclick="getDirections()" class="btn btn-success mt-3">Get Directions</button>
        <button onclick="toggleStreetView()" class="btn btn-success mt-3 ml-3">Toggle Street View</button>
    </div>
    <div id="info-container" class="mt-3" style="margin-left:350px;"></div>
    <div id="map" class="mt-3" style="width: 1200px;height:500px;margin-left:120px;"></div>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTFw8M2rNpgCYiSXEENctwyEehQhPnF1s&callback=initMap&libraries=places&v=weekly" defer></script>
    <script>
        let map, activeInfoWindow, markers = [], searchCircle;
        let autocomplete;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 33.6494595, lng: 73.075933 },
                zoom: 15,
            });

            map.addListener("click", function (event) {
                mapClicked(event);
            });

            initMarkers();

            // Get the user's current location and mark it on the map
            getCurrentLocation();

            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("location-search")
            );
            autocomplete.addListener("place_changed", onPlaceChanged);

            // Add an event listener for right-click on the map
            google.maps.event.addListener(map, "rightclick", function (event) {
                alert("Right-clicked on the map at: " + event.latLng);
            });

            // Create a custom overlay (e.g., a polygon) on map load
            google.maps.event.addListenerOnce(map, "idle", function () {
                createCustomOverlay();
            });
        }

        function initMarkers() {
            // Your marker initialization code here
            // ...
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const currentLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        markCurrentLocation(currentLocation);
                    },
                    (error) => {
                        console.error("Error getting current location:", error);
                    }
                );
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        function markCurrentLocation(currentLocation) {
            const marker = new google.maps.Marker({
                position: currentLocation,
                map,
                title: "Current Location",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#00FF00",
                    fillOpacity: 1,
                    strokeWeight: 1,
                },
            });
            markers.push(marker);
        }

        function onPlaceChanged() {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.error("Place not found:", place);
                return;
            }

            // Center the map on the selected place
            map.panTo(place.geometry.location);

            // Clear existing markers and search circle
            clearMarkers();
            clearSearchCircle();

            // Add a marker for the selected place
            const marker = new google.maps.Marker({
                position: place.geometry.location,
                map,
                title: place.name,
            });
            markers.push(marker);

            // Display information about the selected place
            const infowindow = new google.maps.InfoWindow({
                content: `<b>${place.name}</b><br>${place.formatted_address}`,
            });
            infowindow.open({
                anchor: marker,
                shouldFocus: false,
                map,
            });
            activeInfoWindow = infowindow;

            // Draw a red circle around the selected place
            drawSearchCircle(place.geometry.location);

            // Calculate and display the distance
            calculateAndDisplayDistance();

            // You can also perform additional actions with the selected place
            console.log("Selected Place:", place);
        }

        function mapClicked(event) {
            // Your map click handling code here
            // ...
        }

        function clearMarkers() {
            // Your marker clearing code here
            // ...
        }

        function drawSearchCircle(location) {
            searchCircle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                map,
                center: location,
                radius: 400, // You can adjust the radius as needed
            });
        }

        function clearSearchCircle() {
            if (searchCircle) {
                searchCircle.setMap(null);
            }
        }

        function calculateAndDisplayDistance() {
            if (markers.length < 2) {
                console.error("Need at least two markers to calculate distance.");
                return;
            }

            const currentLocation = markers[0].getPosition();
            const destination = markers[1].getPosition();

            const service = new google.maps.DistanceMatrixService();

            service.getDistanceMatrix({
                origins: [currentLocation],
                destinations: [destination],
                travelMode: google.maps.TravelMode.DRIVING,
            }, function (response, status) {
                if (status == google.maps.DistanceMatrixStatus.OK) {
                    const distanceText = response.rows[0].elements[0].distance.text;
                    const durationText = response.rows[0].elements[0].duration.text;

                    console.log("Distance:", distanceText);
                    console.log("Duration:", durationText);

                    // Display the distance on the map
                    const infoContainer = document.getElementById("info-container");
                    infoContainer.innerHTML = `Distance: ${distanceText}, Duration: ${durationText}`;
                } else {
                    console.error("Error calculating distance:", status);
                }
            });
        }

        function getDirections() {
            if (markers.length < 2) {
                console.error("Need at least two markers to get directions.");
                return;
            }

            const currentLocation = markers[0].getPosition();
            const destination = markers[1].getPosition();

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();

            directionsRenderer.setMap(map);

            const request = {
                origin: currentLocation,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
            };

            directionsService.route(request, function (result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                } else {
                    console.error("Error getting directions:", status);
                }
            });
        }

        function createCustomOverlay() {
            const customOverlay = new google.maps.Polygon({
                paths: [
                    { lat: 33.6494595, lng: 73.075933 },
                    { lat: 33.6494595, lng: 73.076933 },
                    { lat: 33.6484595, lng: 73.076933 },
                    { lat: 33.6484595, lng: 73.075933 },
                ],
                strokeColor: "#0000FF",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#0000FF",
                fillOpacity: 0.35,
                map,
            });
        }

        function toggleStreetView() {
            const streetViewService = new google.maps.StreetViewService();

            streetViewService.getPanorama({ location: markers[0].getPosition() }, function (
                data,
                status
            ) {
                if (status === "OK") {
                    const panorama = map.getStreetView();
                    panorama.setPosition(data.location.latLng);
                    panorama.setPov({ heading: 270, pitch: 0 });
                    panorama.setVisible(true);
                } else {
                    console.error("Error loading Street View:", status);
                }
            });
        }
    </script>
</body>

</html>
