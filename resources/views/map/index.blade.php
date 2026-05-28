@extends('layouts.app')

@section('header', 'Plattegrond')

@section('content')
    <div class="relative min-h-[calc(100vh-6.8rem)] m-0 overflow-hidden px-4">
        <div id="map" class="absolute left-3/4 top-0 bottom-0 w-1/2 max-w-6xl -translate-x-1/2 bg-gray-900"></div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const map = window.L.map('map', {
                    crs: window.L.CRS.Simple,
                    minZoom: -2.6,
                    maxZoom: -0.5,
                    zoomControl: true
                });

                const imageUrl = @json(asset('img/camping_map.png'));
                const markers = [
                    { name: 'Ingang', position: [150, 220] },
                    { name: 'Zwemplas', position: [540, 2250] },
                    { name: 'Paard/Camperveld', position: [1400, 1590] },
                    { name: 'Varken/trekkerveld', position: [2450, 440] },
                    { name: 'Kip', position: [2600, 1520] },
                    { name: 'Schaap', position: [2590, 2680] },
                    { name: 'Koe', position: [2450, 3670] },
                    { name: 'Geit', position: [1270, 3650] },
                    { name: 'Konijn', position: [320, 3600] }
                ];

                const image = new Image();
                image.onload = () => {
                    const imageBounds = [[0, 0], [image.naturalHeight, image.naturalWidth]];

                    window.L.imageOverlay(imageUrl, imageBounds).addTo(map);
                    map.setMaxBounds(imageBounds);
                    map.options.maxBoundsViscosity = 1.0;
                    // map.options.zoomsnap = 0;
                    // map.options.zoomdelta = 0.1;

                    markers.forEach(({ name, position }) => {
                        window.L.marker(position)
                            .addTo(map)
                            .bindPopup(name);
                    });

                    map.fitBounds(imageBounds);
                    map.setZoom(map.getZoom() - 0.5);
                };
                image.src = imageUrl;
            });
        </script>
    </div>
@endsection