/**
 * Created by jrborisov on 8.7.17.
 */
var ListCity = (function (window, document, undefined,$) {

    return function () {



        var __$liCity = $('<li></li>');
        var __$selected_letter = $('<b class="selected-letter"></b>');
        var __$container_cities = $('<ul class="block-list-cities"></ul>');

        var that = {

            init:function () {
                $(document).on('click','.btn-select-city',function () {
                    $('.container-selected-city').show().css({display:'flex'});
                    $('body').css({overflowY: 'hidden'});
                })
                $(document).on('click','.close-selected-city',function () {
                    $('.container-selected-city').hide().css({display:'none'});
                    $('body').css({overflowY: 'auto'});
                })
                $(document).on('click','.block-list-cities li',function () {
                    var dataCity ={
                        name:'',
                        url_name:''
                    }
                    dataCity.name =$(this).text();
                    dataCity.url_name=$(this).attr('data-url_name');
                    window.location.href='/site/set-city?dataCity='+JSON.stringify(dataCity);
                })
            },


            Autocomplete:function (p) {

                var __selector=p.selector;
                var $container = $(__selector);
                var $__resultContainer = $container.find('.autocomplete-result-search');
                var dataCities = p.dataCities;
                var text_error = '<p style="display: flex;justify-content: center;padding-bottom: 20px;">Ничего не найдено</p>';

                var autocomplete = {

                        init:function () {
                            $(__selector+' .search-cities-i').on('keydown',autocomplete.filterCities)
                        },

                        filterCities:function (event) {
                            var _this = this;
                            setTimeout(function () {
                                var $inputSearch = $(_this);
                                var textFil=$inputSearch.val();
                                var expr = new RegExp('^'+textFil,'i');
                                var findCities = [];

                                for(var index in dataCities){

                                    if(expr.test(dataCities[index].name)){
                                       findCities.push(dataCities[index]);
                                    }
                                }
                                autocomplete.refreshCities(findCities);

                            },30)

                        },
                        refreshCities:function (data) {
                            if(data.length==0){
                                $__resultContainer.html(text_error);
                                return false;
                            }
                            var $tmp_container_cities =__$container_cities.clone();

                            for (var index in data){
                                var $tmp_liCity = __$liCity.clone();
                                var $tmp_selected_letter =__$selected_letter.clone();
                                var name = data[index]['name'].toString();
                                var url_name =data[index]['url_name'].toString();
                                if(data[index]['selected-letter']){
                                    $tmp_selected_letter.text(name.substr(0,1));
                                    $tmp_liCity.append($tmp_selected_letter);
                                    $tmp_liCity.append(name.substr(1,name.length));
                                }else {
                                    $tmp_liCity.append(name);
                                }
                                $tmp_liCity.attr('data-url_name',url_name);
                                $tmp_container_cities.append($tmp_liCity);
                            }

                            $__resultContainer.html($tmp_container_cities);
                        }

                };

                return autocomplete;
            }

        }

        return that;
    }

}(window,document,undefined,jQuery));
var listCity = ListCity();
listCity.init();
