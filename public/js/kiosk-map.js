/* Guest delivery map: pin customer + select admin-configured store */
(function () {
  var map, userMarker, storeMarkers = [];
  var selectedStoreId = null;
  var customerLat = null;
  var customerLng = null;

  function $(id) {
    return document.getElementById(id);
  }

  function setStatus(msg) {
    var el = $('location-status');
    if (el) el.textContent = msg;
  }

  function updateHiddenFields() {
    if ($('customer_lat')) $('customer_lat').value = customerLat != null ? customerLat : '';
    if ($('customer_lng')) $('customer_lng').value = customerLng != null ? customerLng : '';
    if ($('restaurant_id')) $('restaurant_id').value = selectedStoreId || '';
    var btn = $('continue-btn');
    if (btn) btn.disabled = !(customerLat != null && customerLng != null && selectedStoreId);
  }

  function placeUserPin(lat, lng, fly) {
    customerLat = lat;
    customerLng = lng;
    if (userMarker) {
      userMarker.setLatLng([lat, lng]);
    } else {
      userMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
      userMarker.bindPopup('You are here').openPopup();
      userMarker.on('dragend', function (e) {
        var p = e.target.getLatLng();
        placeUserPin(p.lat, p.lng, false);
        loadNearby(p.lat, p.lng);
      });
    }
    if (fly) map.flyTo([lat, lng], 14);
    updateHiddenFields();
    setStatus('Location set. Pick a store below.');
  }

  function renderStores(stores) {
    var list = $('store-list');
    if (!list) return;

    storeMarkers.forEach(function (m) { map.removeLayer(m); });
    storeMarkers = [];

    if (!stores.length) {
      list.innerHTML = '<div class="list-group-item text-muted">No active stores found.</div>';
      selectedStoreId = null;
      updateHiddenFields();
      return;
    }

    list.innerHTML = '';
    stores.forEach(function (store) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'list-group-item list-group-item-action' + (String(store.id) === String(selectedStoreId) ? ' active' : '');
      var dist = store.distance_km != null ? ' · ' + store.distance_km + ' km' : '';
      btn.innerHTML = '<strong>' + store.name + '</strong><br><small>' + (store.address || '') + dist + '</small>';
      btn.addEventListener('click', function () {
        selectedStoreId = store.id;
        updateHiddenFields();
        renderStores(stores);
        map.panTo([store.lat, store.lng]);
      });
      list.appendChild(btn);

      var marker = L.marker([store.lat, store.lng]).addTo(map);
      marker.bindPopup(store.name);
      marker.on('click', function () {
        selectedStoreId = store.id;
        updateHiddenFields();
        renderStores(stores);
      });
      storeMarkers.push(marker);
    });
  }

  function loadNearby(lat, lng) {
    var cfg = window.KIOSK_MAP || {};
    if (!cfg.storesUrl) {
      renderStores(cfg.initialStores || []);
      return;
    }
    fetch(cfg.storesUrl + '?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng), {
      headers: { Accept: 'application/json' }
    })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        renderStores((json && json.data) || []);
      })
      .catch(function () {
        renderStores(cfg.initialStores || []);
      });
  }

  window.initKioskMap = function () {
    if (typeof L === 'undefined' || !$('kiosk-map')) return;

    map = L.map('kiosk-map').setView([14.5995, 120.9842], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    map.on('click', function (e) {
      placeUserPin(e.latlng.lat, e.latlng.lng, false);
      loadNearby(e.latlng.lat, e.latlng.lng);
    });

    var useBtn = $('use-my-location-btn');
    if (useBtn) {
      useBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
          setStatus('Geolocation is not supported in this browser.');
          return;
        }
        setStatus('Getting your location…');
        navigator.geolocation.getCurrentPosition(
          function (pos) {
            placeUserPin(pos.coords.latitude, pos.coords.longitude, true);
            loadNearby(pos.coords.latitude, pos.coords.longitude);
          },
          function () {
            setStatus('Could not get GPS. Click the map to pin manually.');
          },
          { enableHighAccuracy: true, timeout: 15000 }
        );
      });
    }

    var initial = (window.KIOSK_MAP && window.KIOSK_MAP.initialStores) || [];
    if (initial.length) {
      renderStores(initial);
      var group = L.featureGroup(storeMarkers);
      if (storeMarkers.length) map.fitBounds(group.getBounds().pad(0.2));
    }
  };

  if (typeof L !== 'undefined') {
    window.initKioskMap();
  }
})();
