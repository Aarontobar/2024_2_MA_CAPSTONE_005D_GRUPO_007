function buscarUsuario() {
    const filter = document.getElementById('search-user').value.toLowerCase();
    const userList = document.getElementById('user-list');
    const users = userList.getElementsByTagName('li');

    for (let i = 0; i < users.length; i++) {
        const txtValue = users[i].textContent || users[i].innerText;
        users[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
    }
}