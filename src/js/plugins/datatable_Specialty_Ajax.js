/**
 *
 * RowsAjax
 *
 * Interface.Plugins.Datatables.RowsAjax page content scripts. Initialized from scripts.js file.
 *
 *
 */

class RowsAjax {
  constructor() {
    if (!jQuery().DataTable) {
      console.log('DataTable is null!');
      return;
    }

    // Selected single row which will be edited
    this._rowToEdit;

    // Datatable instance
    this._datatable;

    // Edit or add state of the modal
    this._currentState;

    // Controls and select helper
    this._datatableExtend;

    // Add or edit modal
    this._addEditModal;

    // Datatable single item height
    this._staticHeight = 62;

    this._createInstance();
    this._addListeners();
    this._extend();
    this._initBootstrapModal();
  }

  // Creating datatable instance. Table data is provided by json/products.json file and loaded via ajax
  _createInstance() {
    const _this = this;
    this._datatable = jQuery('#datatableRowsAjax').DataTable({
      scrollX: true,
      buttons: ['copy', 'excel', 'csv', 'print'],
      info: false,
      ajax: '../../backend/php/GetSpecialties.php', // Cambiar '/ruta/de/obtener' por la ruta adecuada en tu servidor
      order: [], // Clearing default order
      sDom: '<"row"<"col-sm-12"<"table-container"t>r>><"row"<"col-12"p>>', // Hiding all other dom elements except table and pagination
      pageLength: 10,
      columns: [
        {data: 'name', title: 'Nombre del Area'},
        {data: 'description', title: 'Descripción'},
      ],
      language: {
        paginate: {
          previous: '<i class="cs-chevron-left"></i>',
          next: '<i class="cs-chevron-right"></i>',
        },
      },
      initComplete: function (settings, json) {
        _this._setInlineHeight();
      },
      drawCallback: function (settings) {
        _this._setInlineHeight();
      },
      columnDefs: [
        // Adding Name content as an anchor with a target #
        {
          targets: 0,
          render: function (data, type, row, meta) {
            return '<span class="badge bg-outline-primary"><a class="list-item-heading body" href="#">' + data + '</a></span>';
          },
        },
        // Adding checkbox for Check column
        {
          targets: 2,
          render: function (data, type, row, meta) {
            return '<div class="form-check float-end mt-1"><input type="checkbox" class="form-check-input"></div>';
          },
        },
      ],
    });
  }

  _addListeners() {
    // Listener for confirm button on the edit/add modal
    document.getElementById('addEditConfirmButton').addEventListener('click', this._addEditFromModalClick.bind(this));

    // Listener for add buttons
    document.querySelectorAll('.add-datatable').forEach((el) => el.addEventListener('click', this._onAddRowClick.bind(this)));

    // Listener for delete buttons
    document.querySelectorAll('.delete-datatable').forEach((el) => el.addEventListener('click', this._onDeleteClick.bind(this)));

    // Listener for edit button
    document.querySelectorAll('.edit-datatable').forEach((el) => el.addEventListener('click', this._onEditButtonClick.bind(this)));

    // Calling clear form when modal is closed
    document.getElementById('addEditModal').addEventListener('hidden.bs.modal', this._clearModalForm);
  }

  // Extending with DatatableExtend to get search, select and export working
  _extend() {
    this._datatableExtend = new DatatableExtend({
      datatable: this._datatable,
      editRowCallback: this._onEditRowClick.bind(this),
      singleSelectCallback: this._onSingleSelect.bind(this),
      multipleSelectCallback: this._onMultipleSelect.bind(this),
      anySelectCallback: this._onAnySelect.bind(this),
      noneSelectCallback: this._onNoneSelect.bind(this),
    });
  }

  // Keeping a reference to add/edit modal
  _initBootstrapModal() {
    this._addEditModal = new bootstrap.Modal(document.getElementById('addEditModal'));
  }

  // Setting static height to datatable to prevent pagination movement when list is not full
  _setInlineHeight() {
    if (!this._datatable) {
      return;
    }
    const pageLength = this._datatable.page.len();
    document.querySelector('.dataTables_scrollBody').style.height = this._staticHeight * pageLength + 'px';
  }

  // Add or edit button inside the modal click
  _addEditFromModalClick(event) {
    if (this._currentState === 'Agregar') {
      this._addNewRowFromModal();
    } else {
      this._editRowFromModal();
    }
    this._addEditModal.hide();
  }

  // Top side edit icon click
  _onEditButtonClick(event) {
    if (event.currentTarget.classList.contains('disabled')) {
      return;
    }
    const selected = this._datatableExtend.getSelectedRows();
    this._onEditRowClick(this._datatable.row(selected[0][0]));
  }

  // Direct click from row title
  _onEditRowClick(rowToEdit) {
    this._rowToEdit = rowToEdit; // Passed from DatatableExtend via callback from settings
    this._showModal('editar', 'Editar', 'Actualizar');
    this._setForm();
  }

  _editRowFromModal() {
    const data = this._rowToEdit.data();
    const formData = Object.assign(data, this._getFormData());
    const updatedData = {
      id_specialty: document.querySelector('#addEditModal input[name=id_specialty]').value,
      name: this._getFormData().name,
      description: this._getFormData().description,
    };

    // Realizar la solicitud AJAX para actualizar los datos en el servidor
    jQuery.ajax({
      type: 'POST', // O PUT, dependiendo de tu implementación en el archivo PHP
      url: '../../backend/php/UpdateSpecialty.php', // Ruta del archivo PHP que manejará la actualización
      data: updatedData,
      success: function (response) {
        // El servidor debe responder con el registro actualizado, actualizar el DataTable
        this._datatable.row(this._rowToEdit).data(updatedData).draw();
        this._datatableExtend.unCheckAllRows();
        this._datatableExtend.controlCheckAll();

        // Mostrar notificación de éxito
        jQuery.notify({title: response, message: 'Registro Actualizado exitosamente!', icon: 'cs-clipboard'}, {type: 'success', delay: 5000});

        // Mostrar mensaje de éxito en la consola
        console.log('Registro actualizado exitosamente:', response);
      }.bind(this),
      error: function (error) {
        // Mostrar notificación de advertencia
        jQuery.notify({title: 'Error', message: '¡Error al actualizar el registro!', icon: 'cs-close-circle'}, {type: 'warning', delay: 5000});
        // Mostrar mensaje de error en la consola
        console.error('Error al actualizar el registro:', error);
      },
    });
    console.log(updatedData);
  }

  // Add button inside th modal click
  _addNewRowFromModal() {
    const data = this._getFormData();
    // Realizar la solicitud AJAX para guardar los datos en el servidor
    jQuery.ajax({
      type: 'POST',
      url: '../../backend/php/AddSpecialty.php', // Cambiar '/ruta/de/guardar' por la ruta adecuada en tu servidor
      data: data,
      success: function (response) {
        // El servidor debe responder con el registro guardado, actualizar el DataTable
        this._datatable.row.add(data).draw();
        this._datatableExtend.unCheckAllRows();
        this._datatableExtend.controlCheckAll();

        // Mostrar notificación de éxito
        jQuery.notify({title: response, message: 'Área medica agregada exitosamente!', icon: 'cs-clipboard'}, {type: 'success', delay: 5000});

        // Mostrar mensaje en la consola
        console.log('Registro guardado exitosamente:', response);
      }.bind(this),
      error: function (error) {
        // Mostrar notificación de advertencia
        jQuery.notify({title: 'Error', message: '¡Por favor, inténtelo otra vez!', icon: 'cs-close-circle'}, {type: 'warning', delay: 5000});

        console.error('Error al guardar el registro:', error);
      },
    });
  }

  // Delete icon click
  /* _onDeleteClick() {
    const selected = this._datatableExtend.getSelectedRows();
    selected.remove().draw();
    this._datatableExtend.controlCheckAll();
  }*/

  _onDeleteClick() {
    const selectedRows = this._datatableExtend.getSelectedRows();
    const selectedData = selectedRows.data().toArray();
    const selectedIds = selectedData.map((data) => data.id_specialty); // Asegúrate de usar el nombre correcto del campo de ID
    console.log(selectedIds);
    // Realizar la solicitud AJAX para eliminar los registros en el servidor
    jQuery.ajax({
      type: 'POST',
      url: '../../backend/php/DeleteSpecialties.php', // Ruta del archivo PHP que manejará la eliminación
      data: {ids: selectedIds}, // Envía los IDs de los registros seleccionados al backend
      success: function (response) {
        // Elimina las filas seleccionadas del DataTable después de que se haya eliminado la base de datos
        selectedRows.remove().draw();

        // Mostrar notificación de éxito
        jQuery.notify({title: response, message: 'Registros eliminados exitosamente!', icon: 'cs-clipboard'}, {type: 'success', delay: 5000});

        // Mostrar mensaje de éxito en la consola
        console.log('Registros eliminados exitosamente:', response);
      },
      error: function (error) {
        // Mostrar notificación de advertencia
        jQuery.notify({title: 'Error', message: '¡Error al eliminar los registros!', icon: 'cs-close-circle'}, {type: 'warning', delay: 5000});

        // Mostrar mensaje de error en la consola
        console.error('Error al eliminar los registros:', error);
      },
    });
  }

  // + Add New or just + button from top side click
  _onAddRowClick() {
    this._showModal('Agregar', 'Agregar Nuevo', 'Agregar');
  }

  // Showing modal for an objective, add or edit
  _showModal(objective, title, button) {
    this._addEditModal.show();
    this._currentState = objective;
    document.getElementById('modalTitle').innerHTML = title;
    document.getElementById('addEditConfirmButton').innerHTML = button;
  }

  // Filling the modal form data
  _setForm() {
    const data = this._rowToEdit.data();
    document.querySelector('#addEditModal input[name=name]').value = data.name;
    document.querySelector('#addEditModal input[name=description]').value = data.description;
    document.querySelector('#addEditModal input[name=id_specialty]').value = data.id_specialty;
  }

  // Getting form values from the fields to pass to datatable
  _getFormData() {
    const data = {};
    data.name = document.querySelector('#addEditModal input[name=name]').value;
    data.description = document.querySelector('#addEditModal input[name=description]').value;
    return data;
  }

  // Clearing modal form
  _clearModalForm() {
    document.querySelector('#addEditModal form').reset();
  }

  // Single item select callback from DatatableExtend
  _onSingleSelect() {
    document.querySelectorAll('.edit-datatable').forEach((el) => el.classList.remove('disabled'));
  }

  // Multiple item select callback from DatatableExtend
  _onMultipleSelect() {
    document.querySelectorAll('.edit-datatable').forEach((el) => el.classList.add('disabled'));
  }

  // One or more item select callback from DatatableExtend
  _onAnySelect() {
    document.querySelectorAll('.delete-datatable').forEach((el) => el.classList.remove('disabled'));
    document.querySelectorAll('.tag-datatable').forEach((el) => el.classList.remove('disabled'));
  }

  // Deselect callback from DatatableExtend
  _onNoneSelect() {
    document.querySelectorAll('.delete-datatable').forEach((el) => el.classList.add('disabled'));
    document.querySelectorAll('.tag-datatable').forEach((el) => el.classList.add('disabled'));
  }
}
