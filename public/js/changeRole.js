/**
 * Modifier le rôle d'un utilisateur
 */

 const buttons = document.querySelectorAll('button.btn');

 buttons.forEach(btn => {
     btn.addEventListener('click', () => {
         const id = btn.dataset.id;
         const role = btn.dataset.role;
 
         fetch(`/admin/user/${id}/roles/${role}`, {
             method: 'POST',
             headers: {
                 'Accept': 'application/json',
                 'Content-Type': 'application/json'
             }
         })
             .then(response => response.json())
             .then(data => {
                 // alert(data.role);
                 document.querySelector(`#roles_${id}`).innerText = data.role;
             })
             .catch(error => alert(error))
     })
 });