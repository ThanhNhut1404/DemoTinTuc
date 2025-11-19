(function(){
    // sidebar toggle logic (kept minimal)
    document.addEventListener('DOMContentLoaded', function(){
        var sidebar = document.getElementById('sidebar');
        var toggle = document.getElementById('sidebarToggle');
        if (!sidebar || !toggle) return;
        toggle.addEventListener('click', function(){
            if (window.innerWidth <= 900) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
        document.addEventListener('click', function(e){
            if (window.innerWidth <= 900 && sidebar.classList.contains('open')){
                var inside = sidebar.contains(e.target) || toggle.contains(e.target);
                if (!inside) sidebar.classList.remove('open');
            }
        });
    });
})();
