const pass = document.querySelector("#user_pass");

if (pass) {
    const eye = document.createElement('button');
    eye.type = 'button';
    eye.className = 'btn__eye';
    eye.setAttribute('aria-label', 'Afficher le mot de passe');
    eye.innerHTML = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>';

    pass.after(eye);

    eye.addEventListener('click', ()=>{
        if(pass.type === 'password'){
            pass.type = 'text';
        } else{
            pass.type = 'password';
        }

    })
}