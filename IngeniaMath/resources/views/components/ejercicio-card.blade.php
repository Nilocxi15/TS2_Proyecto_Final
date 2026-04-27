{{-- resources/views/components/ejercicio-card.blade.php --}}
@props(['modo' => 'PRACTICA'])

<div id="exerciseCard" class="card shadow-lg mb-4" style="border: 2px solid #e2e8f0;">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between">
        <span class="badge" id="badgeDif" style="font-size: 0.85rem;"></span>
        <span class="text-muted fw-bold" style="font-size: 0.85rem;" id="lblModuloSubtema"></span>
    </div>
    
    <div class="card-body p-4 pt-3">
        <h4 class="mb-4 fw-bold lh-base" id="enunciadoContainer" style="color: #2d3748;"></h4>
        
        <div id="imagenContainer" class="text-center mb-4 d-none">
            <img src="" id="exerciseImg" class="img-fluid rounded" style="max-height: 250px; border: 1px solid #e2e8f0;"/>
        </div>

        {{-- Zona dinámica renderizada por JS --}}
        <div id="answerArea" class="mt-4 pt-3 border-top text-center">
            
        </div>
    </div>
</div>

{{-- Feedback Oculto --}}
<div id="feedbackPanel" class="card shadow-lg mb-4 d-none">
    <div class="card-body p-4 rounded" id="feedbackBody">
        <div class="d-flex align-items-top gap-3">
            <div id="feedbackIcon" class="fs-1"></div>
            <div>
                <h3 id="feedbackTitle" class="fw-bold mb-2"></h3>
                <p class="text-muted mb-0" id="feedbackRespuestaCorrecta"></p><br>
                <hr id="feedbackDivider" class="d-none">
                <div class="math-preview" id="feedbackExplicacion"></div>
                <div class="text-muted mt-2" id="feedbackSolucion"></div>
            </div>
        </div>
    </div>
</div>

{{-- Controles Inferiores --}}
<div class="d-flex justify-content-end mb-5">
    <button class="btn btn-lg btn-success px-5 fw-bold" id="btnComprobar" style="border-radius: 12px;">COMPROBAR</button>
    <button class="btn btn-lg btn-primary px-5 fw-bold d-none" id="btnContinuar" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">CONTINUAR</button>
    <button class="btn btn-lg btn-primary px-5 fw-bold d-none" id="btnFinalizar" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">VER RESULTADOS</button>
</div>
