const lastpage = document.querySelector('.btn__lastpage');

if(lastpage){
    lastpage.addEventListener('click', () => {
        if(document.referrer.startsWith(window.location.origin)){
            history.back();
        } else{
            window.location.href = window.location.origin;
        }
    });
}
