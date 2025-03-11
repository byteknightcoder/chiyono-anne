'use strict';

/*! RUN SCRIPTS WHEN ELEMENTOR LOADED ( FOR PREVIEW MODE ONLY )------------------------------------------------->*/

var onElementorLoaded = function onElementorLoaded(callback) {
  if (undefined === window.elementorFrontend || undefined === window.elementorFrontend.hooks) {
    setTimeout(function () {
      return onElementorLoaded(callback);
    });
    return;
  }
  callback();
};

/*! WIDGET COUNTDOWN------------------------------------------------->*/
var countDown = function countDown() {
  var el = document.getElementsByClassName('flash-sale-cd'),
      elen = el.length,
      i = void 0;
  if (elen < 1) return;

  for (i = 0; i < elen; i++) {
    var _date = el[i].getAttribute('data-date'),
        days_id = el[i].getElementsByClassName('cd-time')[0].id,
        hours_id = el[i].getElementsByClassName('cd-time')[1].id,
        mins_id = el[i].getElementsByClassName('cd-time')[2].id,
        secs_id = el[i].getElementsByClassName('cd-time')[3].id;

    var counter = Doom({
      targetDate: _date,
      ids: {
        days: days_id,
        hours: hours_id,
        mins: mins_id,
        secs: secs_id
      }
    });

    counter.doom();
  }
};

var comingSoon = function comingSoon() {
  var countdowns = document.querySelectorAll('.zoa-countdown-wrapper');

  if (!countdowns) return;

  countdowns.forEach(function (countdownContainer) {

    var digits = Array.from(countdownContainer.children);
    var targetDate = countdownContainer.getAttribute('data-date');
    var countdown = Doom({
      targetDate: targetDate,
      ids: {
        days: digits[0].querySelector('.zoa-countdown-digit').id,
        hours: digits[1].querySelector('.zoa-countdown-digit').id,
        mins: digits[2].querySelector('.zoa-countdown-digit').id,
        secs: digits[3].querySelector('.zoa-countdown-digit').id
      }
    });

    countdown.doom();
  });
};

/*! PAGE SETTINGS------------------------------------------------->*/
var pageSettings = function pageSettings() {
  if (typeof elementor == 'undefined' || typeof elementor.settings.page == 'undefined') return;

  var elementorAction = function elementorAction() {
    elementor.reloadPreview();
    elementor.once('preview:loaded', function () {
      return elementor.getPanelView().setPage('page_settings');
    });
  };

  /* PAGE MENU LAYOUT */
  elementor.settings.page.addChangeCallback('page_menu_layout', function (newValue) {
    elementor.saver.update({
      onSuccess: function onSuccess() {
        elementorAction();
      }
    });
  });

  /* PAGE HEADER LAYOUT */
  elementor.settings.page.addChangeCallback('p_header_layout', function (newValue) {
    elementor.saver.update({
      onSuccess: function onSuccess() {
        elementorAction();
      }
    });
  });

  /* PAGE FOOTER LAYOUT */
  elementor.settings.page.addChangeCallback('p_footer_layout', function (newValue) {
    elementor.saver.update({
      onSuccess: function onSuccess() {
        elementorAction();
      }
    });
  });
};

/*! ON ELEMENTOR LOADED------------------------------------------------->*/
document.addEventListener('DOMContentLoaded', function () {

  onElementorLoaded(function () {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
      countDown();
      comingSoon();
    });

    pageSettings();
  });
});