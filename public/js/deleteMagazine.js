/**
 * Suppression d'un magazine avec confirmation de suppression
 */

// Récupère le bouton de suppression d'un magazine
const btnDelete = document.querySelectorAll('.btn-delete');

// Boucle sur tous les boutons comportant la classe CSS "btn-delete"
btnDelete.forEach(btn => {

  // Écouteur d'évènement sur le bouton au click
  btn.addEventListener('click', (event) => {

    // Empêche le comportement par défaut du lien hypertexte
    event.preventDefault();

    // Récupère le bouton de suppression de la modale vie une classe CSS
    const modalDelete = document.querySelector('.btn-delete-modal');

    // Récupère lle champ caché qui recevra la valeur du jeton CSRF
    const modalToken = document.querySelector('.input-token-csrf');

    // Attribue la valeur du href du bouton de suppression à l'action du formulaire de la modale
    modalDelete.action = btn.href;

    // Attribue le contenu de l'attribut "data-token" au champ caché du formulaire de la modale
    modalToken.value = btn.dataset.token;

    // Récupération de la modale
    const modal = new bootstrap.Modal(document.querySelector('#confirmDelete'));

    // Ouverture de la modale Bootstrap
    modal.show();
  });
})