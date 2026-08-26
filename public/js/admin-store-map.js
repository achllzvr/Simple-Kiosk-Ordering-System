/* Admin store map: click to set lat/lng */
(function () {
  function $(id) { return document.getElementById(id); }

  window.initAdminStoreMap = function () {
    if (typeof L === 'undefined' || !$('admin-store-map')) return;

    var latInput = $('lat');
    var lngInput = $('lng');
    var startLat = parseFloat(latInput && latInput.value) || 14.5995;
    var startLng = parseFloat(lngInput && lngInput.value) || 120.9842;
    var hasPin = latInput && latInput.value && lngInput && lngInput.value;

    var map = L.map('admin-store-map').setView([startLat, startLng], hasPin ? 15 : 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var marker = null;
    function setPin(lat, lng) {
      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function (e) {
          var p = e.target.getLatLng();
          setPin(p.lat, p.lng);
        });
      }
      if (latInput) latInput.value = lat.toFixed(7);
      if (lngInput) lngInput.value = lng.toFixed(7);
    }

    if (hasPin) setPin(startLat, startLng);

    map.on('click', function (e) {
      setPin(e.latlng.lat, e.latlng.lng);
    });
  };

  if (typeof L !== 'undefined') {
    window.initAdminStoreMap();
  }
})();
