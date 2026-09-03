-- =========================================================
-- 🌿 CASA NATURISTA LA HUANUQUEÑA
-- Script de base de datos completo
-- Importar en phpMyAdmin / MySQL (base de datos: casanaturista)
-- =========================================================

-- 🔥 IMPORTANTE: fuerza la codificación UTF-8 en esta sesión de
-- importación. Sin esto, las tildes y la "ñ" pueden guardarse mal
-- si tu cliente MySQL usa otra codificación por defecto (ej: al
-- importar con "mysql -u root < schema.sql" sin flags adicionales).
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS casanaturista CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE casanaturista;

-- =========================================================
-- TABLA: usuarios (acceso al panel de administración)
-- =========================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 🔑 Usuario administrador por defecto
-- Email:    admin@casanaturistalahuanuquena.com
-- Password: CasaNaturista2026
-- ⚠️ Cambia la contraseña luego de tu primer ingreso.
INSERT INTO usuarios (email, password) VALUES
('admin@casanaturistalahuanuquena.com', '$2y$10$MYGzaT/Fu0YLgLiE6zycYOYdUJxbiijFQ1L0mOBe6t8LRV2e9dYXW');

-- =========================================================
-- TABLA: categorias
-- =========================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 🔥 4 categorías iniciales
INSERT INTO categorias (id, nombre) VALUES
(1, 'Productos de la Colmena'),
(2, 'Plantas y Raíces Naturales'),
(3, 'Suplementos y Colágeno'),
(4, 'Nutrición Infantil');

-- =========================================================
-- TABLA: productos
-- 🔥 precio y precio_original son OPCIONALES (NULL permitido):
--    - Sin precio_original            -> se muestra "Consultar precio"
--    - Solo precio_original           -> se muestra un único precio
--    - precio_original + precio       -> se muestra como OFERTA
--      (precio siempre debe ser MENOR a precio_original)
-- =========================================================
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    precio DECIMAL(10,2) NULL,
    precio_original DECIMAL(10,2) NULL,
    imagen VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(100) NULL,
    categoria_id INT NULL,
    stock INT DEFAULT 0,
    caracteristicas TEXT NULL,
    creado DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para que las consultas del listado y del menú sean rápidas
CREATE INDEX idx_productos_categoria ON productos (categoria_id);
CREATE INDEX idx_productos_creado ON productos (creado);

-- =========================================================
-- TABLA: producto_imagenes (galería adicional por producto)
-- =========================================================
CREATE TABLE IF NOT EXISTS producto_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    CONSTRAINT fk_imagen_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- TABLA: marcas_destacadas (reservada para uso futuro)
-- =========================================================
CREATE TABLE IF NOT EXISTS marcas_destacadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    posicion INT NOT NULL,
    CONSTRAINT fk_marca_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- TABLA: sliders (reservada para uso futuro)
-- =========================================================
CREATE TABLE IF NOT EXISTS sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) NULL,
    titulo VARCHAR(150) NULL,
    subtitulo VARCHAR(255) NULL,
    enlace VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 🔥 PRODUCTOS DE EJEMPLO (14 productos, dentro del límite de 60)
-- Las imágenes se colocan en /casanaturista/imagenes/ con estos
-- nombres EXACTOS (formato JPG). Ver la lista completa que te
-- entrego aparte con nombre, tamaño y formato recomendado.
-- =========================================================

-- 🍯 Categoría 1: Productos de la Colmena
INSERT INTO productos (nombre, precio, precio_original, imagen, descripcion, categoria_id, stock, caracteristicas) VALUES
('Polen de Abeja x 250g', NULL, 35.00, 'polen-abeja.jpg',
 'Polen de abeja 100% natural, recolectado de flores silvestres. Fuente de proteínas, vitaminas y minerales para complementar tu alimentación diaria.',
 1, 20, 'Presentación: 250g\nOrigen: 100% natural\nConservación: lugar fresco y seco'),

('Própoleo en Gotas x 30ml', 22.00, 28.00, 'propoleo-gotas.jpg',
 'Extracto de propóleo puro en gotas. Tradicionalmente usado como apoyo para las defensas del organismo.',
 1, 30, 'Presentación: frasco 30ml\nModo de uso: diluir en agua o jugo\nOrigen: 100% natural'),

('Jalea Real x 100g', NULL, NULL, 'jalea-real.jpg',
 'Jalea real fresca, un producto natural de la colmena valorado por su aporte energético y nutritivo.',
 1, 15, 'Presentación: frasco 100g\nConservación: refrigerar después de abierto'),

('Miel de Abeja Pura x 500g', NULL, 25.00, 'miel-abeja.jpg',
 'Miel de abeja 100% pura, sin aditivos ni conservantes, cosechada de forma artesanal.',
 1, 40, 'Presentación: frasco 500g\nOrigen: 100% natural');

-- 🌿 Categoría 2: Plantas y Raíces Naturales
INSERT INTO productos (nombre, precio, precio_original, imagen, descripcion, categoria_id, stock, caracteristicas) VALUES
('Huanarpo Macho en Polvo x 250g', 24.00, 30.00, 'huanarpo-macho.jpg',
 'Huanarpo macho en polvo, planta tradicional de la selva peruana utilizada como tonificante natural.',
 2, 18, 'Presentación: 250g\nOrigen: selva peruana\nModo de uso: infusión'),

('Moringa en Cápsulas x 60', NULL, 45.00, 'moringa-capsulas.jpg',
 'Cápsulas de moringa, planta reconocida por su alto contenido de vitaminas, minerales y antioxidantes.',
 2, 25, 'Presentación: 60 cápsulas\nModo de uso: 1 a 2 cápsulas al día'),

('Maca Andina en Polvo x 250g', NULL, 22.00, 'maca-polvo.jpg',
 'Maca andina en polvo, raíz peruana tradicionalmente usada para aportar energía y vitalidad.',
 2, 35, 'Presentación: 250g\nOrigen: Andes peruanos\nModo de uso: agregar a batidos o jugos'),

('Uña de Gato en Cápsulas x 60', NULL, NULL, 'una-de-gato.jpg',
 'Uña de gato en cápsulas, planta amazónica de uso tradicional como apoyo del sistema de defensas.',
 2, 12, 'Presentación: 60 cápsulas\nOrigen: Amazonía peruana'),

('Hercampuri en Infusión x 100g', 14.00, 18.00, 'hercampuri.jpg',
 'Hercampuri en infusión, planta andina de uso tradicional como complemento de una dieta balanceada.',
 2, 22, 'Presentación: 100g\nModo de uso: infusión\nOrigen: Andes peruanos');

-- 💊 Categoría 3: Suplementos y Colágeno
INSERT INTO productos (nombre, precio, precio_original, imagen, descripcion, categoria_id, stock, caracteristicas) VALUES
('Colágeno Hidrolizado x 300g', NULL, 55.00, 'colageno-hidrolizado.jpg',
 'Colágeno hidrolizado en polvo, de fácil absorción, ideal para el cuidado de piel, cabello y articulaciones.',
 3, 28, 'Presentación: 300g\nModo de uso: 1 cucharada al día\nSabor: neutro'),

('Omega 3 en Perlas x 60', 32.00, 40.00, 'omega-3.jpg',
 'Omega 3 en perlas, ácidos grasos esenciales que contribuyen a la salud cardiovascular.',
 3, 30, 'Presentación: 60 perlas\nModo de uso: 1 perla al día'),

('Multivitamínico Natural x 60 cápsulas', NULL, NULL, 'multivitaminico.jpg',
 'Complejo multivitamínico natural, formulado a base de extractos vegetales para complementar tu dieta diaria.',
 3, 20, 'Presentación: 60 cápsulas\nModo de uso: 1 cápsula al día');

-- 🍭 Categoría 4: Nutrición Infantil
INSERT INTO productos (nombre, precio, precio_original, imagen, descripcion, categoria_id, stock, caracteristicas) VALUES
('Gomitas de Maca para Niños x 60', NULL, 32.00, 'gomitas-maca-ninos.jpg',
 'Gomitas masticables con extracto de maca, especialmente formuladas para el consumo infantil.',
 4, 26, 'Presentación: 60 gomitas\nSabor: frutas\nModo de uso: 1 a 2 gomitas al día'),

('Gomitas Multivitamínicas Infantiles x 60', 25.00, 30.00, 'gomitas-multivitaminicas.jpg',
 'Gomitas multivitamínicas para niños, con sabor a frutas, para complementar su alimentación diaria.',
 4, 24, 'Presentación: 60 gomitas\nSabor: frutas variadas'),

('Jarabe de Própoleo para Niños x 120ml', NULL, NULL, 'jarabe-propoleo-ninos.jpg',
 'Jarabe de própoleo especialmente formulado para niños, de sabor agradable.',
 4, 16, 'Presentación: frasco 120ml\nSabor: agradable para niños');
