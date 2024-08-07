jQuery(document).ready(function($) {
    const templateCheckbox = $('#template_checkbox');
    const pageCheckboxes = $('.page-checkbox');

    function togglePageCheckboxes() {
        const disable = templateCheckbox.is(':checked');
        pageCheckboxes.prop('disabled', disable);

        if (disable) {
            pageCheckboxes.prop('checked', false); // Desmarcar las páginas si el template está seleccionado
        }
    }

    templateCheckbox.on('change', togglePageCheckboxes);

    // Run on page load in case the template checkbox is already checked
    togglePageCheckboxes();
});
