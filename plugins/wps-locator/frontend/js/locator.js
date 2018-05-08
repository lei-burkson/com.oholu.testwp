var wps = function() {
	var $ = jQuery;
    var map, geocoder;
    var markerIcon, markerIconActive;

    $wpsLocatorMapWrap = $('.wps-locator-map-wrap');

    var mapLinkBase = 'https://maps.google.com';

    var markersArray = [], storeIDArray = [];

    var need_rebound = true, fromFit = false;

    var clearMarkers = function () {
        for (var i = 0; i < markersArray.length; i++) {
            var thisMarker = markersArray[i];
            thisMarker.setMap(null);
        }
        markersArray = [];
    },
    addLoader = function () {
    	var imgUrl = $wpsLocatorMapWrap.data('loader');
    	if (typeof imgUrl !== 'undefined')
    		$('<img/>', {'class': 'wps-map-loading', 'src': imgUrl}).appendTo($wpsLocatorMapWrap);
    },
    removeLoader = function () {
    	$wpsLocatorMapWrap.find('.wps-map-loading').remove();
    },
    createListLocation = function (item) {
        var $itemWrap = $('<a/>', {'class': 'locator-result-item', 'href': 'javascript:void(0);'}),
        $itemTitle = $('<p/>', {'class': 'result-item-header text-uppercase', 'text': item.LocationName}),
        $addressVal = item.LocationAddress+'<br/>' + ($.trim(item.LocationAddress2) != '' ? item.LocationAddress2 + '<br>' : '') + item.LocationCity + ', ' + item.LocationState + ' ' + item.LocationZip,
        addressString = item.LocationAddress+', ' + ($.trim(item.LocationAddress2) != '' ? item.LocationAddress2 + ', ' : ' ') + item.LocationCity + ', ' + item.LocationState + ' ' + item.LocationZip;

        $itemAddress = $('<p/>', {'class': 'result-item-address k-gray'}).html($addressVal);
        $itemDirection = $('<a/>', {'class': 'result-item-directions text-uppercase', 'href': mapLinkBase + "/maps?saddr=&daddr="+addressString, 'text': 'Direction ', 'target': '_blank'})
                            .append($('<i/>', {'class': 'glyphicon glyphicon-menu-right k-text-red'})),
        $itemPhone = $('<span/>', {'class': 'result-item-phone', 'text': item.dealer_phone});

        return $itemWrap.append($itemTitle).append($itemAddress).append($itemDirection).append($itemPhone);
    },
    sortLocations = function (items) {
        var r = [], r1 = [], r2 = [];
        var centerLatLng = map.getCenter();

    	for (var i=0; i < items.length; i++) {
    		var realDistance = google.maps.geometry.spherical.computeDistanceBetween(centerLatLng, new google.maps.LatLng(parseFloat(items[i]['LocationLatitude']), parseFloat(items[i]['LocationLongitude'])));
    		r.push({
    			index: i,
    			distance: realDistance
    		});
    	}

    	r.sort(function (a,b) {return a.distance - b.distance;});
    	for (var i = r.length - 1; i >= 0; i--) r[i] = items[r[i].index];

    	return r;
    },
    resetMarkerStyle = function () {
        for (var i = 0; i < markersArray.length; i++) {
            markersArray[i].setIcon(markerIcon);
        }
    },
    getListItemVisibility = function(index) {
    	var $theResultList = $('.wps-locator-scroll-wrap'),
    	$theFirstListItem = $theResultList.find('.locator-result-item').eq(0);
    	var $thisListItem = $theResultList.find('.locator-result-item').filter(function(){
    		return $(this).data('marker-index') == index;
    	});
    	if ($thisListItem.length == 1) {
    		var result = [];
    		var listTop = $theResultList.offset().top,
    		firstItemTop = $theFirstListItem.offset().top,
    		itemTop = $thisListItem.offset().top,
    		itemHeight = $thisListItem.outerHeight();
    		if (itemTop < listTop) {
    			result['visibility'] = false;
    			result['position'] = 'top';
    		}
    		else if (itemTop > kinesis.utils.getViewport().height - itemHeight) {
    			result['visibility'] = false;
    			result['position'] = 'bottom';
    		}
    		else {
    			result['visibility'] = true;
    			result['position'] = 'middle';
    		}
    		return result;
    	}
    },
    getTopTen = function(latlng, radius, isRandom, options) {
        var postData = {
    		action: 'wps_get_locations',
    		security : wpsLocator.security,
    		lat: latlng.lat(),
    		lng: latlng.lng(),
            radius: radius,
            cnt: 10
    	};
		$.ajax({
			url: wpsLocator.ajaxurl,
    		data: postData,
    		dataType: 'json'
		}).done(function(jsonData) {
			var bounds = new google.maps.LatLngBounds();
			if(jsonData.error){
				bounds.extend(latlng);
				map.panToBounds(bounds);
				fromFit = true;
				map.fitBounds(bounds);
				map.setZoom(9);
				var listener = google.maps.event.addListener(map, "idle", function() {
					bounds = map.getBounds();
					var swPoint = bounds.getSouthWest(),
					nePoint = bounds.getNorthEast();

					fromFit = false;

					getLocationsFromBounds(swPoint.lat(), nePoint.lat(), swPoint.lng(), nePoint.lng(), false, function() {
						removeLoader();
						need_rebound = true;
						$('.wps-locator-result-list').scrollTop(0);
					});
					google.maps.event.removeListener(listener);
				});
			}
			else {
				for(var i = 0; i<jsonData.length; i++ )	{
					var location_latitude = jsonData[i]["LocationLatitude"];
					var location_longitude = jsonData[i]["LocationLongitude"];
					var point = new google.maps.LatLng(location_latitude, location_longitude);
					bounds.extend(point);
				}
				bounds.extend(latlng);
				map.panToBounds(bounds);
				fromFit = true;
				map.fitBounds(bounds);
				var listener = google.maps.event.addListener(map, "idle", function() {
					bounds = map.getBounds();
					var swPoint = bounds.getSouthWest(),
					nePoint = bounds.getNorthEast();
					fromFit = false;

					getLocationsFromBounds(swPoint.lat(), nePoint.lat(), swPoint.lng(), nePoint.lng(), false, function() {
						removeLoader();
						need_rebound = true;
						$('.wps-locator-result-list').scrollTop(0);
					});
					google.maps.event.removeListener(listener);
				});
			}
		})
	},
	getLocationsFromBounds = function(min_lat, max_lat, min_lng, max_lng, hasReset, callback) {
		//if (!isRandom) isRandom = false;
		$storeIDArray = [];
		var postData = {
    		action: 'wps_locations_in_bound',
    		security : wpsLocator.security,
    		min_lat: min_lat, 
    		max_lat: max_lat, 
    		min_lng: min_lng, 
    		max_lng: max_lng
    	};
		$.ajax({
			url: wpsLocator.ajaxurl,
    		data: postData,
    		dataType: 'json'
		}).done(function( jsonData ) {
			clearMarkers();

			/*if no store matches, jsonData is null*/
			if (typeof jsonData.error !== 'undefined' || jsonData.length == 0) {
				var eMessage = '';
				eMessage = 'No locations found in this area.';
				/*if(hasReset) {
					$('.search-warning-content').html(eMessage+'.<a href="#" id="btn_reset" class="btn btn-expand">Reset</a>');
				}
				else {
					$('.search-warning-content').html(eMessage+'. Please try a new search.');
				}*/
				$('<div/>', {'class': 'wps-locator-warning', 'text': eMessage}).appendTo($wpsLocatorMapWrap);
				/*$eles.info_modal.addClass('hide');
				$eles.warning_strip.show();
				$eles.ajax_loader.hide();
				need_rebound = true;*/
				need_rebound = true;
				removeLoader();

				return false;
			}

			jsonData = sortLocations(jsonData);
			$('.wps-locator-result-list').empty();
			var markerIndex = 0;

			for (var i = 0; i < jsonData.length; i++) {
	            var thisItem = jsonData[i];

                var thisMarker = new google.maps.Marker({
                    position: {lat: parseFloat(thisItem['LocationLatitude']), lng: parseFloat(thisItem['LocationLongitude'])},
                    icon: markerIcon,
                    label: {text: (markerIndex+1).toString(), color: '#fff', fontSize: '13', fontFamily: '"Oswald", sans-serif', fontWeight: '300'},
                    markerIndex: markerIndex
                });
                $('.wps-locator-result-list').append(createListLocation(thisItem).attr('data-marker-index', markerIndex));
                google.maps.event.addListener(thisMarker, 'click', function() {
                    resetMarkerStyle();
                    this.setIcon(markerIconActive);
                    var $theResultList = $('.wps-locator-scroll-wrap'),
                    itemList = $theResultList.find('.locator-result-item');
                    var $thisListItem = itemList.removeClass('active').eq(this.markerIndex).addClass('active');
                });
                markerIndex++;
                thisMarker.setMap(map);
                markersArray.push(thisMarker);
	        }
			callback();
		});
	};

    return {
    	initMap: function () {
	        // Using Silver theme from withgoogle.com
	        var wpsStyle = new google.maps.StyledMapType(
	            [
	                {
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#f5f5f5"
	                        }
	                    ]
	                },
	                {
	                    "elementType": "labels.icon",
	                    "stylers": [
	                        {
	                            "visibility": "off"
	                        }
	                    ]
	                },
	                {
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#616161"
	                        }
	                    ]
	                },
	                {
	                    "elementType": "labels.text.stroke",
	                    "stylers": [
	                        {
	                            "color": "#f5f5f5"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "administrative.land_parcel",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#bdbdbd"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "poi",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#eeeeee"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "poi",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#757575"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "poi.park",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#e5e5e5"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "poi.park",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#9e9e9e"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "road",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#ffffff"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "road.arterial",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#757575"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "road.highway",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#dadada"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "road.highway",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#616161"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "road.local",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#9e9e9e"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "transit.line",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#e5e5e5"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "transit.station",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#eeeeee"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "water",
	                    "elementType": "geometry",
	                    "stylers": [
	                        {
	                            "color": "#c9c9c9"
	                        }
	                    ]
	                },
	                {
	                    "featureType": "water",
	                    "elementType": "labels.text.fill",
	                    "stylers": [
	                        {
	                            "color": "#9e9e9e"
	                        }
	                    ]
	                }
	            ]
	        );

	        var geocodeOptions = {
	            componentRestrictions: {
	    			'country': 'US'
	    		}
	        }

	        map = new google.maps.Map(document.getElementById('wps-locator-map-wrap'), {
	            center: {lat: 39.618272, lng: -104.892871},
	            zoom: 12,
	            /*gestureHandling: 'cooperative',*/
	            scrollwheel: false,
	            minZoom: 8, maxZoom: 16
	        });

	        geocoder = new google.maps.Geocoder();

	        // Set Map Style
	        map.mapTypes.set('wps_map', wpsStyle);
	        map.setMapTypeId('wps_map');

	        // Set icons
	        markerIcon = {
	            path: 'M 0 0 L 0 30 L 9 30 L 15 36 L 21 30 L 30 30 L 30 0 Z',
	            fillColor: '#0082c9',
	            fillOpacity: 0.75,
	            scale: 1,
	            strokeWeight: 0,
	            size: new google.maps.Size(15, 18),
	            origin: new google.maps.Point(15, 18),
	            anchor: new google.maps.Point(15, 36),
	            scaledSize: new google.maps.Size(15, 18),
	            labelOrigin: new google.maps.Point(15, 15),
	            zIndex: 1
	        };
	        markerIconActive = {
	            path: 'M 0 0 L 0 30 L 9 30 L 15 36 L 21 30 L 30 30 L 30 0 Z',
	            fillColor: '#0082c9',
	            fillOpacity: 1,
	            scale: 1,
	            strokeWeight: 0,
	            size: new google.maps.Size(15, 18),
	            origin: new google.maps.Point(15, 18),
	            anchor: new google.maps.Point(15, 36),
	            scaledSize: new google.maps.Size(15, 18),
	            labelOrigin: new google.maps.Point(15, 15),
	            zIndex: 2
	        };

	        google.maps.event.addDomListener(window, 'load', function() {
	            getTopTen(new google.maps.LatLng(39.653454, -104.979727));
	        });
	        google.maps.event.addListener(map, 'zoom_changed', function() {
	        	if (fromFit) return false;
	            $('.wps-use-location-btn').removeClass('active');
	            $wpsLocatorMapWrap.find('.wps-locator-warning').remove();
	            var bounds = map.getBounds();
	            var swPoint = bounds.getSouthWest(),
				nePoint = bounds.getNorthEast();

				getLocationsFromBounds(swPoint.lat(), nePoint.lat(), swPoint.lng(), nePoint.lng(), false, function() {
					removeLoader();
					need_rebound = true;
					$('.wps-locator-result-list').scrollTop(0);
				});
	        });
	        google.maps.event.addListener(map, 'dragend', function() {
	            $('.wps-use-location-btn').removeClass('active');
	            $wpsLocatorMapWrap.find('.wps-locator-warning').remove();
	            var bounds = map.getBounds();
	            var swPoint = bounds.getSouthWest(),
				nePoint = bounds.getNorthEast();

				getLocationsFromBounds(swPoint.lat(), nePoint.lat(), swPoint.lng(), nePoint.lng(), false, function() {
					removeLoader();
					need_rebound = true;
					$('.wps-locator-result-list').scrollTop(0);
				});
	        });
	        $(window).on('resizeend', function () {
	            // Trigger google maps' resize event to freshen up the map,
	            // Do not updateMarkers for now.
	            // fullpage js seems to have negative effect on both resizeend and google maps
	            $('.wps-use-location-btn').removeClass('active');
	            $wpsLocatorMapWrap.find('.wps-locator-warning').remove();
	            google.maps.event.trigger(map, 'resize');
	        })

	        $('.wps-locator-result-list').on('click', '.locator-result-item', function () {
	            var $that = $(this),
	            markerIndex = $that.data('marker-index');

	            if (!$that.hasClass('active')) {
	                $that.siblings().removeClass('active');
	                $that.addClass('active');
	                for (var i = 0; i < markersArray.length; i++) {
	                    var thisMarker = markersArray[i];
	                    if (thisMarker.markerIndex == markerIndex) {
	                        thisMarker.setIcon(markerIconActive);
	                    }
	                    else {
	                        thisMarker.setIcon(markerIcon);
	                    }
	                }
	            }
	        });

	        //Form Submission
	        $('.wps-locator-search-wrap').submit(function (e) {
	            e.preventDefault();
	            var $that = $(this);
	            var $input = $that.find('input[name="location"]'),
	            inputVal = $.trim($input.val());
	            if (inputVal == '') {
	                $input.val('').focus();
	                return;
	            }
	        	addLoader();
	            $('.wps-use-location-btn').removeClass('active');
	            $wpsLocatorMapWrap.find('.wps-locator-warning').remove();
	            geocoder.geocode($.extend({ 'address' : inputVal }, geocodeOptions), function(results, status) {
	    			if (status == google.maps.GeocoderStatus.OK) {
	    				var topResult = results[0],
	                    searchedLatLng = topResult.geometry.location;

	                    map.setCenter(searchedLatLng);
	                    need_rebound = false;
	                    getTopTen(searchedLatLng);
	                }
	            })
	        });

	        $('.wps-use-location-btn').on('click', function () {
	            var $that = $(this);
	            addLoader();
	            if ($that.hasClass('active')) {
	                return;
	            }
	            $wpsLocatorMapWrap.find('.wps-locator-warning').remove();
	            if (navigator.geolocation)
	    		{
	    			navigator.geolocation.getCurrentPosition(function(position){
	    				$that.addClass('active');
	    				var currentLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
	                    map.setCenter(currentLatLng);
	                    need_rebound = false;
	                    getTopTen(currentLatLng);
	    			},
	    			function(error){
	    				switch(error.code) {
	    					case error.PERMISSION_DENIED:
	                            // Use IP or other to get location
	    						break;
	    					case error.POSITION_UNAVAILABLE:
	                            // Use IP or other to get location
	    						break;
	    					case error.TIMEOUT:
	                            // Use IP or other to get location
	    						break;
	    					case error.UNKNOWN_ERROR:
	                            // Use IP or other to get location
	    						break;
	    					default:
	                            // Use IP or other to get location
	    						break;
	    				}
	    				removeLoader();
	    			});
	    		}
	    		else {
	    			removeLoader();
	    		}
	        });
	    }
    }
}();

var initWPSMap = wps.initMap;