document.addEventListener("DOMContentLoaded", () => {
  const sequence = [
    { id: "header", delay: 200, fromTop: true }
  ];

  sequence.forEach((item, index) => {
    setTimeout(() => {
      const el = document.getElementById(item.id);
      if (!el) return;

      el.classList.remove("opacity-0");

      if (item.fromTop) {
        el.classList.remove("-translate-y-6");
      } else {
        el.classList.remove("translate-y-4");
      }
    }, item.delay + index * 200);
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const sequence = [
    { id: "user-bar", delay: 100 },
    { id: "cards", delay: 200 },
    { id: "agenda", delay: 300 }
  ];

  sequence.forEach((item, index) => {
    setTimeout(() => {
      const el = document.getElementById(item.id);
      if (!el) return;

      el.classList.remove("opacity-0");

      if (item.fromTop) {
        el.classList.remove("-translate-y-6");
      } else {
        el.classList.remove("translate-y-4");
      }
    }, item.delay + index * 100);
  });
});

function filtraApps() {
    const searchInput = document.getElementById("search");
    const filter = searchInput.value.toLowerCase();
    const cards = document.getElementById("cards");
    const appNames = cards.getElementsByClassName("nome");

    for (let i = 0; i < appNames.length; i++) {
        const appName = appNames[i];
        if (appName.textContent.toLowerCase().includes(filter)) {
            appName.parentElement.parentElement.style.display = "block";
        } else {
            appName.parentElement.parentElement.style.display = "none";
        }
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const perfilBtn = document.getElementById("perfil");
    if (perfilBtn) {
        perfilBtn.addEventListener("click", function () {
            window.location.href = "/perfil";
        });
    }
});

