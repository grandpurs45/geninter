const moyensList = document.querySelector('#moyens-list');
const addRowButton = document.querySelector('#add-row');

function bindRemoveButtons() {
    document.querySelectorAll('[data-remove-row]').forEach((button) => {
        button.onclick = () => {
            const rows = moyensList.querySelectorAll('.moyen-row');
            if (rows.length > 1) {
                button.closest('.moyen-row').remove();
            } else {
                rows[0].querySelectorAll('input, textarea').forEach((field) => {
                    field.value = '';
                });
            }
        };
    });
}

addRowButton?.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'moyen-row';
    row.innerHTML = `
        <label>
            Centre
            <input name="centre[]" value="">
        </label>
        <label>
            Engin(s)
            <textarea name="engin[]" rows="2"></textarea>
        </label>
        <button type="button" class="icon-action" data-remove-row aria-label="Supprimer ce moyen">×</button>
    `;

    moyensList.appendChild(row);
    bindRemoveButtons();
    row.querySelector('input')?.focus();
});

bindRemoveButtons();
