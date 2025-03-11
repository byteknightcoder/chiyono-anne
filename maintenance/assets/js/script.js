"use strict";

const contactToggleButton = document.getElementById("contact-toggle");
const contactOverlay = document.querySelector(".contact-overlay");
const contactClose = document.querySelector(".contact__close");
const content = document.querySelector(".content");

contactToggleButton.addEventListener("click", function () {
  contactOverlay.classList.add("open");
  content.style.transform = "translateX(-100vw)";
});

contactClose.addEventListener("click", function () {
  contactOverlay.classList.remove("open");
  content.style.transform = "translateX(0)";
});
