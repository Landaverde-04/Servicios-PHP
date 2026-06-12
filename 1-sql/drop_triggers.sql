-- ============================================================================
-- DROP TRIGGERS: borra los 29 triggers (sin tocar tablas ni datos).
-- Se usa ANTES de recargar el llenado, para que los triggers de validacion
-- no bloqueen las inserciones. Despues se recrean con 03_triggers_mysql.sql.
-- ============================================================================
USE tours_gpo4;

DROP TRIGGER IF EXISTS trg_validar_visa;
DROP TRIGGER IF EXISTS trg_validar_vacunas;
DROP TRIGGER IF EXISTS trg_factura_validar_visa;
DROP TRIGGER IF EXISTS trg_factura_validar_vacunas;
DROP TRIGGER IF EXISTS trg_eliminar_turista_vacunas;
DROP TRIGGER IF EXISTS trg_eliminar_turista_documentos;
DROP TRIGGER IF EXISTS trg_validar_eliminar_destino_con_inscripciones;
DROP TRIGGER IF EXISTS trg_limpiar_requisitos_destino;
DROP TRIGGER IF EXISTS trg_limpiar_motivaciones_destino;
DROP TRIGGER IF EXISTS trg_limpiar_paquetes_destino;
DROP TRIGGER IF EXISTS trg_validar_vacuna_requisito_duplicada;
DROP TRIGGER IF EXISTS validar_eliminar_clase;
DROP TRIGGER IF EXISTS eliminar_inscripcion;
DROP TRIGGER IF EXISTS validar_cupos_paquete;
DROP TRIGGER IF EXISTS no_inscripciones_repetidas;
DROP TRIGGER IF EXISTS no_calses_repetida;
DROP TRIGGER IF EXISTS no_clases_repetidas_update;
DROP TRIGGER IF EXISTS validar_fecha_inscripcion_rango;
DROP TRIGGER IF EXISTS trg_vacuna_turista_insert;
DROP TRIGGER IF EXISTS trg_vacuna_turista_update;
DROP TRIGGER IF EXISTS trg_actualizar_inscripcion_pagada;
DROP TRIGGER IF EXISTS trg_vacuna_turista_duplicado;
DROP TRIGGER IF EXISTS trg_vacuna_turista_duplicado_update;
DROP TRIGGER IF EXISTS trg_anular_factura;
DROP TRIGGER IF EXISTS trg_cancelar_inscripciones;
DROP TRIGGER IF EXISTS trg_bloquear_destino_paquete_con_inscripciones;
DROP TRIGGER IF EXISTS trg_bloquear_eliminar_precio_clase;
DROP TRIGGER IF EXISTS trg_propagar_precio_clase;
DROP TRIGGER IF EXISTS trg_cancelar_inscripciones_paquete_inactivo;

-- ============================================================================
-- Triggers borrados. Ahora recargar 02_llenado y luego recrear con 03_triggers.
-- ============================================================================