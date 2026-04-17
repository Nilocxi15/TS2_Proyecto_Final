CREATE DATABASE "IngeniaMath";

\c "IngeniaMath";

BEGIN;

-- Usuarios y Roles
CREATE TABLE
    roles (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(50) UNIQUE NOT NULL
    );

CREATE TABLE
    usuarios (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100),
        apellido VARCHAR(100),
        email VARCHAR(150) UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        foto_perfil TEXT,
        activo BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    usuario_roles (
        usuario_id INT REFERENCES usuarios (id),
        rol_id INT REFERENCES roles (id),
        PRIMARY KEY (usuario_id, rol_id)
    );

-- Modulos y Subtemas
CREATE TABLE
    modulos (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL
    );

CREATE TABLE
    subtemas (
        id SERIAL PRIMARY KEY,
        modulo_id INT REFERENCES modulos (id),
        nombre VARCHAR(100) NOT NULL,
        nivel_complejidad INT
    );

-- Banco de ejercicios
CREATE TYPE nivel_dificultad AS ENUM ('BASICO', 'INTERMEDIO', 'AVANZADO', 'EXAMEN');
CREATE TYPE tipo_ejercicio AS ENUM ('OPCION_MULTIPLE', 'VF', 'NUMERICO', 'COMPLETAR');
CREATE TYPE estado_ejercicio AS ENUM ('BORRADOR', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO');

CREATE TABLE ejercicios (
    id SERIAL PRIMARY KEY,
    modulo_id INT REFERENCES modulos(id),
    subtema_id INT REFERENCES subtemas(id),
    dificultad nivel_dificultad,
    tipo tipo_ejercicio,
    enunciado TEXT NOT NULL,
    imagen TEXT,
    respuesta_correcta TEXT,
    solucion TEXT,
    explicacion TEXT,
    tiempo_estimado INT,
    estado estado_ejercicio DEFAULT 'BORRADOR',
    creado_por INT REFERENCES usuarios(id),
    revisado_por INT REFERENCES usuarios(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ejercicios_relacionados (
    ejercicio_id INT REFERENCES ejercicios(id),
    relacionado_id INT REFERENCES ejercicios(id),
    PRIMARY KEY (ejercicio_id, relacionado_id)
);

-- Practicas y Respuestas
CREATE TABLE sesiones_practica (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    modo VARCHAR(20), -- libre o guiada
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE respuestas_usuario (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    ejercicio_id INT REFERENCES ejercicios(id),
    sesion_id INT REFERENCES sesiones_practica(id),
    respuesta TEXT,
    es_correcta BOOLEAN,
    tiempo_respuesta INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ejercicios_guardados (
    usuario_id INT REFERENCES usuarios(id),
    ejercicio_id INT REFERENCES ejercicios(id),
    PRIMARY KEY (usuario_id, ejercicio_id)
);

-- Diagnostico y Rutas
CREATE TABLE diagnosticos (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE resultados_diagnostico (
    diagnostico_id INT REFERENCES diagnosticos(id),
    modulo_id INT REFERENCES modulos(id),
    puntaje DECIMAL(5,2),
    estado VARCHAR(20), -- dominado, desarrollo, deficiente
    PRIMARY KEY (diagnostico_id, modulo_id)
);

CREATE TABLE rutas_aprendizaje (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ruta_detalle (
    ruta_id INT REFERENCES rutas_aprendizaje(id),
    subtema_id INT REFERENCES subtemas(id),
    prioridad INT,
    completado BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (ruta_id, subtema_id)
);

-- Simulacros
CREATE TABLE simulacros (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duracion INT,
    puntaje DECIMAL(5,2)
);

CREATE TABLE simulacro_preguntas (
    simulacro_id INT REFERENCES simulacros(id),
    ejercicio_id INT REFERENCES ejercicios(id),
    es_correcta BOOLEAN,
    PRIMARY KEY (simulacro_id, ejercicio_id)
);

-- Recursos educativos
CREATE TYPE tipo_recurso AS ENUM ('VIDEO', 'PDF', 'FLASHCARD', 'SIMULADOR');

CREATE TABLE recursos (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(150),
    descripcion TEXT,
    modulo_id INT REFERENCES modulos(id),
    subtema_id INT REFERENCES subtemas(id),
    tipo tipo_recurso,
    url TEXT,
    estado estado_ejercicio,
    creado_por INT REFERENCES usuarios(id)
);

CREATE TABLE flashcards (
    id SERIAL PRIMARY KEY,
    subtema_id INT REFERENCES subtemas(id),
    pregunta TEXT,
    respuesta TEXT
);

-- Foro
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    modulo_id INT REFERENCES modulos(id),
    subtema_id INT REFERENCES subtemas(id),
    contenido TEXT,
    estado VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE respuestas_post (
    id SERIAL PRIMARY KEY,
    post_id INT REFERENCES posts(id),
    usuario_id INT REFERENCES usuarios(id),
    contenido TEXT,
    es_solucion BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rendimiento para Respuestas Usuario
CREATE INDEX idx_respuestas_usuario_usuario ON respuestas_usuario(usuario_id);
CREATE INDEX idx_respuestas_usuario_ejercicio ON respuestas_usuario(ejercicio_id);

COMMIT;