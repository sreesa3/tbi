/**
 * SCSVMV TBI — light progressive enhancement only.
 * No forms, no network calls, no innerHTML of untrusted input.
 */
(function () {
  "use strict";

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function animateCounters() {
    var nodes = document.querySelectorAll("[data-count]");
    if (!nodes.length) return;

    nodes.forEach(function (el, index) {
      var target = parseInt(el.getAttribute("data-count"), 10);
      var suffix = el.getAttribute("data-suffix") || "";
      var prefix = el.getAttribute("data-prefix") || "";
      var duration = parseInt(el.getAttribute("data-duration") || "1800", 10);
      var delay = parseInt(el.getAttribute("data-delay") || String(index * 120), 10);

      if (isNaN(target)) return;

      if (reduceMotion) {
        el.textContent = prefix + target + suffix;
        return;
      }

      window.setTimeout(function () {
        var start = performance.now();

        function tick(now) {
          var progress = Math.min((now - start) / duration, 1);
          var eased = 1 - Math.pow(1 - progress, 3);
          var current = Math.round(eased * target);
          el.textContent = prefix + current + suffix;

          if (progress < 1) {
            window.requestAnimationFrame(tick);
          } else {
            el.textContent = prefix + target + suffix;
          }
        }

        window.requestAnimationFrame(tick);
      }, delay);
    });
  }

  function revealOnScroll() {
    var nodes = document.querySelectorAll(".reveal");
    if (!nodes.length) return;

    if (reduceMotion || !("IntersectionObserver" in window)) {
      nodes.forEach(function (el) {
        el.classList.add("is-visible");
      });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
    );

    nodes.forEach(function (el) {
      observer.observe(el);
    });
  }

  function hardenExternalAnchors() {
    document.querySelectorAll('a[target="_blank"]').forEach(function (a) {
      var rel = (a.getAttribute("rel") || "").toLowerCase();
      var parts = new Set(rel.split(/\s+/).filter(Boolean));
      parts.add("noopener");
      parts.add("noreferrer");
      a.setAttribute("rel", Array.from(parts).join(" "));
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      animateCounters();
      revealOnScroll();
      hardenExternalAnchors();
    });
  } else {
    animateCounters();
    revealOnScroll();
    hardenExternalAnchors();
  }
})();
