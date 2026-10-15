const header = document.querySelector('.header');
const menuCheckbox = document.querySelector('.menu_hamburger_checkbox');
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    if (menuCheckbox.checked) {
        // If the menu is open, do not hide the header
        return;
    }

    const currentScrollY = window.scrollY;

    if (currentScrollY > lastScrollY && currentScrollY > 300 ) {
        // Scrolling down
        header.classList.add('header-cache');
    } else {
        // Scrolling up
        header.classList.remove('header-cache');
    }

    lastScrollY = currentScrollY;
});

