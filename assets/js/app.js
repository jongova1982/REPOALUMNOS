const modal = document.querySelector('#student-modal');
const form = document.querySelector('#student-form');

function openForCreate() {
    form.reset();
    document.querySelector('#form-action').value = 'create';
    document.querySelector('#original-cedula').value = '';
    document.querySelector('#modal-kicker').textContent = 'Nuevo registro';
    document.querySelector('#modal-title').textContent = 'Agregar alumno';
    document.querySelector('#submit-label').textContent = 'Guardar alumno';
    modal.showModal();
    document.querySelector('#cedula').focus();
}

function openForEdit(button) {
    document.querySelector('#form-action').value = 'update';
    document.querySelector('#original-cedula').value = button.dataset.cedula;
    document.querySelector('#cedula').value = button.dataset.cedula;
    document.querySelector('#nombre').value = button.dataset.nombre;
    document.querySelector('#telefono').value = button.dataset.telefono;
    document.querySelector('#modal-kicker').textContent = 'Actualizar registro';
    document.querySelector('#modal-title').textContent = 'Editar alumno';
    document.querySelector('#submit-label').textContent = 'Guardar cambios';
    modal.showModal();
    document.querySelector('#nombre').focus();
}

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', openForCreate);
});

document.querySelectorAll('[data-edit]').forEach((button) => {
    button.addEventListener('click', () => openForEdit(button));
});

document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => modal.close());
});

modal.addEventListener('click', (event) => {
    if (event.target === modal) modal.close();
});

document.querySelectorAll('[data-delete-form]').forEach((deleteForm) => {
    deleteForm.addEventListener('submit', (event) => {
        const name = deleteForm.dataset.student;
        if (!window.confirm(`¿Eliminar a ${name}? Esta acción no se puede deshacer.`)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('[data-close-alert]').forEach((button) => {
    button.addEventListener('click', () => button.parentElement.remove());
});
