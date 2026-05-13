function toggleSession(sid) {
    const body = document.getElementById('session-' + sid);
    body.classList.toggle('collapsed');
}
