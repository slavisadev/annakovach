"use strict";

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

(function ($) {
  var flags = {};
  window.lightspeed_extra_checks = [].concat(_toConsumableArray(window.lightspeed_extra_checks || []), [
  /**
   * Add comments styles if we have comments element
   * @param selector
   * @return {*}
   */
  function (selector) {
    if (typeof flags['has-comments'] === 'undefined') {
      flags['has-comments'] = $('#comments').length > 0;
    }

    return flags['has-comments'] && selector.match(/comment/);
  },
  /**
   * Styles for the sticky sidebar
   * @param selector
   * @return {*}
   */
  function (selector) {
    if (typeof flags['has-sticky-sidebar'] === 'undefined') {
      var $sidebar = $('#theme-sidebar-section');
      flags['has-sticky-sidebar'] = $sidebar.length > 0 && $sidebar.attr('class').includes('sidebar-sticky-on-');
    }
    /* we can do this better ._. and search only for sticky styles TODO */


    return flags['has-sticky-sidebar'] && selector.match(/sidebar/);
  },
  /**
   * Styles for the offscreen sidebar
   * @param selector
   * @return {*}
   */
  function (selector) {
    if (typeof flags['has-offscreen-sidebar'] === 'undefined') {
      flags['has-offscreen-sidebar'] = $('#theme-sidebar-section').length > 0 && $('body.theme-has-off-screen-sidebar').length > 0;
    }

    return flags['has-offscreen-sidebar'] && selector.match(/sidebar/);
  },
  /**
   * Make sure the <iframe> styles are kept when optimizing the video template
   *
   * @param selector
   * @return {*}
   */
  function (selector) {
    if (typeof flags['is-video-template'] === 'undefined') {
      flags['is-video-template'] = $('.thrv_responsive_video[data-type="dynamic"]').length > 0 && $('body.single-format-video').length > 0;
    }

    return flags['is-video-template'] && selector.match(/.tve_responsive_video_container/);
  }]);
})(jQuery);
