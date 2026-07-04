# MODELO RELACIONAL - Óptica Golden

## Esquema SQL Completo

```sql
-- ============================================
-- TABLAS PRINCIPALES
-- ============================================

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id BIGINT UNSIGNED NOT NULL DEFAULT 2,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20) NULL,
    direccion TEXT NULL,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL,
    estado ENUM('activo', 'suspendido') NOT NULL DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE marcas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lentes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    categoria_id BIGINT UNSIGNED NOT NULL,
    genero ENUM('hombre', 'mujer', 'unisex') NOT NULL DEFAULT 'unisex',
    tipo_lente ENUM('optical', 'sol', 'ambos') NOT NULL DEFAULT 'optical',
    tipo_montura ENUM('completa', 'semi_al_aire', 'al_aire') NOT NULL,
    material VARCHAR(100) NULL,
    color VARCHAR(100) NULL,
    marca_id BIGINT UNSIGNED NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen_principal VARCHAR(255) NULL,
    estado ENUM('disponible', 'vendido') NOT NULL DEFAULT 'disponible',
    fecha_registro DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE RESTRICT,
    INDEX idx_lentes_estado (estado),
    INDEX idx_lentes_genero (genero),
    INDEX idx_lentes_precio (precio),
    INDEX idx_lentes_categoria (categoria_id),
    INDEX idx_lentes_marca (marca_id),
    INDEX idx_lentes_tipo_montura (tipo_montura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE imagenes_lentes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lente_id BIGINT UNSIGNED NOT NULL,
    url VARCHAR(255) NOT NULL,
    orden INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (lente_id) REFERENCES lentes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DE PEDIDOS Y PAGOS
-- ============================================

CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    fecha_pedido DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'en_preparacion', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    observaciones TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_pedidos_estado (estado),
    INDEX idx_pedidos_usuario (usuario_id),
    INDEX idx_pedidos_fecha (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detalle_pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    lente_id BIGINT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (lente_id) REFERENCES lentes(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_lente_pedido_activo (lente_id, pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pagos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL UNIQUE,
    metodo_pago ENUM('tarjeta_credito', 'tarjeta_debito', 'transferencia', 'efectivo') NOT NULL,
    fecha_pago DATETIME NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'reembolsado') NOT NULL DEFAULT 'pendiente',
    comprobante_url VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DE IA Y ANÁLISIS
-- ============================================

CREATE TABLE analisis_faciales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    imagen_url VARCHAR(255) NOT NULL,
    forma_rostro ENUM('ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante') NULL,
    puntos_referencia JSON NULL,
    confianza DECIMAL(5,2) NULL,
    tiempo_procesamiento INT NULL COMMENT 'en milisegundos',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recomendaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    analisis_facial_id BIGINT UNSIGNED NULL,
    forma_rostro ENUM('ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante') NULL,
    presupuesto_max DECIMAL(10,2) NULL,
    uso_lentes ENUM('computadora', 'lectura', 'estudio', 'conducir', 'uso_diario', 'deportes', 'moda') NOT NULL,
    estilo ENUM('clasico', 'moderno', 'ejecutivo', 'deportivo', 'minimalista') NOT NULL,
    color_favorito VARCHAR(100) NULL,
    tipo_montura ENUM('completa', 'semi_al_aire', 'al_aire') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (analisis_facial_id) REFERENCES analisis_faciales(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detalle_recomendaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recomendacion_id BIGINT UNSIGNED NOT NULL,
    lente_id BIGINT UNSIGNED NOT NULL,
    compatibilidad DECIMAL(5,2) NOT NULL COMMENT 'porcentaje 0-100',
    justificacion TEXT NULL,
    orden INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (recomendacion_id) REFERENCES recomendaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (lente_id) REFERENCES lentes(id) ON DELETE RESTRICT,
    INDEX idx_recomendacion_lente (recomendacion_id, lente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chat_ia (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    recomendacion_id BIGINT UNSIGNED NULL,
    sesion_id VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    respuesta TEXT NULL,
    tipo ENUM('usuario', 'sistema') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (recomendacion_id) REFERENCES recomendaciones(id) ON DELETE SET NULL,
    INDEX idx_chat_sesion (sesion_id),
    INDEX idx_chat_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS AUXILIARES
-- ============================================

CREATE TABLE carritos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    lente_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (lente_id) REFERENCES lentes(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_carrito_lente (usuario_id, lente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_tokenable (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
