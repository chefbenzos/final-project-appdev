document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var toggleButton = document.getElementById('sidebar-toggle');

    toggleButton.addEventListener('click', function () {
        var collapsed = sidebar.classList.toggle('collapsed');
        toggleButton.setAttribute('aria-expanded', String(!collapsed));
    });
});
