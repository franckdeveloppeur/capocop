(function () {
  const init = () => {
    const customCheckboxes =
          document.querySelectorAll(".custom-checkbox-1");
    customCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const svg = this.parentElement.querySelector("svg");
        if (this.checked) {
          svg.classList.remove("hidden");
        } else {
          svg.classList.add("hidden");
        }
      });
    });

    const forms = document.querySelectorAll("form");
    forms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        alert(
          "Merci pour votre demande! Nous vous contacterons dans les plus brefs délais.",
        );
        this.reset();
      });
    });

    const productLinks = document.querySelectorAll('a[href^="#produits"]');
    productLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const target = this.getAttribute("href");
        if (target === "#produits" || target.includes("#produits-")) {
          document
            .querySelector("#produits")
            .scrollIntoView({ behavior: "smooth" });
        }
      });
    });

    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        const href = this.getAttribute("href");
        if (href.length > 1) {
          const target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        }
      });
    });
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
