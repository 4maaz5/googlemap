<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Include jQuery (Make sure to include jQuery before this script) -->
                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                    <!-- Your HTML content -->
                    {{-- <div id="user-location">
</div> --}}
                    <h3>Location form</h3>
                    <form method="post" action="{{ route('location') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" />
                        <label>Location Name</label>
                        <input type="text" name="location" placeholder="Location Name" id="latitude"
                            value="{{ old('latitude') }}" />
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" /><br><br>
                        <button
                            style="background-color: greenyellow;border:solid 2px black;radius:5px;margin-left:110px;">Submit</button>
                    </form>
                    <div id="map">
                        <Button style="background-color: red;" onclick="showMap(25.594095,85.137566)">
                        Mapping
                        </Button>
                    </div>
                    <br>
                    @php
                        $data = App\Models\User::all();
                    @endphp
                    <div>
                        <table style="border:solid 2px black;">
                            <tr style="border:solid 2px black;">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Location</th>
                            </tr>
                            @if (!empty($data))
                                @foreach ($data as $key)
                                    <tr style="border:solid 2px black;"
                                        onclick="showMap({{ $key->latitude }},{{ $key->longitude }})">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $key->name }}</td>
                                        <td>{{ $key->email }}</td>
                                        <td>{{ $key->latitude }}</td>
                                        <td>{{ $key->longitude }}</td>
                                        <td>{{ $key->location }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <div>No Data Found!</div>
                            @endif
                        </table>
                    </div><br>


                    <script>
                        function showMap(lat, lng) {
                            var myLatLng = { lat: lat, lng: lng };

                            // Create a new map centered at the specified coordinates
                            var map = new google.maps.Map(document.getElementById("map"), {
                                zoom: 10,
                                center: myLatLng
                            });

                            // Add a marker to the map
                            new google.maps.Marker({
                                position: myLatLng,
                                map: map
                            });
                        }
                        showMap(0, 0);
                    </script>
                    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&v=weekly" defer></script>


                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
