const sidebar = document.getElementById('sidebar');
const btn = document.getElementById('toggle-btn');

btn.onclick = function () {
    sidebar.classList.toggle('collapsed');
};
