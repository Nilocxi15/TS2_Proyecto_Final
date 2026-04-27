<?php

namespace App\Services;

class RespuestaValidator
{
    /**
     * Valida si la respuesta del usuario es correcta en base al tipo de ejercicio.
     *
     * @param string $tipo Tipo de ejercicio (OPCION_MULTIPLE, VF, NUMERICO, COMPLETAR)
     * @param string $respuestaUsuario La respuesta enviada por el estudiante
     * @param string $respuestaCorrecta La respuesta esperada del sistema
     * @return bool
     */
    public function validar(string $tipo, string $respuestaUsuario, string $respuestaCorrecta): bool
    {
        $respuestaUsuario = $this->normalizar($respuestaUsuario, $tipo);
        $respuestaCorrecta = $this->normalizar($respuestaCorrecta, $tipo);

        if ($tipo === 'NUMERICO') {
            // Tolerancia de +- 0.01
            $valEstudiante = floatval($respuestaUsuario);
            $valCorrecto = floatval($respuestaCorrecta);
            
            return abs($valEstudiante - $valCorrecto) <= 0.01;
        }

        if ($tipo === 'COMPLETAR') {
            // Para completar, se asume un arreglo ordenado por comas
            $arrEstudiante = array_map('trim', explode(',', strtolower($respuestaUsuario)));
            $arrCorrecto = array_map('trim', explode(',', strtolower($respuestaCorrecta)));
            
            // Ordenar arrays por si el orden no importa, o se pueden dejar estrictamente en el mismo orden
            // Según la regla: "separar por comas, trim a cada elemento, comparar"
            return $arrEstudiante === $arrCorrecto;
        }

        // Para OPCION_MULTIPLE y VF usamos comparacion exacta tras normalizacion
        return $respuestaUsuario === $respuestaCorrecta;
    }

    /**
     * Normaliza la cadena para compararla adecuadamente
     *
     * @param string $respuesta
     * @param string $tipo
     * @return string
     */
    public function normalizar(string $respuesta, string $tipo): string
    {
        $respuesta = trim($respuesta);
        
        if ($tipo === 'OPCION_MULTIPLE' || $tipo === 'VF') {
            return strtolower($respuesta);
        }

        return $respuesta;
    }
}
