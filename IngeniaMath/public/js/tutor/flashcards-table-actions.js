(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function parseConfig() {
        var configNode = document.getElementById('resourceActionConfig');
        if (!configNode) {
            return null;
        }

        try {
            return JSON.parse(configNode.textContent || '{}');
        } catch (error) {
            return null;
        }
    }

    function setupDependentSubtemas() {
        // Filtra subtemas en formularios según módulo seleccionado.
        var filterForms = document.querySelectorAll('[data-tutor-filter-form], .js-tutor-filter-form');

        filterForms.forEach(function (form) {
            var moduloSelect = form.querySelector('[data-module-select]');
            var subtemaSelect = form.querySelector('[data-subtema-select]');

            if (!moduloSelect || !subtemaSelect) {
                return;
            }

            var allSubtemaOptions = Array.from(subtemaSelect.querySelectorAll('option'));

            function syncSubtemaOptions() {
                var selectedModulo = moduloSelect.value;
                var currentSubtema = subtemaSelect.value;

                allSubtemaOptions.forEach(function (option) {
                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    var belongsToModulo = option.dataset.moduloId === selectedModulo;
                    option.hidden = Boolean(selectedModulo) && !belongsToModulo;
                });

                var selectedOption = subtemaSelect.querySelector('option[value="' + currentSubtema + '"]');
                if (selectedOption && selectedOption.hidden) {
                    subtemaSelect.value = '';
                }
            }

            moduloSelect.addEventListener('change', syncSubtemaOptions);
            syncSubtemaOptions();
        });
    }

    function setupActionModal() {
        // Controlador principal del modal de acciones del panel tutor.
        var modalElement = document.getElementById('resourceActionModal');
        var modalConfig = parseConfig();

        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        var modal = new bootstrap.Modal(modalElement);
        var titleEl = document.getElementById('resourceActionModalLabel');
        var bodyEl = document.getElementById('resourceActionModalBody');
        var contextEl = document.getElementById('resourceActionModalContext');
        var fieldsEl = document.getElementById('resourceActionFields');
        var formEl = document.getElementById('resourceActionModalForm');
        var methodInput = document.getElementById('resourceActionMethod');
        var stateInput = document.getElementById('resourceActionState');
        var submitModeInput = document.getElementById('resourceActionSubmitMode');
        var draftBtn = document.getElementById('resourceActionDraftBtn');
        var confirmBtn = document.getElementById('resourceActionConfirmBtn');

        var actionConfig = {
            crear: {
                label: 'Crear',
                btnClass: 'btn-primary',
                description: 'Completa los datos y elige si deseas enviar o guardar en borrador.'
            },
            editar: {
                label: 'Editar',
                btnClass: 'btn-primary',
                description: 'Al guardar cambios, el estado pasará automáticamente a PENDIENTE.'
            },
            eliminar: {
                label: 'Eliminar',
                btnClass: 'btn-danger',
                description: 'Esta acción eliminará definitivamente el elemento.'
            },
            deshabilitar: {
                label: 'Deshabilitar',
                btnClass: 'btn-warning',
                description: 'El elemento quedará inactivo y no será visible para estudiantes.'
            },
            habilitar: {
                label: 'Habilitar',
                btnClass: 'btn-success',
                description: 'El elemento volverá a estar activo y disponible.'
            },
            revisar: {
                label: 'Enviar a revisión',
                btnClass: 'btn-info',
                description: 'El contenido quedará en estado REVISION para moderación.'
            }
        };

        var entityLabel = {
            recurso: 'recurso',
            flashcard: 'flashcard'
        };

        function buildOptions(items, selectedValue, includeBlank, extraDataKey) {
            var html = includeBlank ? '<option value="">Seleccione...</option>' : '';

            (items || []).forEach(function (item) {
                var value = String(item.id ?? item);
                var label = String(item.nombre ?? item);
                var selected = String(selectedValue ?? '') === value ? ' selected' : '';
                var extra = '';

                if (extraDataKey && item[extraDataKey] !== undefined) {
                    extra = ' data-' + extraDataKey.replace('_', '-') + '="' + escapeHtml(item[extraDataKey]) + '"';
                }

                html += '<option value="' + escapeHtml(value) + '"' + selected + extra + '>' + escapeHtml(label) + '</option>';
            });

            return html;
        }

        function buildResourceEditFields(trigger) {
            var tipoOptions = buildOptions(modalConfig?.recursosTipos || [], trigger.dataset.tipo, true);
            var moduloOptions = buildOptions(modalConfig?.recursosModulos || [], trigger.dataset.moduloId, true);
            var subtemaOptions = buildOptions(modalConfig?.recursosSubtemas || [], trigger.dataset.subtemaId, true, 'modulo_id');

            return [
                '<div class="row g-3">',
                '<div class="col-12"><label class="form-label">Título</label><input class="form-control" name="titulo" value="' + escapeHtml(trigger.dataset.titulo) + '"></div>',
                '<div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" rows="3" name="descripcion">' + escapeHtml(trigger.dataset.descripcion) + '</textarea></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Módulo</label><select class="form-select" name="modulo_id" data-modal-module-select>' + moduloOptions + '</select></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Subtema</label><select class="form-select" name="subtema_id" data-modal-subtema-select>' + subtemaOptions + '</select></div>',
                '<div class="col-12 col-md-4"><label class="form-label">Tipo</label><select class="form-select" name="tipo">' + tipoOptions + '</select></div>',
                '<div class="col-12 col-md-8"><label class="form-label">URL</label><input class="form-control" name="url" value="' + escapeHtml(trigger.dataset.url) + '"></div>',
                '<div class="col-12"><label class="form-label">Archivo (opcional)</label><input class="form-control" type="file" name="archivo" accept="image/*,video/*,application/pdf"><small class="text-muted">Si subes archivo, se reemplazará la URL actual.</small></div>',
                '</div>'
            ].join('');
        }

        function buildResourceCreateFields() {
            var tipoOptions = buildOptions(modalConfig?.recursosTipos || [], null, true);
            var moduloOptions = buildOptions(modalConfig?.recursosModulos || [], null, true);
            var subtemaOptions = buildOptions(modalConfig?.recursosSubtemas || [], null, true, 'modulo_id');

            return [
                '<div class="row g-3">',
                '<div class="col-12"><label class="form-label">Título</label><input class="form-control" name="titulo" required></div>',
                '<div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" rows="3" name="descripcion"></textarea></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Módulo</label><select class="form-select" name="modulo_id" data-modal-module-select required>' + moduloOptions + '</select></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Subtema</label><select class="form-select" name="subtema_id" data-modal-subtema-select required>' + subtemaOptions + '</select></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Tipo</label><select class="form-select" name="tipo">' + tipoOptions + '</select></div>',
                '<div class="col-12 col-md-6"><label class="form-label">Origen del recurso</label><select class="form-select" name="fuente_tipo" data-source-type><option value="url">URL</option><option value="archivo">Archivo propio</option></select></div>',
                '<div class="col-12" data-source-url><label class="form-label">URL</label><input class="form-control" type="url" name="url" maxlength="2000" placeholder="https://..." required></div>',
                '<div class="col-12 d-none" data-source-file><label class="form-label">Archivo</label><input class="form-control" type="file" name="archivo" accept="image/*,video/*,application/pdf"><small class="text-muted">Puedes subir imagen, video o PDF.</small></div>',
                '</div>'
            ].join('');
        }

        function buildFlashcardEditFields(trigger) {
            var subtemaOptions = buildOptions(modalConfig?.flashcardsSubtemas || [], trigger.dataset.subtemaId, true, 'modulo_id');

            return [
                '<div class="row g-3">',
                '<div class="col-12"><label class="form-label">Subtema</label><select class="form-select" name="subtema_id">' + subtemaOptions + '</select></div>',
                '<div class="col-12"><label class="form-label">Pregunta</label><textarea class="form-control" rows="3" name="pregunta">' + escapeHtml(trigger.dataset.pregunta) + '</textarea></div>',
                '<div class="col-12"><label class="form-label">Respuesta</label><textarea class="form-control" rows="3" name="respuesta">' + escapeHtml(trigger.dataset.respuesta) + '</textarea></div>',
                '</div>'
            ].join('');
        }

        function buildFlashcardCreateFields() {
            var subtemaOptions = buildOptions(modalConfig?.flashcardsSubtemas || [], null, true, 'modulo_id');

            return [
                '<div class="row g-3">',
                '<div class="col-12"><label class="form-label">Subtema</label><select class="form-select" name="subtema_id" required>' + subtemaOptions + '</select></div>',
                '<div class="col-12"><label class="form-label">Pregunta</label><textarea class="form-control" rows="3" name="pregunta" required></textarea></div>',
                '<div class="col-12"><label class="form-label">Respuesta</label><textarea class="form-control" rows="3" name="respuesta" required></textarea></div>',
                '</div>'
            ].join('');
        }

        function syncResourceSourceMode() {
            var sourceTypeSelect = fieldsEl.querySelector('[data-source-type]');
            if (!sourceTypeSelect) {
                return;
            }

            var urlWrapper = fieldsEl.querySelector('[data-source-url]');
            var fileWrapper = fieldsEl.querySelector('[data-source-file]');
            var urlInput = urlWrapper ? urlWrapper.querySelector('input[name="url"]') : null;
            var fileInput = fileWrapper ? fileWrapper.querySelector('input[name="archivo"]') : null;
            var tipoSelect = fieldsEl.querySelector('select[name="tipo"]');

            var mode = sourceTypeSelect.value;
            var isFile = mode === 'archivo';

            if (urlWrapper) {
                urlWrapper.classList.toggle('d-none', isFile);
            }
            if (fileWrapper) {
                fileWrapper.classList.toggle('d-none', !isFile);
            }

            if (urlInput) {
                urlInput.required = !isFile;
            }
            if (fileInput) {
                fileInput.required = isFile;
            }
            if (tipoSelect) {
                tipoSelect.required = !isFile;
            }
        }

        function syncModalSubtemaOptions() {
            var moduleSelect = fieldsEl.querySelector('[data-modal-module-select]');
            var subtemaSelect = fieldsEl.querySelector('[data-modal-subtema-select]');

            if (!moduleSelect || !subtemaSelect) {
                return;
            }

            var selectedModulo = moduleSelect.value;
            var options = Array.from(subtemaSelect.querySelectorAll('option'));

            options.forEach(function (option) {
                if (option.value === '') {
                    option.hidden = false;
                    return;
                }

                var moduloId = option.dataset.moduloId;
                option.hidden = Boolean(selectedModulo) && moduloId !== selectedModulo;
            });

            if (subtemaSelect.selectedOptions.length > 0 && subtemaSelect.selectedOptions[0].hidden) {
                subtemaSelect.value = '';
            }
        }

        function setConfirmStyle(btnClass) {
            confirmBtn.classList.remove('btn-primary', 'btn-danger', 'btn-warning', 'btn-success');
            confirmBtn.classList.add(btnClass || 'btn-primary');
        }

        if (draftBtn) {
            draftBtn.addEventListener('click', function () {
                submitModeInput.value = 'borrador';
            });
        }

        confirmBtn.addEventListener('click', function () {
            submitModeInput.value = 'enviar';
        });

        formEl.addEventListener('submit', function () {
            if (!submitModeInput.value) {
                submitModeInput.value = 'enviar';
            }
        });

        document.addEventListener('click', function (event) {
            // Resuelve acción según dataset del botón presionado.
            var trigger = event.target.closest('.js-open-action-modal');
            if (!trigger) {
                return;
            }

            var currentAction = trigger.dataset.action;
            var currentEntityType = trigger.dataset.entityType;
            var currentEntityId = trigger.dataset.entityId;
            var currentEntityName = trigger.dataset.entityName;

            var actionSettings = actionConfig[currentAction] || actionConfig.editar;
            var readableEntityType = entityLabel[currentEntityType] || 'elemento';

            titleEl.textContent = actionSettings.label + ' ' + readableEntityType;
            bodyEl.textContent = actionSettings.description;
            contextEl.innerHTML = '<strong>ID:</strong> ' + (currentEntityId || '-') + '<br><strong>Nombre:</strong> ' + (currentEntityName || '-');
            confirmBtn.textContent = actionSettings.label;
            setConfirmStyle(actionSettings.btnClass);
            submitModeInput.value = 'enviar';
            if (draftBtn) {
                draftBtn.classList.add('d-none');
            }
            formEl.enctype = 'application/x-www-form-urlencoded';

            fieldsEl.innerHTML = '';
            stateInput.value = '';
            methodInput.value = 'POST';

            if (currentAction === 'crear') {
                formEl.action = currentEntityType === 'recurso'
                    ? (modalConfig?.createResourceUrl || '#')
                    : (modalConfig?.createFlashcardUrl || '#');

                if (currentEntityType === 'recurso') {
                    fieldsEl.innerHTML = buildResourceCreateFields();
                    formEl.enctype = 'multipart/form-data';

                    var createModuleSelect = fieldsEl.querySelector('[data-modal-module-select]');
                    if (createModuleSelect) {
                        createModuleSelect.addEventListener('change', syncModalSubtemaOptions);
                        syncModalSubtemaOptions();
                    }

                    var sourceTypeSelect = fieldsEl.querySelector('[data-source-type]');
                    if (sourceTypeSelect) {
                        sourceTypeSelect.addEventListener('change', syncResourceSourceMode);
                        syncResourceSourceMode();
                    }
                } else {
                    fieldsEl.innerHTML = buildFlashcardCreateFields();
                }

                confirmBtn.textContent = 'Enviar';
                if (draftBtn) {
                    draftBtn.classList.remove('d-none');
                }
            } else if (currentAction === 'editar') {
                formEl.action = trigger.dataset.updateUrl || '#';
                methodInput.value = 'PATCH';
                fieldsEl.innerHTML = currentEntityType === 'recurso'
                    ? buildResourceEditFields(trigger)
                    : buildFlashcardEditFields(trigger);

                if (currentEntityType === 'recurso') {
                    formEl.enctype = 'multipart/form-data';
                }

                var modalModuleSelect = fieldsEl.querySelector('[data-modal-module-select]');
                if (modalModuleSelect) {
                    modalModuleSelect.addEventListener('change', syncModalSubtemaOptions);
                    syncModalSubtemaOptions();
                }
            } else if (currentAction === 'eliminar') {
                formEl.action = trigger.dataset.deleteUrl || '#';
                methodInput.value = 'DELETE';
            } else {
                formEl.action = trigger.dataset.stateUrl || '#';
                methodInput.value = 'PATCH';
                stateInput.value = trigger.dataset.stateTarget || '';
            }

            modal.show();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Inicialización del comportamiento de filtros y modal.
        setupDependentSubtemas();
        setupActionModal();
    });
})();
