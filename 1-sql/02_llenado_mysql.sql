-- ============================================================================
-- Llenado inicial MySQL - datos idénticos a llenarDatosPrueba() del BDHelperGpo4
-- Requiere: ejecutar primero 01_creabase_mysql.sql
-- ============================================================================
USE tours_gpo4;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO AGENCIA_VIAJE (IDAGENCIA, NOMBREAGENCIA, DIRECCION, TELEFONO, CORREO) VALUES
('AG01','Viajes El Salvador Tours','Alameda Roosevelt 234, San Salvador','2222-1111','info@esaltours.com'),
('AG02','Centroamerica Viajes S.A.','5a Av. Norte 10, Santa Ana','2233-4455','ventas@centroviajes.com'),
('AG03','Oriente Travel Express','1a Calle Poniente 88, San Miguel','2644-7788','contacto@orientetravel.com');

INSERT INTO CLASE_PASAJERO (IDCLASE, NOMBRECLASE) VALUES
('C01','Turista'),('C02','Ejecutiva'),('C03','Primera Clase');

INSERT INTO PAIS (IDPAIS, NOMBREPAIS) VALUES
('SLV','El Salvador'),('GTM','Guatemala'),('HND','Honduras'),
('CRI','Costa Rica'),('MEX','México'),('ESP','España'),('USA','Estados Unidos');

INSERT INTO MOTIVACIONES (IDMOTIVACION, DESMOTIVACION) VALUES
('M01','Aventura y naturaleza'),('M02','Cultura e historia'),('M03','Descanso y relax'),
('M04','Gastronomia'),('M05','Turismo de playa');

INSERT INTO ENFERMEDAD (IDENFERMEDAD, NOMBREENFERMEDAD) VALUES
('E01','Fiebre Amarilla'),('E02','Hepatitis A'),('E03','Tifoidea'),
('E04','COVID-19'),('E05','Sarampion');

INSERT INTO TIPO_DOCUMENTO (IDTIPODOC, DESTIPODOC, LONGITUDMIN, LONGITUDMAX, REGEXVALIDACION, FORMATOEJEMPLO, MENSAJEERROR, ACTIVO) VALUES
('TD01','DUI',10,10,'^\\d{8}-\\d$','00000000-0','El DUI debe tener el formato 00000000-0',1),
('TD02','Pasaporte',6,12,'^[A-Z0-9]{6,12}$','A12345678','El pasaporte debe tener entre 6 y 12 caracteres alfanuméricos',1),
('TD03','Carnet de minoridad',6,15,'^[A-Z0-9-]{6,15}$','MIN-000001','El carnet debe contener letras, números o guiones',1),
('TD04','Carnet de residente',6,15,'^[A-Z0-9-]{6,15}$','RES-123456','El documento de residencia debe contener letras, números o guiones',1);

INSERT INTO TIPO_AGRUPACION (IDTIPOAGRUP, DESTIPOAGRUP) VALUES
('TA01','Individual'),('TA02','Pareja'),('TA03','Familia'),('TA04','Grupo corporativo');

INSERT INTO FORMA_PAGO (IDFORMAPAGO, DESFORMAPAGO) VALUES
('FP01','Efectivo'),('FP02','Tarjeta de credito'),('FP03','Transferencia bancaria'),('FP04','Cheque');

INSERT INTO VACUNA (IDVACUNA, IDENFERMEDAD, NOMBREVACUNA) VALUES
('V01','E01','Vacuna Fiebre Amarilla YF-VAX'),('V02','E02','Vacuna Hepatitis A Havrix'),
('V03','E03','Vacuna Tifoidea Vivotif'),('V04','E04','Vacuna COVID-19 Pfizer BioNTech'),
('V05','E05','Vacuna Triple Viral MMR');

INSERT INTO DESTINO (IDDESTINO, IDPAIS, NOMBREDESTINO, DESDESTINO, REQUIEREVISAESP) VALUES
('D01','GTM','Tikal, Guatemala','Antigua ciudad maya rodeada de selva tropical.',0),
('D02','CRI','San José, Costa Rica','Capital con museos, gastronomía y cultura tica.',0),
('D03','MEX','Cancún, México','Playas de arena blanca y zona arqueológica maya.',0),
('D04','ESP','Barcelona, España','Ciudad cosmopolita, arquitectura de Gaudí y playas.',1),
('D05','USA','Nueva York, EE.UU.','La Gran Manzana: Times Square, Central Park.',1),
('D06','HND','Copán Ruinas, Honduras','Sitio arqueológico maya Patrimonio de la Humanidad.',0),
('D07','SLV','Ruta de las Flores, El Salvador','Pueblos coloniales, fincas de café y cascadas.',0);

INSERT INTO DESTINO_MOTIVACION (IDMOTIVACION, IDDESTINO) VALUES
('M02','D01'),('M01','D01'),('M03','D02'),('M04','D02'),('M05','D03'),('M03','D03'),
('M02','D04'),('M04','D04'),('M02','D05'),('M03','D05'),('M02','D06'),('M01','D07'),('M04','D07');

INSERT INTO REQUISITO_VACUNA_DESTINO (IDDESTINO, IDVACUNA) VALUES
('D01','V01'),('D02','V02'),('D03','V04'),('D04','V04'),('D05','V04');

INSERT INTO PAQUETE_TURISTICO (IDPAQUETE, IDAGENCIA, NOMBREPAQUETE, DESPAQUETE, FECHAINICIO, FECHAFIN, CUPOMAXIMO, ESTADO) VALUES
('PQ01','AG01','Maravillas Mayas','Recorrido por sitios arqueologicos de Centroamerica.','2026-07-01','2026-07-10',20,'Activo'),
('PQ02','AG02','Caribe y Naturaleza','Playas caribenas y parques naturales de Costa Rica.','2026-08-15','2026-08-25',15,'Activo'),
('PQ03','AG01','Europa Express','Principales capitales europeas en 15 dias.','2026-09-01','2026-09-15',25,'Activo'),
('PQ04','AG03','Ruta Centroamericana','Recorrido por los paises centroamericanos.','2026-10-01','2026-10-14',30,'Activo'),
('PQ05','AG02','Paraiso Salvadoreno','Los mejores destinos turisticos de El Salvador.','2026-06-01','2026-06-07',12,'Activo');

INSERT INTO PAQUETE_DESTINO (IDPAQUETE, IDDESTINO) VALUES
('PQ01','D01'),('PQ01','D06'),('PQ02','D02'),('PQ02','D03'),('PQ03','D04'),('PQ03','D05'),
('PQ04','D01'),('PQ04','D06'),('PQ04','D07'),('PQ05','D07');

INSERT INTO PAQUETE_MOTIVACION (IDMOTIVACION, IDPAQUETE) VALUES
('M02','PQ01'),('M01','PQ01'),('M05','PQ02'),('M01','PQ02'),('M02','PQ03'),('M03','PQ03'),
('M02','PQ04'),('M01','PQ04'),('M01','PQ05'),('M04','PQ05');

INSERT INTO PRECIO_PAQUETE_CLASE (IDPAQUETE, IDCLASE, PRECIO_PAQUETE) VALUES
('PQ01','C01',850.00),('PQ01','C02',1200.00),('PQ01','C03',1800.00),
('PQ02','C01',1100.00),('PQ02','C02',1600.00),('PQ02','C03',2400.00),
('PQ03','C01',2500.00),('PQ03','C02',3800.00),('PQ03','C03',5500.00),
('PQ04','C01',950.00),('PQ04','C02',1400.00),('PQ04','C03',2100.00),
('PQ05','C01',350.00),('PQ05','C02',500.00),('PQ05','C03',750.00);

INSERT INTO TURISTA (IDTURISTA, IDPAIS, NOMBRES, APELLIDOS, FECHANACIMIENTO, TELEFONO, CORREO, TIENEVISA) VALUES
('T01','SLV','Carlos Antonio','Orantes Mejía','1990-03-15','7701-1234','carlos@mail.com',0),
('T02','SLV','María José','González López','1995-07-22','7702-5678','maria@mail.com',1),
('T03','GTM','Pedro Rafael','Ortiz Ramos','1988-11-08','7703-9012','pedro@mail.com',0),
('T04','SLV','Sara Beatriz','Hernández Cruz','2000-01-30','7704-3456','sara@mail.com',1),
('T05','HND','Luis Fernando','Martínez Pérez','1985-05-19','7705-7890','luis@mail.com',0);

INSERT INTO DOCUMENTO_TURISTA (IDTURISTA, IDTIPODOC, NUM_DOC) VALUES
('T01','TD01','01234567-8'),('T02','TD02','A12345678'),('T03','TD02','B98765432'),
('T04','TD01','09876543-2'),('T05','TD02','C55544433');

INSERT INTO VACUNA_TURISTA (IDTURISTA, IDVACUNA, FECHAAPLICACION, FECHAVIGENCIA) VALUES
('T01','V01','2025-06-01','2030-06-01'),('T01','V02','2023-09-01','2028-09-01'),
('T01','V04','2024-03-15','2030-03-15'),('T02','V01','2025-01-10','2030-01-10'),
('T02','V04','2024-03-15','2027-03-15'),('T03','V02','2025-05-01','2030-05-01'),
('T03','V04','2025-05-01','2030-05-01'),('T04','V04','2024-06-20','2027-06-20');

INSERT INTO INSCRIPCION (IDINSCRIPCION, IDPAQUETE, IDCLASE, IDTURISTA, FECHAINSCRIPCION, ESTADO) VALUES
('I01','PQ01','C01','T01','2026-07-05','Confirmado'),('I02','PQ02','C02','T01','2026-08-20','Confirmado'),
('I03','PQ03','C01','T02','2026-09-03','Confirmado'),('I04','PQ05','C01','T03','2026-06-03','Confirmado'),
('I05','PQ05','C03','T04','2026-06-04','Confirmado'),('I06','PQ05','C02','T02','2026-06-05','Confirmado'),
('I07','PQ02','C01','T03','2026-08-18','Confirmado'),('I08','PQ05','C01','T05','2026-06-07','pendiente');

INSERT INTO FACTURA (IDFACTURA, IDTIPOAGRUP, IDFORMAPAGO, FECHAEMISION, NOMBREGRUPO, TOTAL, ESTADO) VALUES
('F01','TA01','FP01','2026-04-12','Carlos Orantes',850.00,'Pagada'),
('F02','TA02','FP02','2026-04-13','Maria Gonzalez',1200.00,'Pagada'),
('F03','TA01','FP03','2026-05-02','Pedro Ortiz',1100.00,'Pendiente'),
('F04','TA03','FP01','2026-05-05','Sara Hernandez',350.00,'Pagada'),
('F05','TA01','FP02','2026-05-06','Luis Martinez',750.00,'Pendiente');

INSERT INTO DETALLE_FACTURA (IDFACTURA, IDINSCRIPCION, PRECIOUNITARIO, SUBTOTAL) VALUES
('F01','I01',850.00,850.00),('F02','I02',1200.00,1200.00),('F03','I03',1100.00,1100.00),
('F04','I04',350.00,350.00),('F05','I05',750.00,750.00);

-- Usuarios: clave SHA-256 (admin/admin123, agencia/agencia123)
INSERT INTO USUARIO (IDUSUARIO, NOMUSUARIO, CLAVE, ROL, ACTIVO) VALUES
('01','admin','240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9','ADMIN',1),
('02','agencia','db9816308c143bc67b6b10f368d4a1caa0c812ee44ff94e25820310ef21cadba','AGENCIA',1);

INSERT INTO OPCION_CRUD (IDOPCION, DESOPCION, NUMCRUD) VALUES
('010','Menu de Agencia',0),('011','Adicion de Agencia',1),('012','Modificacion de Agencia',2),('013','Actualizacion de Agencia',3),('014','Consulta de Agencia',4),
('020','Menu de Pais',0),('021','Adicion de Pais',1),('022','Modificacion de Pais',2),('023','Actualizacion de Pais',3),('024','Consulta de Pais',4),
('030','Menu de Motivacion',0),('031','Adicion de Motivacion',1),('032','Modificacion de Motivacion',2),('033','Actualizacion de Motivacion',3),('034','Consulta de Motivacion',4),
('040','Menu de Destino',0),('041','Adicion de Destino',1),('042','Modificacion de Destino',2),('043','Actualizacion de Destino',3),('044','Consulta de Destino',4),
('050','Menu de Clase Pasajero',0),('051','Adicion de Clase Pasajero',1),('052','Modificacion de Clase Pasajero',2),('053','Actualizacion de Clase Pasajero',3),('054','Consulta de Clase Pasajero',4),
('060','Menu de Tipo Documento',0),('061','Adicion de Tipo Documento',1),('062','Modificacion de Tipo Documento',2),('063','Actualizacion de Tipo Documento',3),('064','Consulta de Tipo Documento',4),
('070','Menu de Tipo Agrupacion',0),('071','Adicion de Tipo Agrupacion',1),('072','Modificacion de Tipo Agrupacion',2),('073','Actualizacion de Tipo Agrupacion',3),('074','Consulta de Tipo Agrupacion',4),
('080','Menu de Forma Pago',0),('081','Adicion de Forma Pago',1),('082','Modificacion de Forma Pago',2),('083','Actualizacion de Forma Pago',3),('084','Consulta de Forma Pago',4),
('090','Menu de Enfermedad',0),('091','Adicion de Enfermedad',1),('092','Modificacion de Enfermedad',2),('093','Actualizacion de Enfermedad',3),('094','Consulta de Enfermedad',4),
('100','Menu de Vacuna',0),('101','Adicion de Vacuna',1),('102','Modificacion de Vacuna',2),('103','Actualizacion de Vacuna',3),('104','Consulta de Vacuna',4),
('110','Menu de Turista',0),('111','Adicion de Turista',1),('112','Modificacion de Turista',2),('113','Actualizacion de Turista',3),('114','Consulta de Turista',4),
('120','Menu de Paquete',0),('121','Adicion de Paquete',1),('122','Modificacion de Paquete',2),('123','Actualizacion de Paquete',3),('124','Consulta de Paquete',4),
('130','Menu de Inscripcion',0),('131','Adicion de Inscripcion',1),('132','Modificacion de Inscripcion',2),('133','Actualizacion de Inscripcion',3),('134','Consulta de Inscripcion',4),
('140','Menu de Factura',0),('141','Adicion de Factura',1),('142','Modificacion de Factura',2),('143','Actualizacion de Factura',3),('144','Consulta de Factura',4),
('150','Menu de Detalle Factura',0),('151','Adicion de Detalle Factura',1),('152','Modificacion de Detalle Factura',2),('153','Actualizacion de Detalle Factura',3),('154','Consulta de Detalle Factura',4);

-- Admin (01): acceso total. Agencia (02): solo Menu(0), Adicion(1), Consulta(4).
INSERT INTO ACCESO_USUARIO (IDUSUARIO, IDOPCION) VALUES
('01','010'),('01','011'),('01','012'),('01','013'),('01','014'),('01','020'),('01','021'),('01','022'),('01','023'),('01','024'),
('01','030'),('01','031'),('01','032'),('01','033'),('01','034'),('01','040'),('01','041'),('01','042'),('01','043'),('01','044'),
('01','050'),('01','051'),('01','052'),('01','053'),('01','054'),('01','060'),('01','061'),('01','062'),('01','063'),('01','064'),
('01','070'),('01','071'),('01','072'),('01','073'),('01','074'),('01','080'),('01','081'),('01','082'),('01','083'),('01','084'),
('01','090'),('01','091'),('01','092'),('01','093'),('01','094'),('01','100'),('01','101'),('01','102'),('01','103'),('01','104'),
('01','110'),('01','111'),('01','112'),('01','113'),('01','114'),('01','120'),('01','121'),('01','122'),('01','123'),('01','124'),
('01','130'),('01','131'),('01','132'),('01','133'),('01','134'),('01','140'),('01','141'),('01','142'),('01','143'),('01','144'),
('01','150'),('01','151'),('01','152'),('01','153'),('01','154'),
('02','010'),('02','011'),('02','014'),('02','020'),('02','021'),('02','024'),('02','030'),('02','031'),('02','034'),
('02','040'),('02','041'),('02','044'),('02','050'),('02','051'),('02','054'),('02','060'),('02','061'),('02','064'),
('02','070'),('02','071'),('02','074'),('02','080'),('02','081'),('02','084'),('02','090'),('02','091'),('02','094'),
('02','100'),('02','101'),('02','104'),('02','110'),('02','111'),('02','114'),('02','120'),('02','121'),('02','124'),
('02','130'),('02','131'),('02','134'),('02','140'),('02','141'),('02','144'),('02','150'),('02','151'),('02','154');
-- ============================================================================
-- Paquete extra agregado para pruebas y defensa (PQ06)
-- ============================================================================
INSERT INTO PAQUETE_TURISTICO (IDPAQUETE, IDAGENCIA, NOMBREPAQUETE, DESPAQUETE, FECHAINICIO, FECHAFIN, CUPOMAXIMO, ESTADO) 
VALUES ('PQ06', 'AG01', 'Aventura en Cancún y NY', 'Un tour emocionante combinando playas del Caribe y rascacielos.', '2026-11-01', '2026-11-12', 20, 'Activo');

INSERT INTO PAQUETE_DESTINO (IDPAQUETE, IDDESTINO) VALUES 
('PQ06', 'D03'),
('PQ06', 'D05');

INSERT INTO PAQUETE_MOTIVACION (IDMOTIVACION, IDPAQUETE) VALUES 
('M01', 'PQ06'),
('M05', 'PQ06');

INSERT INTO PRECIO_PAQUETE_CLASE (IDPAQUETE, IDCLASE, PRECIO_PAQUETE) VALUES 
('PQ06', 'C01', 1200.00),
('PQ06', 'C02', 1800.00),
('PQ06', 'C03', 2600.00);

-- =====================================
-- ENFERMEDADES (Adicionales)
-- =====================================

INSERT INTO ENFERMEDAD
(IDENFERMEDAD, NOMBREENFERMEDAD)
VALUES
('E06','Rabia'), ('E07','Fiebre Tifoidea'), ('E08','Meningitis Meningocócica'), 
('E09','Poliomielitis'), ('E10','Encefalitis Japonesa'), ('E11','Cólera'),
('E12','Varicela'), ('E13','Influenza Estacional'), ('E14','Virus del Papiloma Humano'),
('E15','Difteria');

-- =====================================
-- VACUNAS (Adicionales)
-- =====================================

INSERT INTO VACUNA
(IDVACUNA, IDENFERMEDAD, NOMBREVACUNA)
VALUES
('V06','E06','Vacuna Verorab'), ('V07','E07','Vacuna Typhim Vi'), ('V08','E08','Vacuna Menactra'),
('V09','E09','Vacuna IPOL'), ('V10','E10','Vacuna Ixiaro'), ('V11','E11','Vacuna Dukoral'),
('V12','E12','Vacuna Varivax'), ('V13','E13','Vacuna Fluarix'),('V14','E14','Vacuna Gardasil 9'),
('V15','E15','Vacuna Daptacel');

SET FOREIGN_KEY_CHECKS = 1;