{{-- Configuración JSON y modal reutilizable para acciones del panel tutor. --}}
<script id="resourceActionConfig" type="application/json">
    @json($resourceActionConfig)
</script>

<div class="modal fade" id="resourceActionModal" tabindex="-1" aria-labelledby="resourceActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content tutor-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="resourceActionModalLabel">Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="resourceActionModalForm" method="POST" action="#">
                @csrf
                <input type="hidden" name="_method" id="resourceActionMethod" value="POST">
                <input type="hidden" name="estado" id="resourceActionState" value="">
                <input type="hidden" name="accion_envio" id="resourceActionSubmitMode" value="enviar">

                <div class="modal-body">
                    <p class="mb-2" id="resourceActionModalBody"></p>
                    <div class="modal-context" id="resourceActionModalContext"></div>
                    <div id="resourceActionFields" class="modal-edit-fields mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline-primary d-none" id="resourceActionDraftBtn" data-submit-mode="borrador">
                        Guardar borrador
                    </button>
                    <button type="submit" class="btn" id="resourceActionConfirmBtn">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
