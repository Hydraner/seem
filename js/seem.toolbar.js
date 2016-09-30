/**
 * @file
 * Attaches behaviors for the seem module.
 */

(function ($, Drupal, drupalSettings, _, Backbone, JSON, storage) {

  'use strict';

  /**
   * Make seem links visible when clicking the link in the toolbar..
   *
   * @param {jQuery.Event} event
   *   The `drupalContextualLinkAdded` event.
   * @param {object} data
   *   An object containing the data relevant to the event.
   *
   * @listens event:drupalContextualLinkAdded
   */
  $(document).on('drupalContextualLinkAdded', function (event, data) {
    if (data.$region.is('.seem-region')) {
      $('.seem-toolbar-tab').removeClass('hidden');
      $('.seem-toolbar-tab').click(function () {
        if (!$('.toolbar-icon-edit').hasClass('visually-hidden')) {
          $('.seem-region .trigger').removeClass('visually-hidden');
        }
        else {
          $('.seem-region .trigger').toggleClass('visually-hidden');
        }
      });
      $('.seem-region .trigger').hover(function (e) {
        e.preventDefault();
        return false;
      });
    }
  });

})(jQuery, Drupal, drupalSettings, _, Backbone, window.JSON, window.sessionStorage);
