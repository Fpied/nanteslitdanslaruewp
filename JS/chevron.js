const chevron = document.querySelector(".chevron__haut");

function chevronCache(){
    chevron.classList.toggle("chevron__haut--cache", window.scrollY < 600 );
}

window.addEventListener("scroll", chevronCache);

chevronCache();

chevron.addEventListener("click", function(event){
    event.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});