//when user clicks on .fea-password-toggle, toggle the type of the password field and the icon class from dashicons-visibility to dashicons-hidden
//vanilla js
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('fea-password-toggle')) {
        let container = e.target.closest('.acf-field');
        let input = container.querySelector('input.fea-password');
        if (input.type === 'password') {
            input.type = 'text';
            e.target.classList.remove('dashicons-visibility');
            e.target.classList.add('dashicons-hidden');
        } else {
            input.type = 'password';
            e.target.classList.remove('dashicons-hidden');
            e.target.classList.add('dashicons-visibility');
        }
    }
});