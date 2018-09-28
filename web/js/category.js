/**
 * Created by jrborisov on 8.7.17.
 */
var Category = (function (window, document, undefined, $) {

	return function () {

		var that = {

			init: function () {

				$(document).ready(function () {
					$(document).off('click', '.btn-sort.no-geolocation')
							.on('click', '.btn-sort.no-geolocation', function () {
								main.userGeolocation.requestGeolocation(function () {
									$('.btn-nigh.btn-sort').trigger("click");
								})

							});
				})

			},
			refreshTotalCount: function (totalCount) {
				$('.total-count', '.menu-info-cards').html('<span class="under-line">Места ' + totalCount + '</span>');
			},

			refreshDiscountTotalCount: function (totalCount) {
				$('#discount-total-count', '.menu-info-cards').html(
						'<span class="under-line">Скидки ' + totalCount + '</span>');
			},

			Filters: function () {

				var __leftMenuFilter = {isOpen: false, isUnderOpen: false};
				var __$leftMenuFilterContainer = null;
				var __$leftMenuUnderFilterContainer = null;

				var __current_url = null;
				var __current_url_with_filters = null;
				var __params_filters = {};
				var __params_other = {};
				var __loadFilters = {};


				var __storageUnderFilters = {
					currentOpen: {id: '', name: ''},
					container: {},
					removeContainer: {}
				};


				var filters = {

					init: function () {
						$(document).ready(function () {
							$(document).off('click', '.icon-filter,.close-menu')
									.on('click', '.icon-filter,.close-menu', function () {
										if (!__leftMenuFilter.isOpen) {
											filters.loadFilters(__current_url);
											filters.openMenu();
										} else {
											if (__leftMenuFilter.isUnderOpen) {
												filters.closeMenuUnderFilter();
											} else {
												filters.closeMenu();
											}
										}
									});

							$(document).off('click', '.btn-container-filters')
									.on('click', '.btn-container-filters', function () {
										var params = $(this).data('under_features');
										filters.openMenuUnderFilter.call(this, params);
									});


							$(document).off('click', '.menu-info-cards .btn-filter,.left-menu.filter .btn-filter')
									.on('click', '.menu-info-cards .btn-filter,.left-menu.filter .btn-filter', function () {
										var name_filter = $(this).data('name_filter');
										var value_filter = $(this).data('value');
										if (name_filter && value_filter) {
											if ($(this).hasClass('active')) {
												filters.removeFilter(name_filter);
												$(this).removeClass('active');
												filters.filtrate()
											} else {
												filters.addFilter(name_filter, value_filter);
												$(this).addClass('active');
												filters.filtrate()
											}
										}
									});

							$(document).off('click', '.under-filter .btn-filters')
									.on('click', '.under-filter .btn-filters', function () {
										if ($(this).hasClass('active')) {
											filters.removeUnderFilter.apply(this);
											$(this).removeClass('active')
										} else {
											filters.addUnderFilter.apply(this);
											$(this).addClass('active')
										}
									});

							$(document).off('click', '.btn-container-filters .icon-remove-filters')
									.on('click', '.btn-container-filters .icon-remove-filters', function (e) {
										var nameFilter = $(this).parents('.btn-container-filters').data('name_filter');

										filters.removeUnderFiltersByName(nameFilter);
										filters.refreshMainFilters();
										e.stopPropagation();

									});
							$(document).off('click', '.filter-complete')
									.on('click', '.filter-complete', function () {
										filters.closeMenuUnderFilter();
										filters.closeMenu();
									});
							filters.resetData();
						})

					},
					setDefaultUrl: function (p) {
						__current_url = p.url;
					},
					resetData: function () {
						__$leftMenuFilterContainer = $('.left-menu.filter');
						__$leftMenuUnderFilterContainer = $('.left-menu.under-filter');


						__current_url_with_filters = null;
						__params_filters = {};
						__params_other = {};
						__loadFilters = {};

						__storageUnderFilters = {
							currentOpen: {id: '', name: ''},
							container: {},
							removeContainer: {}
						};


					},
					loadFilters: function (url) {

						if (!__loadFilters[url]) {
							$.ajax({
								url: '/category/get-filters',
								type: 'POST',
								data: {url: url},
								beforeSend: function () {
									__$leftMenuFilterContainer.find('.menu-content').html(main.getLoadBlock());
								},
								async: true,
								success: function (response) {
									__loadFilters[url] = response;
									__$leftMenuFilterContainer.find('.menu-content').replaceWith(response);
									filters.initAverage();
									filters.initCustomScrollBar('.left-menu.filter .menu-content');
								}
							});
						}

					},
					initAverage: function () {

						$('.average-item-range').each(function () {
							var $averageContainer = $(this).parents('.average-item');

							$(this).slider({
								values: [$averageContainer.data('min'), $averageContainer.data('max')],
								max: $averageContainer.data('max'),
								min: $averageContainer.data('min'),
								step: 1,
								range: true,
								create: displaySliderValues,
								slide: displaySliderValues,
								change: change
							});
						});


						function displaySliderValues(e) {
							var id_filter = $(e.target).data('name_filter');
							$('#' + id_filter + ' .min-border').text($('#' + id_filter + ' .average-item-range').slider("values", 0));
							$('#' + id_filter + ' .max-border').text($('#' + id_filter + ' .average-item-range').slider("values", 1));

						}

						function change(e) {
							var id_filter = $(e.target).data('name_filter');
							var min = $('#' + id_filter + ' .average-item-range').slider("values", 0);
							var max = $('#' + id_filter + ' .average-item-range').slider("values", 1);
							$('#' + id_filter + ' .min-border').text(min);
							$('#' + id_filter + ' .max-border').text(max);


							if (min == $('#' + id_filter).data('min') && max == $('#' + id_filter).data('max')) {
								filters.removeFilter(id_filter);
							} else {
								filters.addFilter(id_filter, min + ',' + max);
							}

						}

					},
					initCustomScrollBar: function (selector) {
						main.initCustomScrollBar($(selector), {scrollInertia: 50});
					},
					openMenu: function () {
						filters.refreshMainFilters();
						__leftMenuFilter.isOpen = true;
						__$leftMenuFilterContainer.animate({left: '0px', top: '0px'}, 200);
					},
					closeMenu: function () {
						if (__$leftMenuFilterContainer && __leftMenuFilter.isOpen) {
							filters.competeFilter();
							__leftMenuFilter.isOpen = false;
							__$leftMenuFilterContainer.animate({left: '-300px', top: '0px'}, 200);
						}

					},
					refreshMainFilters: function () {
						$('.btn-container-filters', '.left-menu.filter .menu-content').each(function () {
							var name_filters = $(this).data('name_filter');
							var $container = $('.btn-container-filters[data-name_filter="' + name_filters + '"]', '.left-menu.filter .menu-content');
							if (__storageUnderFilters.container[name_filters] != undefined) {
								var countSelected = Object.keys(__storageUnderFilters.container[name_filters]).length;
								$container.find('.count').text(countSelected != 0 ? countSelected : '');
								$container.addClass('active');
								$container.find('.icon-add-filters').removeClass('icon-add-filters').addClass('icon-remove-filters');

							} else {
								$container.find('.count').text('');
								$container.removeClass('active');
								$container.find('.icon-remove-filters').addClass('icon-add-filters').removeClass('icon-remove-filters');
							}
						})


					},
					openMenuUnderFilter: function (filters_params) {
						__storageUnderFilters.currentOpen = $(this).data('name_filter');
						__leftMenuFilter.isUnderOpen = true;

						var $btns = filters.createBlockUnderFilter(filters_params);

						__$leftMenuUnderFilterContainer.find('.menu-content').replaceWith($btns);
						filters.initCustomScrollBar('.left-menu.under-filter .menu-content');
						__$leftMenuUnderFilterContainer.find('.header-menu-title').text($(this).find('.name_filters').text());
						__$leftMenuUnderFilterContainer.animate({left: '0px', top: '0px'}, 200);
					},
					closeMenuUnderFilter: function () {
						if (__$leftMenuUnderFilterContainer && __leftMenuFilter.isUnderOpen) {
							filters.refreshMainFilters();
							__leftMenuFilter.isUnderOpen = false;
							__$leftMenuUnderFilterContainer.animate({left: '-300px', top: '0px'}, 200);
						}
					},
					createBlockUnderFilter: function (filters_params) {
						var $tmp_btn_filter = $('<div class="btn-filters" data-name_filter="" data-value=true><span class="name_filter"></span></div>');
						var $tmp_container = $('<div class="menu-content"><div class="container-filters"></div></div>');

						for (var index in filters_params) {
							var $newElem = $tmp_btn_filter.clone();
							$newElem.attr('data-name_filter', filters_params[index]['id_filter']);
							$newElem.attr('data-value', true);
							$newElem.find('.name_filter').text(filters_params[index]['name_filter']);
							//#check selected filters
							if (__storageUnderFilters.container[__storageUnderFilters.currentOpen] != undefined && __storageUnderFilters.container[__storageUnderFilters.currentOpen][filters_params[index]['id_filter']] != undefined) {
								$newElem.addClass('active');
							}
							$tmp_container.find('.container-filters').append($newElem);
						}

						return $tmp_container;
					},
					competeFilter: function () {
						for (var index in __storageUnderFilters.container) {
							for (var filter_name in __storageUnderFilters.container[index]) {
								filters.addFilter(filter_name, __storageUnderFilters.container[index][filter_name]);
							}
						}

						for (var index2 in __storageUnderFilters.removeContainer) {
							for (var filter_name2 in __storageUnderFilters.removeContainer[index2]) {
								filters.removeFilter(filter_name2);
							}
						}

						filters.filtrate()

					},
					createFilterUrl: function () {
						var url = filters.getUrl();
						var i = 0;
						var countFilters = 0;
						var getSepararot = '?';

						for (var param_name in __params_other) {
							getSepararot = i > 0 ? '&' : '?';
							url += (getSepararot + param_name + '=' + __params_other[param_name]);
							i++;
						}

						for (var param_name_filter in __params_filters) {
							getSepararot = i > 0 ? '&' : '?';
							url += (getSepararot + param_name_filter + '=' + __params_filters[param_name_filter]);
							i++;
							countFilters++;
						}
						if (countFilters > 0) {
							url += '&filters=' + countFilters;
						}
						__current_url_with_filters = url;
					},
					addFilter: function (name_filter, value) {
						var _filter_name = name_filter;
						__params_filters[_filter_name] = value;
						filters.createFilterUrl();
					},
					removeFilter: function (name_filter) {
						var _filter_name = name_filter;
						if (__params_filters[_filter_name] !== undefined) {
							delete __params_filters[_filter_name];
						}
						filters.createFilterUrl();
					},

					addParamOther: function (name_param, value) {
						__params_other[name_param] = value;
						filters.createFilterUrl();
					},
					removeParamOther: function (name_param) {
						if (__params_other[name_param] !== undefined) {
							delete __params_other[name_param];
						}
						filters.createFilterUrl();
					},

					addUnderFilter: function () {
						if (!__storageUnderFilters.container[__storageUnderFilters.currentOpen])
							__storageUnderFilters.container[__storageUnderFilters.currentOpen] = {};
						__storageUnderFilters.container[__storageUnderFilters.currentOpen][$(this).data('name_filter')] = $(this).data('value');
						if (__storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen]) {
							if (__storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen][$(this).data('name_filter')]) {
								delete __storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen][$(this).data('name_filter')];
							}
						}
					},
					removeUnderFilter: function () {
						delete __storageUnderFilters.container[__storageUnderFilters.currentOpen][$(this).data('name_filter')];
						if (Object.keys(__storageUnderFilters.container[__storageUnderFilters.currentOpen]).length == 0) {
							delete __storageUnderFilters.container[__storageUnderFilters.currentOpen]
						}

						if (!__storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen])
							__storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen] = {};
						__storageUnderFilters.removeContainer[__storageUnderFilters.currentOpen][$(this).data('name_filter')] = $(this).data('value');

					},
					removeUnderFiltersByName: function (name) {
						if (__storageUnderFilters.container[name] != undefined) {
							for (var index  in __storageUnderFilters.container[name]) {
								delete __storageUnderFilters.container[name][index];
								if (!__storageUnderFilters.removeContainer[name])
									__storageUnderFilters.removeContainer[name] = {};
								__storageUnderFilters.removeContainer[name][index] = 1;
							}
							if (Object.keys(__storageUnderFilters.container[name]).length == 0) {
								delete __storageUnderFilters.container[name]
							}

						}
					},

					getUrlWithFilters: function () {
						return __current_url_with_filters || filters.getUrl();
					},
					getUrl: function () {
						return __current_url;
					},
					filtrate: function () {
						$.pjax.reload({
							container: '#feed-category',
							url: filters.getUrlWithFilters(),
							push: true,
							replace: false
						});
						var $iconFilter = $('.icon-filter', '.menu-info-cards-contener');
						$iconFilter.removeClass('active');
						if ($('.container-filters .btn-filter').hasClass('active')) {
							$iconFilter.addClass('active');
						} else {
							for (var index in __params_filters) {
								if (index != 'open') {
									$iconFilter.addClass('active');
									break;
								}
							}
						}


					}
				};

				return filters;
			}

		};

		return that;
	}

}(window, document, undefined, jQuery));

var category = Category();
category.init();
category.filters = category.Filters();

$(document).ready(function () {
	$(document).on('click', function (e) {
		if ($(e.target).closest(".left-menu,.icon-filter").length) return;
		category.filters.closeMenu();
		category.filters.closeMenuUnderFilter();
		e.stopPropagation();
	});
})


