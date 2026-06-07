-- ============================================================================
-- Sistema de Tours - Grupo 4 - ETAPA 2
-- TODOS los triggers convertidos de SQLite a MySQL
-- Equivalentes a crearTriggers() de BDHelperGpo4.kt
-- ----------------------------------------------------------------------------
-- En MySQL se aborta una operacion con: SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='...'
-- (equivale al RAISE(ABORT,'...') de SQLite)
-- ============================================================================
USE tours_gpo4;

-- Borrado previo para poder re-ejecutar sin errores
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

DELIMITER //

-- 1) Validar visa al inscribir
CREATE TRIGGER trg_validar_visa
BEFORE INSERT ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        JOIN DESTINO D ON PD.IDDESTINO = D.IDDESTINO
        WHERE PD.IDPAQUETE = NEW.IDPAQUETE AND D.REQUIEREVISAESP = 1
    )
    AND (SELECT TIENEVISA FROM TURISTA WHERE IDTURISTA = NEW.IDTURISTA) = 0
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El turista no tiene visa y el destino la requiere';
    END IF;
END//

-- 2) Validar vacunas al inscribir
CREATE TRIGGER trg_validar_vacunas
BEFORE INSERT ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        JOIN REQUISITO_VACUNA_DESTINO RVD ON PD.IDDESTINO = RVD.IDDESTINO
        JOIN PAQUETE_TURISTICO PT ON PD.IDPAQUETE = PT.IDPAQUETE
        WHERE PD.IDPAQUETE = NEW.IDPAQUETE
          AND NOT EXISTS (
              SELECT 1 FROM VACUNA_TURISTA VT
              WHERE VT.IDTURISTA = NEW.IDTURISTA
                AND VT.IDVACUNA = RVD.IDVACUNA
                AND VT.FECHAVIGENCIA >= PT.FECHAINICIO
          )
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El turista no tiene todas las vacunas requeridas vigentes para este paquete';
    END IF;
END//

-- 3) Validar visa al facturar
CREATE TRIGGER trg_factura_validar_visa
BEFORE INSERT ON DETALLE_FACTURA
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM INSCRIPCION I
        JOIN PAQUETE_DESTINO PD ON I.IDPAQUETE = PD.IDPAQUETE
        JOIN DESTINO D ON PD.IDDESTINO = D.IDDESTINO
        WHERE I.IDINSCRIPCION = NEW.IDINSCRIPCION AND D.REQUIEREVISAESP = 1
    )
    AND EXISTS (
        SELECT 1 FROM INSCRIPCION I
        JOIN TURISTA T ON I.IDTURISTA = T.IDTURISTA
        WHERE I.IDINSCRIPCION = NEW.IDINSCRIPCION AND T.TIENEVISA = 0
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede facturar: el turista no tiene visa y el destino la requiere';
    END IF;
END//

-- 4) Validar vacunas al facturar
CREATE TRIGGER trg_factura_validar_vacunas
BEFORE INSERT ON DETALLE_FACTURA
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM INSCRIPCION I
        JOIN PAQUETE_DESTINO PD ON I.IDPAQUETE = PD.IDPAQUETE
        JOIN REQUISITO_VACUNA_DESTINO RVD ON PD.IDDESTINO = RVD.IDDESTINO
        JOIN PAQUETE_TURISTICO PT ON I.IDPAQUETE = PT.IDPAQUETE
        WHERE I.IDINSCRIPCION = NEW.IDINSCRIPCION
          AND NOT EXISTS (
              SELECT 1 FROM VACUNA_TURISTA VT
              WHERE VT.IDTURISTA = I.IDTURISTA
                AND VT.IDVACUNA = RVD.IDVACUNA
                AND VT.FECHAVIGENCIA >= PT.FECHAINICIO
          )
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede facturar: el turista no tiene todas las vacunas requeridas vigentes';
    END IF;
END//

-- 5a) Al borrar TURISTA -> borrar sus VACUNA_TURISTA
CREATE TRIGGER trg_eliminar_turista_vacunas
AFTER DELETE ON TURISTA
FOR EACH ROW
BEGIN
    DELETE FROM VACUNA_TURISTA WHERE IDTURISTA = OLD.IDTURISTA;
END//

-- 5b) Al borrar TURISTA -> borrar sus DOCUMENTO_TURISTA
CREATE TRIGGER trg_eliminar_turista_documentos
AFTER DELETE ON TURISTA
FOR EACH ROW
BEGIN
    DELETE FROM DOCUMENTO_TURISTA WHERE IDTURISTA = OLD.IDTURISTA;
END//

-- 6a) No eliminar destino usado por inscripciones
CREATE TRIGGER trg_validar_eliminar_destino_con_inscripciones
BEFORE DELETE ON DESTINO
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        INNER JOIN INSCRIPCION I ON I.IDPAQUETE = PD.IDPAQUETE
        WHERE PD.IDDESTINO = OLD.IDDESTINO
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar: el destino pertenece a paquetes con inscripciones registradas.';
    END IF;
END//

-- 6b) Si el destino no tiene historial, limpiar vacunas requeridas
CREATE TRIGGER trg_limpiar_requisitos_destino
BEFORE DELETE ON DESTINO
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        INNER JOIN INSCRIPCION I ON I.IDPAQUETE = PD.IDPAQUETE
        WHERE PD.IDDESTINO = OLD.IDDESTINO
    ) THEN
        DELETE FROM REQUISITO_VACUNA_DESTINO WHERE IDDESTINO = OLD.IDDESTINO;
    END IF;
END//

-- 6c) Si el destino no tiene historial, limpiar motivaciones
CREATE TRIGGER trg_limpiar_motivaciones_destino
BEFORE DELETE ON DESTINO
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        INNER JOIN INSCRIPCION I ON I.IDPAQUETE = PD.IDPAQUETE
        WHERE PD.IDDESTINO = OLD.IDDESTINO
    ) THEN
        DELETE FROM DESTINO_MOTIVACION WHERE IDDESTINO = OLD.IDDESTINO;
    END IF;
END//

-- 6d) Si el destino no tiene historial, limpiar paquetes asociados
CREATE TRIGGER trg_limpiar_paquetes_destino
BEFORE DELETE ON DESTINO
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM PAQUETE_DESTINO PD
        INNER JOIN INSCRIPCION I ON I.IDPAQUETE = PD.IDPAQUETE
        WHERE PD.IDDESTINO = OLD.IDDESTINO
    ) THEN
        DELETE FROM PAQUETE_DESTINO WHERE IDDESTINO = OLD.IDDESTINO;
    END IF;
END//

-- 7) Evitar vacuna requisito duplicada para un destino
CREATE TRIGGER trg_validar_vacuna_requisito_duplicada
BEFORE INSERT ON REQUISITO_VACUNA_DESTINO
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM REQUISITO_VACUNA_DESTINO
        WHERE IDDESTINO = NEW.IDDESTINO AND IDVACUNA = NEW.IDVACUNA
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede agregar: esta vacuna ya es requisito para este destino.';
    END IF;
END//

-- 8) No eliminar clase usada por inscripciones o precios
CREATE TRIGGER validar_eliminar_clase
BEFORE DELETE ON CLASE_PASAJERO
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM INSCRIPCION WHERE IDCLASE = OLD.IDCLASE) > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar: Esta clase esta asignada a una o mas inscripciones.';
    END IF;
    IF (SELECT COUNT(*) FROM PRECIO_PAQUETE_CLASE WHERE IDCLASE = OLD.IDCLASE) > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar: Esta clase esta asignada a uno mas precios de paquete clase';
    END IF;
END//

-- 9) No eliminar inscripcion con detalle de factura
CREATE TRIGGER eliminar_inscripcion
BEFORE DELETE ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM DETALLE_FACTURA WHERE IDINSCRIPCION = OLD.IDINSCRIPCION) > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NO SE PUEDE ELIMINAR ESTA INSCRIPCION DEBIDO A QUE TIENE REGISTROS ASOCIADOS';
    END IF;
END//

-- 10) Validar cupos del paquete al inscribir
CREATE TRIGGER validar_cupos_paquete
BEFORE INSERT ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF (
        SELECT COUNT(*) FROM INSCRIPCION
        WHERE IDPAQUETE = NEW.IDPAQUETE AND ESTADO != 'Cancelado'
    ) >= (
        SELECT CUPOMAXIMO FROM PAQUETE_TURISTICO WHERE IDPAQUETE = NEW.IDPAQUETE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Capacidad maxima alcanzada: Este paquete ya no tiene cupos disponibles.';
    END IF;
END//

-- 11) No inscripciones repetidas (salvo canceladas)
CREATE TRIGGER no_inscripciones_repetidas
BEFORE INSERT ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM INSCRIPCION
        WHERE IDTURISTA = NEW.IDTURISTA AND IDPAQUETE = NEW.IDPAQUETE AND ESTADO != 'Cancelado'
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EL TURISTA YA ESTA REGISTRADO EN ESTE PAQUETE';
    END IF;
END//

-- 12) No clases repetidas al insertar
CREATE TRIGGER no_calses_repetida
BEFORE INSERT ON CLASE_PASAJERO
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM CLASE_PASAJERO WHERE NOMBRECLASE = NEW.NOMBRECLASE) > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'LA CLASE QUE INTENTA GUARDAR YA ESTA INGRESADA';
    END IF;
END//

-- 13) No clases repetidas al actualizar nombre
CREATE TRIGGER no_clases_repetidas_update
BEFORE UPDATE ON CLASE_PASAJERO
FOR EACH ROW
BEGIN
    IF NEW.NOMBRECLASE <> OLD.NOMBRECLASE AND EXISTS (
        SELECT 1 FROM CLASE_PASAJERO
        WHERE NOMBRECLASE = NEW.NOMBRECLASE AND IDCLASE != OLD.IDCLASE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe otra clase registrada con ese nombre.';
    END IF;
END//

-- 14) Fecha de inscripcion dentro del rango del paquete
CREATE TRIGGER validar_fecha_inscripcion_rango
BEFORE INSERT ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF NEW.FECHAINSCRIPCION < (SELECT FECHAINICIO FROM PAQUETE_TURISTICO WHERE IDPAQUETE = NEW.IDPAQUETE)
       OR NEW.FECHAINSCRIPCION > (SELECT FECHAFIN FROM PAQUETE_TURISTICO WHERE IDPAQUETE = NEW.IDPAQUETE)
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha de inscripcion debe estar dentro del rango de duracion del paquete.';
    END IF;
END//

-- 15) Vacuna turista: fecha aplicacion <= vigencia (insert)
CREATE TRIGGER trg_vacuna_turista_insert
BEFORE INSERT ON VACUNA_TURISTA
FOR EACH ROW
BEGIN
    IF NEW.FECHAAPLICACION > NEW.FECHAVIGENCIA THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha de aplicacion no puede ser mayor a la fecha de vigencia';
    END IF;
END//

-- 16) Vacuna turista: fecha aplicacion <= vigencia (update)
CREATE TRIGGER trg_vacuna_turista_update
BEFORE UPDATE ON VACUNA_TURISTA
FOR EACH ROW
BEGIN
    IF NEW.FECHAAPLICACION > NEW.FECHAVIGENCIA THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha de aplicacion no puede ser mayor a la fecha de vigencia';
    END IF;
END//

-- 17) Al pagar factura -> inscripciones a 'Pagado'
CREATE TRIGGER trg_actualizar_inscripcion_pagada
AFTER UPDATE ON FACTURA
FOR EACH ROW
BEGIN
    IF NEW.ESTADO = 'Pagada' AND OLD.ESTADO <> 'Pagada' THEN
        UPDATE INSCRIPCION
        SET ESTADO = 'Pagado'
        WHERE IDINSCRIPCION IN (
            SELECT IDINSCRIPCION FROM DETALLE_FACTURA WHERE IDFACTURA = NEW.IDFACTURA
        );
    END IF;
END//

-- 18) Vacuna turista duplicada (insert)
CREATE TRIGGER trg_vacuna_turista_duplicado
BEFORE INSERT ON VACUNA_TURISTA
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM VACUNA_TURISTA
        WHERE IDTURISTA = NEW.IDTURISTA AND IDVACUNA = NEW.IDVACUNA
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Este turista ya tiene registrada esta vacuna';
    END IF;
END//

-- 19) Vacuna turista duplicada (update)
-- En SQLite usaba ROWID; en MySQL comparamos por la PK (IDTURISTA+IDVACUNA).
CREATE TRIGGER trg_vacuna_turista_duplicado_update
BEFORE UPDATE ON VACUNA_TURISTA
FOR EACH ROW
BEGIN
    IF (NEW.IDTURISTA <> OLD.IDTURISTA OR NEW.IDVACUNA <> OLD.IDVACUNA)
       AND EXISTS (
           SELECT 1 FROM VACUNA_TURISTA
           WHERE IDTURISTA = NEW.IDTURISTA AND IDVACUNA = NEW.IDVACUNA
       )
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Este turista ya tiene registrada esta vacuna';
    END IF;
END//

-- 20) Al cancelar inscripcion -> anular su factura
CREATE TRIGGER trg_anular_factura
AFTER UPDATE ON INSCRIPCION
FOR EACH ROW
BEGIN
    IF NEW.ESTADO = 'Cancelado' AND OLD.ESTADO <> 'Cancelado' THEN
        UPDATE FACTURA
        SET ESTADO = 'Anulada'
        WHERE IDFACTURA IN (
            SELECT IDFACTURA FROM DETALLE_FACTURA WHERE IDINSCRIPCION = NEW.IDINSCRIPCION
        );
    END IF;
END//

-- 21) Al anular factura -> cancelar sus inscripciones
CREATE TRIGGER trg_cancelar_inscripciones
AFTER UPDATE ON FACTURA
FOR EACH ROW
BEGIN
    IF NEW.ESTADO = 'Anulada' AND OLD.ESTADO <> 'Anulada' THEN
        UPDATE INSCRIPCION
        SET ESTADO = 'Cancelado'
        WHERE IDINSCRIPCION IN (
            SELECT IDINSCRIPCION FROM DETALLE_FACTURA WHERE IDFACTURA = NEW.IDFACTURA
        );
    END IF;
END//

-- 22) No agregar destino a paquete con inscripciones activas
CREATE TRIGGER trg_bloquear_destino_paquete_con_inscripciones
BEFORE INSERT ON PAQUETE_DESTINO
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM INSCRIPCION
        WHERE IDPAQUETE = NEW.IDPAQUETE AND LOWER(ESTADO) IN ('pendiente','confirmado')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede agregar un destino: el paquete ya tiene inscripciones activas.';
    END IF;
END//

-- 23) No eliminar precio de clase con inscripciones activas
CREATE TRIGGER trg_bloquear_eliminar_precio_clase
BEFORE DELETE ON PRECIO_PAQUETE_CLASE
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM INSCRIPCION
        WHERE IDPAQUETE = OLD.IDPAQUETE AND IDCLASE = OLD.IDCLASE
          AND LOWER(ESTADO) IN ('pendiente','confirmado')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar: ya hay inscripciones activas con esta clase en el paquete.';
    END IF;
END//

-- 24) Al cambiar precio de clase -> propagar a detalle y recalcular total (si factura no pagada)
CREATE TRIGGER trg_propagar_precio_clase
AFTER UPDATE ON PRECIO_PAQUETE_CLASE
FOR EACH ROW
BEGIN
    IF OLD.PRECIO_PAQUETE <> NEW.PRECIO_PAQUETE THEN
        UPDATE DETALLE_FACTURA
        SET PRECIOUNITARIO = NEW.PRECIO_PAQUETE,
            SUBTOTAL = NEW.PRECIO_PAQUETE
        WHERE IDINSCRIPCION IN (
            SELECT I.IDINSCRIPCION FROM INSCRIPCION I
            WHERE I.IDPAQUETE = NEW.IDPAQUETE AND I.IDCLASE = NEW.IDCLASE
        )
        AND IDFACTURA IN (
            SELECT IDFACTURA FROM FACTURA WHERE LOWER(ESTADO) != 'pagada'
        );

        UPDATE FACTURA F
        SET F.TOTAL = (
            SELECT SUM(SUBTOTAL) FROM DETALLE_FACTURA WHERE IDFACTURA = F.IDFACTURA
        )
        WHERE F.IDFACTURA IN (
            SELECT df.IDFACTURA FROM DETALLE_FACTURA df
            JOIN INSCRIPCION I ON df.IDINSCRIPCION = I.IDINSCRIPCION
            WHERE I.IDPAQUETE = NEW.IDPAQUETE AND I.IDCLASE = NEW.IDCLASE
        )
        AND LOWER(F.ESTADO) != 'pagada';
    END IF;
END//

-- 25) Al marcar paquete Inactivo -> cancelar inscripciones Pendientes
CREATE TRIGGER trg_cancelar_inscripciones_paquete_inactivo
AFTER UPDATE ON PAQUETE_TURISTICO
FOR EACH ROW
BEGIN
    IF OLD.ESTADO != 'Inactivo' AND NEW.ESTADO = 'Inactivo' THEN
        UPDATE INSCRIPCION
        SET ESTADO = 'Cancelado'
        WHERE IDPAQUETE = NEW.IDPAQUETE AND ESTADO = 'Pendiente';
    END IF;
END//

DELIMITER ;