import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const lat = parseFloat(this.element.dataset.lat);
        const lng = parseFloat(this.element.dataset.lng);


        // Initialiser la map
        const map = L.map(this.element).setView([lat, lng], 13);

        // Ajouter les tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        setTimeout(function(){
            map.invalidateSize();
        },200);

        // Marqueur initial
        const marker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`Lieu de la sortie`)
            .openPopup();

        // Ajouter le Geocoder
        const geocoder = L.Control.geocoder({
            defaultMarkGeocode: true,
            placeholder: "Rechercher un lieu..."
        }).addTo(map);

        // Récupérer les coordonnées choisies
        geocoder.on('markgeocode', (e) => {
            const coords = e.geocode.center;

            const rue = e.geocode.properties.address.square;
            const ville = e.geocode.properties.address.city;
            const obj = Object.values(e.geocode.properties.address)[0];
            console.log(e.geocode.properties.address);
            // Mettre à jour le marqueur
            marker.setLatLng(coords).update();

            // Mettre à jour les inputs cachés
            const inputLat = document.getElementById('latitude');
            const inputLng = document.getElementById('longitude');
            if(inputLat) inputLat.value = coords.lat;
            if(inputLng) inputLng.value = coords.lng;

            // Mettre à jour l'affichage des coordonnées
            const latDisplay = document.getElementById('lieu_latitude');
            const lngDisplay = document.getElementById('lieu_longitude');
            const rueDisplay = document.getElementById('lieu_rue');
            if(latDisplay) latDisplay.value = coords.lat.toFixed(6);
            if(lngDisplay) lngDisplay.value = coords.lng.toFixed(6);
            if(rueDisplay) rueDisplay.value = rue;

            console.log('Coordonnées choisies :', coords);
        });
    }
}
