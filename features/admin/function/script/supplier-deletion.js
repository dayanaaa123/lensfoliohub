document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById('confirmDeleteModal');
    const deleteIdInput = document.getElementById('deleteId');

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; 
        const userId = button.getAttribute('data-id');
        deleteIdInput.value = userId; 
    });
});