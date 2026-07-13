const togglePassword = document.querySelector("#toggle-password");
const passwordField = document.querySelector("#password");

togglePassword.addEventListener('click', function (){
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    
    if (type === 'password') {
        this.classList.replace('fa-eye-slash', 'fa-eye');
    }
    else {
        this.classList.replace('fa-eye', 'fa-eye-slash');
    }
    passwordField.setAttribute('type', type);
});