(function () {
  function includePartials() {
    document.querySelectorAll("[data-include]").forEach(async (el) => {
      const file = el.getAttribute("data-include");
      try {
        const res = await fetch(file, { cache: "no-cache" });
        if (!res.ok) throw new Error(res.status);
        const html = await res.text();
        el.insertAdjacentHTML("afterend", html);
        el.remove();
      } catch (e) {
        console.warn("Include failed:", file, e);
      }
    });
  }
  (document.readyState === "loading")
    ? document.addEventListener("DOMContentLoaded", includePartials)
    : includePartials();
})();

document.addEventListener('DOMContentLoaded', function() {
    // Get the current page from the body data attribute
    const currentPage = document.body.getAttribute('data-page');
    
    if (currentPage) {
        // Find the nav link with matching data-page attribute
        const activeLink = document.querySelector(`nav .nav-link[data-page="${currentPage}"]`);
        
        if (activeLink) {
            // Remove active class from all nav links
            document.querySelectorAll('nav .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to current page link
            activeLink.classList.add('active');
        }
    }
});