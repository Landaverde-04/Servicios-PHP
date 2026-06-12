-- ============================================================================
-- Sistema de Tours - Grupo 4 - ETAPA 2
-- RESET (solo vaciar): borra TODOS los datos de las tablas.
-- NO borra tablas ni triggers, NO inserta nada.
-- ----------------------------------------------------------------------------
-- USO: ejecutar este script para vaciar, y LUEGO ejecutar el
--      02_llenado_mysql.sql ACTUALIZADO (el que el grupo mantiene) para
--      recargar los datos de prueba de todos.
-- ============================================================================

USE tours_gpo4;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM ACCESO_USUARIO;
DELETE FROM OPCION_CRUD;
DELETE FROM USUARIO;
DELETE FROM DETALLE_FACTURA;
DELETE FROM VACUNA_TURISTA;
DELETE FROM DOCUMENTO_TURISTA;
DELETE FROM REQUISITO_VACUNA_DESTINO;
DELETE FROM PRECIO_PAQUETE_CLASE;
DELETE FROM PAQUETE_MOTIVACION;
DELETE FROM PAQUETE_DESTINO;
DELETE FROM DESTINO_MOTIVACION;
DELETE FROM INSCRIPCION;
DELETE FROM FACTURA;
DELETE FROM PAQUETE_TURISTICO;
DELETE FROM TURISTA;
DELETE FROM DESTINO;
DELETE FROM VACUNA;
DELETE FROM FORMA_PAGO;
DELETE FROM TIPO_AGRUPACION;
DELETE FROM TIPO_DOCUMENTO;
DELETE FROM ENFERMEDAD;
DELETE FROM MOTIVACIONES;
DELETE FROM PAIS;
DELETE FROM CLASE_PASAJERO;
DELETE FROM AGENCIA_VIAJE;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- Tablas vacias. Ahora ejecutar el 02_llenado_mysql.sql actualizado.
-- ============================================================================