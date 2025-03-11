"use strict";
(function ($) {
  jQuery(document).ready(function ($) {
    const renderCountdown = (element) => {
      var time = element.attr("data-timer").replace("Z", "");

      var countDownDate = new Date(time + window.yaydp_countdown_data.timezone_string).getTime();

      var initial_time = new Date().getTime();

      // Calculate the time left between now and the count down date
      var time_left = countDownDate - initial_time;
      var days = Math.floor(time_left / (1000 * 60 * 60 * 24));
      var hours = Math.floor(
        (time_left % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      var minutes = Math.floor((time_left % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((time_left % (1000 * 60)) / 1000);

      let countdown_text =
        days > 0
          ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
              days < 10 ? "0" + days : days
            }</span> <span class="yaydp-timer-label">DAYS</span></span>`
          : "";
      countdown_text +=
        hours > 0
          ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
              hours < 10 ? "0" + hours : hours
            }</span> <span class="yaydp-timer-label"> HRS </span></span>`
          : "";
      countdown_text +=
        hours > 0 ? `<span class="yaydp-timer-space-group">:</span>` : "";
      countdown_text +=
        hours > 0 || minutes > 0
          ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
              minutes < 10 ? "0" + minutes : minutes
            }</span> <span class="yaydp-timer-label"> MINS </span></span>`
          : "";
      countdown_text +=
        hours > 0 || minutes > 0
          ? `<span class="yaydp-timer-space-group">:</span>`
          : " ";
      countdown_text += `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
        seconds < 10 ? "0" + seconds : seconds
      }</span> <span class="yaydp-timer-label">SECS</span></span>`;

      element.html(countdown_text);

      // Update the count down every 1 second
      var CountdownFunc = setInterval(function () {
        // Get today's date and time
        var now = new Date().getTime();

        time_left = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(time_left / (1000 * 60 * 60 * 24));
        var hours = Math.floor(
          (time_left % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        var minutes = Math.floor((time_left % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((time_left % (1000 * 60)) / 1000);

        let countdown_text =
          days > 0
            ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
                days < 10 ? "0" + days : days
              }</span> <span class="yaydp-timer-label">DAYS</span></span>`
            : "";
        countdown_text +=
          hours > 0
            ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
                hours < 10 ? "0" + hours : hours
              }</span> <span class="yaydp-timer-label"> HRS </span></span>`
            : "";
        countdown_text +=
          hours > 0 ? `<span class="yaydp-timer-space-group">:</span>` : "";
        countdown_text +=
          hours > 0 || minutes > 0
            ? `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
                minutes < 10 ? "0" + minutes : minutes
              }</span> <span class="yaydp-timer-label"> MINS </span></span>`
            : "";
        countdown_text +=
          hours > 0 || minutes > 0
            ? `<span class="yaydp-timer-space-group">:</span>`
            : "";
        countdown_text += `<span class="yaydp-timer-group"><span class="yaydp-timer-number">${
          seconds < 10 ? "0" + seconds : seconds
        }</span> <span class="yaydp-timer-label">SECS</span></span>`;

        element.html(countdown_text);

        // If the count down is over, write some text
        if (time_left <= 1000) {
          clearInterval(CountdownFunc);
          // element.parent().parent().remove();
        }
      }, 1000);
    };

    $(".yaydp-event-wrapper .yaydp-event-countdown").each(function () {
      renderCountdown($(this));
    });
  });
})(jQuery);
