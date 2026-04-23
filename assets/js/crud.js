/**
 * Smart Farming IoT — Generic CRUD Table/Modal Handlers
 */

const CRUD = {
    /**
     * Build an HTML table from data array
     */
    buildTable(columns, rows, actions = null) {
        if (!rows.length) return '<div class="empty-state"><i class="fas fa-inbox"></i><p>No records found</p></div>';
        let html = '<div class="table-wrapper"><table><thead><tr>';
        columns.forEach(col => { html += `<th>${col.label}</th>`; });
        if (actions) html += '<th class="text-right">Actions</th>';
        html += '</tr></thead><tbody>';
        rows.forEach(row => {
            html += '<tr>';
            columns.forEach(col => {
                let val = row[col.key] ?? '—';
                if (col.render) val = col.render(val, row);
                html += `<td>${val}</td>`;
            });
            if (actions) html += `<td class="text-right"><div class="btn-group" style="justify-content:flex-end">${actions(row)}</div></td>`;
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    },

    /**
     * Build a form from field definitions
     */
    buildForm(fields, data = {}) {
        let html = '<form id="crud-form">';
        let rowOpen = false;
        fields.forEach((field, i) => {
            if (field.hidden) {
                html += `<input type="hidden" name="${field.name}" value="${data[field.name] || field.default || ''}">`;
                return;
            }
            const val = data[field.name] ?? field.default ?? '';
            const req = field.required ? 'required' : '';
            html += '<div class="form-group">';
            html += `<label class="form-label" for="field-${field.name}">${field.label}</label>`;
            if (field.type === 'select') {
                html += `<select class="form-control" id="field-${field.name}" name="${field.name}" ${req}>`;
                html += `<option value="">Select...</option>`;
                (field.options || []).forEach(opt => {
                    const optVal = typeof opt === 'object' ? opt.value : opt;
                    const optLabel = typeof opt === 'object' ? opt.label : opt;
                    html += `<option value="${optVal}" ${optVal == val ? 'selected' : ''}>${optLabel}</option>`;
                });
                html += '</select>';
            } else if (field.type === 'textarea') {
                html += `<textarea class="form-control" id="field-${field.name}" name="${field.name}" rows="3" ${req}>${App.escapeHtml(String(val))}</textarea>`;
            } else {
                html += `<input class="form-control" type="${field.type || 'text'}" id="field-${field.name}" name="${field.name}" value="${App.escapeHtml(String(val))}" ${req} ${field.step ? 'step="'+field.step+'"' : ''}>`;
            }
            html += '</div>';
        });
        html += '</form>';
        return html;
    },

    /**
     * Get form data as object
     */
    getFormData(formId = 'crud-form') {
        const form = document.getElementById(formId);
        if (!form) return {};
        const fd = new FormData(form);
        const obj = {};
        for (const [key, value] of fd.entries()) { obj[key] = value; }
        return obj;
    },

    /**
     * Open create/edit modal
     */
    openFormModal(title, fields, data, onSave) {
        const bodyHtml = this.buildForm(fields, data);
        const isEdit = data && Object.keys(data).length > 0;
        const footerHtml = `
            <button class="btn btn-secondary" onclick="Modal.close()">Cancel</button>
            <button class="btn btn-primary" id="crud-save-btn"><i class="fas fa-${isEdit ? 'save' : 'plus'}"></i> ${isEdit ? 'Update' : 'Create'}</button>
        `;
        Modal.open(title, bodyHtml, footerHtml);
        document.getElementById('crud-save-btn').addEventListener('click', () => {
            const form = document.getElementById('crud-form');
            if (form && !form.checkValidity()) { form.reportValidity(); return; }
            onSave(this.getFormData());
        });
    },

    /**
     * Confirm delete modal
     */
    confirmDelete(name, onConfirm) {
        const bodyHtml = `<p style="color:var(--text-secondary)">Are you sure you want to delete <strong style="color:var(--text-primary)">${App.escapeHtml(name)}</strong>? This action cannot be undone.</p>`;
        const footerHtml = `
            <button class="btn btn-secondary" onclick="Modal.close()">Cancel</button>
            <button class="btn btn-danger" id="crud-delete-btn"><i class="fas fa-trash"></i> Delete</button>
        `;
        Modal.open('Confirm Delete', bodyHtml, footerHtml);
        document.getElementById('crud-delete-btn').addEventListener('click', () => { onConfirm(); Modal.close(); });
    }
};
