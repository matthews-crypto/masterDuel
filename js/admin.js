// Fichier: js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour confirmer les actions de suppression
    const confirmDelete = (event) => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            event.preventDefault();
        }
    };

    // Ajouter des écouteurs d'événements pour tous les boutons de suppression
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', confirmDelete);
    });

    // Fonction pour afficher/masquer les formulaires d'édition
    const toggleEditForm = (event) => {
        const formId = event.target.getAttribute('data-form-id');
        const form = document.getElementById(formId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    };

    // Ajouter des écouteurs d'événements pour tous les boutons d'édition
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', toggleEditForm);
    });
});