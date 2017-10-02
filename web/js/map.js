
L.Map.include({
    'clearLayers': function () {
        this.eachLayer(function (layer) {
            this.removeLayer(layer);
        }, this);
    },
    'clearLayer': function (clerarLayer) {
        this.eachLayer(function (layer) {
            if(layer == clerarLayer){
                this.removeLayer(layer);
            }
        }, this);
    }
});


var Map = (function (window, document, undefined,$) {

    return function () {
        var __markers = null;
        var __marker_user = null;
        var __marker_place = null;

        var that = {
            map_block:null,
            showId:null,
            initEvents:function () {
              $(document).ready(function () {
                  $(document).off('click','.action-map,.preload-map')
                      .on('click','.action-map,.preload-map',function (e) {
                          e.stopPropagation();
                          var $block_map = $('#map_block');
                          if($block_map.hasClass('preload-map')){
                              $('.action-map').attr('title','Закрыть карту');
                              $block_map.removeClass('preload-map');
                              $('#map').css('display','block');
                              that.init({lat:53.905219, lon:27.564271},11);
                              that.showPlaceOnMap();
                          }else {
                              $('.action-map').attr('title','Открыть карту');
                              $block_map.addClass('preload-map');
                              $('#map').css('display','none');
                          }

                      });
                  $(document).off('click','.zoom-plus')
                      .on('click','.zoom-plus',function () {
                          that.map_block.setZoom(that.map_block.getZoom() + 1)
                      });
                  $(document).off('click','.zoom-minus')
                      .on('click','.zoom-minus',function () {
                          that.map_block.setZoom(that.map_block.getZoom() - 1)
                      });
                  $(document).off('click','.find-me')
                      .on('click','.find-me',function () {
                          var geolocation = main.userGeolocation.getGeolocation('geolocation');
                          if(geolocation==null){
                              main.userGeolocation.requestGeolocation(function () {
                                  var coords = JSON.parse(main.userGeolocation.getGeolocation('geolocation'));
                                  that.showMeOnMap(coords);
                              })
                          }else {
                              var coords = JSON.parse(geolocation);
                              that.showMeOnMap(coords);
                          }

                      });

                  $(document).off('click', '.place-icon')
                      .on('click', '.place-icon', function () {
                        var id = $(this).data('id');
                        var transform = $(this).parents('.marker-place').css('transform');
                        var transformX = parseInt(transform.split(',')[4]);
                        var transformY = parseInt(transform.split(',')[5]);
                        transformX=transformX - 156;
                        transformY=transformY - 175;
                        transform = 'matrix(1, 0, 0, 1, '+transformX+', '+transformY+')';
                          $.ajax({
                              url: '/post/get-popup-place-for-map',
                              type: "GET",
                              data:{id:id},
                              async:true,
                              success: function (response) {
                                var $popUp = $(response).css('transform',transform);
                                that.renderPopUpPlace($popUp);
                              }
                          });
                      });

                  $(document).on('click',function (e) {
                      if ($(e.target).closest(".container-place-popup,.place-icon").length) return;
                      $('.leaflet-popup-pane').html('')
                  });

              });
            },
            init: function (coords,zoom) {
                $(document).ready(function () {

                    var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                    if(that.map_block!=null){
                        if (!$('#map').hasClass('leaflet-container')) {
                            that.map_block = L.map('map', {scrollWheelZoom: false,zoomControl: false,})
                                .setView([coords.lat, coords.lon], zoom || 8);
                            osm.addTo(that.map_block);
                            that.showId = null;

                        }

                    }else {
                        that.map_block =  L.map('map',{scrollWheelZoom:false,zoomControl: false,})
                            .setView([coords.lat, coords.lon], zoom||8);
                        osm.addTo(that.map_block);

                        that.showId = null;
                    }

                })
            },
            moveToMap:function(coords,zoom){
                if(that.map_block!=null){
					that.map_block.setView([coords.lat, coords.lon], zoom || 12);
                }
            },
            initEventClickMap: function (FcallBack) {

                function initMarkerStart() {
					if(__marker_place!= null){
						that.map_block.clearLayer(__marker_place);
					}

					__marker_place = that.marker.createMarkerPlaceStart(that.map_block.getCenter());
					__marker_place.addTo(that.map_block);
					__marker_place.on('click',function (e) {
						that.setMarkerOnMap(e.target.getLatLng(), FcallBack);
					})
				}

                setTimeout(function () {
					if(that.map_block != null){

                        initMarkerStart();

                        that.map_block.on('move',function () {
							initMarkerStart();
						});
					}else {
					    that.initEventClickMap(FcallBack);
                    }
				},10)

			},
            setIdPlacesOnMap:function (id) {
                $('#map_block').attr('uniqueQuery',id);
                that.showPlaceOnMap();
            },
            getIdPlacesOnMap:function () {
                return $('#map_block').attr('uniqueQuery');
            },
            requestPlace:function () {
                that.showId = that.getIdPlacesOnMap();
                $.ajax({
                    url: '/post/get-place-for-map',
                    type: "GET",
                    data:{id:that.showId},
                    dataType: "json",
                    async:true,
                    success: function (response) {
                        if(response.status=='OK'){
                            that.renderPlaceOnMap(response.places)
                        }
                    }
                });
            },
            showPlaceOnMap:function () {

                if(!$('#map_block').hasClass('preload-map') && that.showId!=that.getIdPlacesOnMap()){

                    that.requestPlace()
                }
            },
            renderPlaceOnMap:function (places) {
                if(__markers!= null){
                    __markers.clearLayers();
                }
                 __markers = L.markerClusterGroup({chunkedLoading: true});
                var markerList = [];
                var countPlaces = places.length;
                for(var i=0;i<countPlaces;i++){
                    markerList.push(that.marker.createMarker(places[i].lat,places[i].lon,places[i].id));
                }
                __markers.addLayers(markerList);
                that.map_block.fitBounds(__markers.getBounds());
                that.map_block.addLayer(__markers);

            },
            showMeOnMap:function (coords) {
                if(__marker_user!=null){
                    that.map_block.clearLayer(__marker_user);
                }
                __marker_user =  that.marker.createMarkerUser(coords.lat,coords.lon);
                __marker_user.addTo(that.map_block);
                that.map_block.setView([coords.lat,coords.lon],14);
            },
            closeMeOnMap:function () {
                if(__marker_user!=null){
                    that.map_block.clearLayer(__marker_user);
                }
                if(__markers!= null){
                    that.map_block.fitBounds(__markers.getBounds());
                }
            },
            renderPopUpPlace:function ($html) {
                $('.leaflet-popup-pane').html($html)
            },

            setMarkerOnMap:function (latlng,fCallBack) {
				console.log('21212');
				if(that.map_block != null){
					if(__marker_place!= null){
						that.map_block.clearLayer(__marker_place);
					}

					__marker_place = that.marker.createMarkerPlace(latlng.lat,latlng.lng);
					__marker_place.addTo(that.map_block);

					that.map_block.off('move');
					fCallBack(latlng);
					__marker_place.on('drag',function (e) {
						fCallBack(e.latlng);
					})
                }
				else {
				    setTimeout(function () {
						that.setMarkerOnMap(latlng,fCallBack);
						},20);
                }


			},

            marker:{
                createMarker:function (lat,lon,id) {
                    var Icon = L.divIcon({
                        className: 'marker-place',
                        html: '<img class="place-icon" src="/img/marker_place.png" data-id="' + id + '"/>',
                        iconAnchor: [0, 41],
                        iconSize: [24, 41]
                    });

                    return L.marker([lat, lon],{
                        icon:Icon,
                        iconSize: [24, 41]
                    })
                },
				createMarkerPlace:function (lat,lon) {
					var Icon = L.divIcon({
						className: 'marker-place-edit',
						html: '<img class="place-icon-dit" src="/img/marker_place.png"/>',
						iconAnchor: [0, 41],
						iconSize: [24, 41]
					});

					return L.marker([lat, lon],{
						icon:Icon,
						iconSize: [24, 41],
						draggable: true
					})
				},
				createMarkerPlaceStart:function (latlng) {
					var Icon = L.divIcon({
						className: 'marker-place-edit',
						html: '<img class="place-icon-dit-start" src="/img/marker_place.png"/>',
						iconAnchor: [0, 41],
						iconSize: [24, 41]
					});

					return L.marker(latlng,{
						icon:Icon,
						iconSize: [24, 41],
						draggable: true
					})
				},
                createMarkerUser:function (lat,lon) {
                    var  Icon = L.divIcon({
                        className: 'marker-user',
                        html: '<img title="Вы здесь" class="mi-on-map-icon" src="/img/user_marker.png"/>',
                        iconAnchor: [14.5, 14.5],
                        iconSize: [29, 29]
                    });
                    return L.marker([lat, lon],{
                        icon:Icon,
                        iconSize: [29, 29],
                    })
                }
            }

        };

        return that;
    }

}(window,document,undefined,jQuery));

var map = Map();
map.initEvents();


