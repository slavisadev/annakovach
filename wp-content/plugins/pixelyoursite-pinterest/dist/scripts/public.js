/* global pysOptions */

!function ($) {

    var Pinterest = function () {

        var initialized = false;

        function getUtils() {
            return window.pys.Utils;
        }

        function getOptions() {
            return window.pysOptions;
        }

        function fireEvent(name, data) {
            if(typeof window.pys_event_data_filter === "function" && window.pys_disable_event_filter(name,'pinterest')) {
                return;
            }
            var params = {};
            getUtils().copyProperties(data, params);
            getUtils().copyProperties(getUtils().getRequestParams(), params);
            if (getOptions().debug) {
                console.log('[Pinterest] ' + name, params);
            }

            pintrk('track', name, params);

        }

        /**
         * Public API
         */
        return {
            tag: function() {
                return "pinterest";
            },
            isEnabled: function () {
                return getOptions().hasOwnProperty('pinterest');
            },

            disable: function () {
                initialized = false;
            },

            /**
             * Load pixel's JS
             *
             * @link: https://developers.pinterest.com/docs/ad-tools/enhanced-match/
             */
            loadPixel: function () {

                if (initialized || !this.isEnabled() || !getUtils().consentGiven('pinterest')) {
                    return;
                }

                !function (e) {
                    if (!window.pintrk) {
                        window.pintrk = function () {
                            window.pintrk.queue.push(Array.prototype.slice.call(arguments))
                        };
                        var n = window.pintrk;
                        n.queue = [], n.version = "3.0";
                        var t = document.createElement("script");
                        t.async = !0, t.src = e;
                        var r = document.getElementsByTagName("script")[0];
                        r.parentNode.insertBefore(t, r)
                    }
                }("https://s.pinimg.com/ct/core.js");

                // initialize pixel
                getOptions().pinterest.pixelIds.forEach(function (pixelId) {
                    pintrk('load', pixelId, {em: getOptions().pinterest.advancedMatching.em, np: 'pixelyoursite'});
                    pintrk('page');
                });

                initialized = true;

                getUtils().fireStaticEvents('pinterest');

            },

            fireEvent: function (name, data) {

                if (!initialized || !this.isEnabled()) {
                    return false;
                }

                data.delay = data.delay || 0;
                data.params = data.params || {};

                if (data.delay === 0) {

                    fireEvent(name, data.params);

                } else {

                    setTimeout(function (name, params) {
                        fireEvent(name, params);
                    }, data.delay * 1000, name, data.params);

                }

                return true;

            },

            onAdSenseEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onClickEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onWatchVideo: function (event) {
                this.fireEvent(event.name, event);
            },

            onCommentEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onFormEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onDownloadEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onWooAddToCartOnButtonEvent: function (product_id) {
                if(!getOptions().dynamicEvents.woo_add_to_cart_on_button_click.hasOwnProperty(this.tag()))
                    return;

                if (window.pysWooProductData.hasOwnProperty(product_id)) {
                    if (window.pysWooProductData[product_id].hasOwnProperty('pinterest')) {

                        var event = getUtils().clone(getOptions().dynamicEvents.woo_add_to_cart_on_button_click[this.tag()]);
                        getUtils().copyProperties(window.pysWooProductData[product_id]['pinterest'].params, event.params);
                        this.fireEvent(event.name, event);

                    }
                }

            },

            onWooAddToCartOnSingleEvent: function (product_id, qty, product_type, is_external, $form) {

                window.pys_woo_product_data = window.pys_woo_product_data || [];

                if(!getOptions().dynamicEvents.woo_add_to_cart_on_button_click.hasOwnProperty(this.tag()))
                    return;


                if (product_type === getUtils().PRODUCT_VARIABLE && !getOptions().pinterest.wooVariableAsSimple) {
                    product_id = parseInt($form.find('input[name="variation_id"]').val());
                }

                if (window.pysWooProductData.hasOwnProperty(product_id)) {
                    if (window.pysWooProductData[product_id].hasOwnProperty('pinterest')) {

                        var event = getUtils().clone(getOptions().dynamicEvents.woo_add_to_cart_on_button_click[this.tag()])
                        getUtils().copyProperties(window.pysWooProductData[product_id]['pinterest'].params, event.params);

                        if(product_type === getUtils().PRODUCT_GROUPED ) {
                            var total = 0;
                            $form.find(".woocommerce-grouped-product-list .qty").each(function(index){
                                var childId = $(this).attr('name').replaceAll("quantity[","").replaceAll("]","");
                                var quantity = parseInt($(this).val());
                                if(isNaN(quantity)) {
                                    quantity = 0;
                                }
                                var price = window.pysWooProductData[product_id]['pinterest'].grouped[childId].price;
                                total += price * quantity;
                            });
                            if(total == 0) return;// skip if no items selected
                            if(getOptions().woo.addToCartOnButtonValueEnabled &&
                                getOptions().woo.addToCartOnButtonValueOption !== 'global') {
                                event.params.value = total;
                            }
                        } else {
                            // maybe customize value option
                            if (getOptions().woo.addToCartOnButtonValueEnabled &&
                                getOptions().woo.addToCartOnButtonValueOption !== 'global') {
                                event.params.value = event.params.value * qty;
                            }
                        }
                        event.params.product_quantity = qty;


                        if(product_type === getUtils().PRODUCT_BUNDLE) {
                            var data = $(".bundle_form .bundle_data").data("bundle_form_data");
                            var items_sum = getBundlePriceOnSingleProduct(data);
                            var price = (data.base_price+items_sum )* qty;
                            if (getOptions().woo.addToCartOnButtonValueEnabled && getOptions().woo.addToCartOnButtonValueOption !== 'global') {
                                event.params.value = price;
                            }
                        }



                        var event_name = is_external ? getOptions().woo.affiliateEventName : event.name;

                        this.fireEvent(event_name, event);

                    }
                }

            },

            onWooRemoveFromCartEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onWooAffiliateEvent: function (product_id) {
                if(!getOptions().dynamicEvents.woo_affiliate.hasOwnProperty(this.tag()))
                    return;
                var event = getOptions().dynamicEvents.woo_affiliate[this.tag()];

                if (window.pysWooProductData.hasOwnProperty(product_id)) {
                    if (window.pysWooProductData[product_id].hasOwnProperty('pinterest')) {

                        event = getUtils().copyProperties(event, {})
                        getUtils().copyProperties(window.pysWooProductData[product_id][this.tag()].params, event.params)
                        this.fireEvent( getOptions().woo.affiliateEventName, event);

                    }
                }

            },

            onWooPayPalEvent: function (event) {
                this.fireEvent(event.name, event);
            },

            onEddAddToCartOnButtonEvent: function (download_id, price_index, qty) {
                if(!getOptions().dynamicEvents.edd_add_to_cart_on_button_click.hasOwnProperty(this.tag()))
                    return;
                var event = getOptions().dynamicEvents.edd_add_to_cart_on_button_click[this.tag()];

                if (window.pysEddProductData.hasOwnProperty(download_id)) {

                    var index;

                    if (price_index) {
                        index = download_id + '_' + price_index;
                    } else {
                        index = download_id;
                    }

                    if (window.pysEddProductData[download_id].hasOwnProperty(index)) {
                        if (window.pysEddProductData[download_id][index].hasOwnProperty('pinterest')) {

                            event = getUtils().copyProperties(event, {})
                            getUtils().copyProperties(window.pysEddProductData[download_id][index]['pinterest'].params, event.params);

                            // maybe customize value option
                            if (getOptions().edd.addToCartOnButtonValueEnabled && getOptions().edd.addToCartOnButtonValueOption !== 'global') {
                                event.params.value = event.params.value * qty;
                            }

                            event.params.product_quantity = qty;

                            this.fireEvent(event.name,event);

                        }
                    }

                }

            },

            onEddRemoveFromCartEvent: function (event) {
                this.fireEvent(event.name, event);
            },
            onPageScroll: function (event) {
                this.fireEvent(event.name, event);
            },
            onTime: function (event) {
                this.fireEvent(event.name, event);
            },

        };

    }();

    window.pys = window.pys || {};
    window.pys.Pinterest = Pinterest;

}(jQuery);