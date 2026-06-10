# Servicios-PHP — Etapa 2 (Proyecto 1, PDM115, Grupo 4)

Servicios web (PHP + MySQL) del **Sistema de Tours de Agencias de Viajes**.
Estos servicios son consumidos por la app Android `SistemaToursAV`.

> 📘 **Guía completa paso a paso** (fases, cómo hacer un servicio, cómo hacer la pantalla Android, errores comunes):
> **Notion → "Guía Etapa 2 — Servicios Web (PHP + MySQL) y consumo Android"**
> https://app.notion.com/p/3789c76a787e81e18a87c205c3c79468

---

## Arranque rápido (cada integrante en su máquina)

**1. WAMP en verde** (Apache + MySQL + phpMyAdmin).

**2. Montar la base** en phpMyAdmin, importando los scripts de `sql/` en este orden:
1. `01_creabase_mysql.sql` — crea las 25 tablas
2. `02_llenado_mysql.sql` — inserta los datos de prueba
3. `03_triggers_mysql.sql` — crea los 29 triggers (importar como archivo por el DELIMITER)

La base se llama **`tours_gpo4`**.

**3. Clonar este repo dentro de WAMP:**
```
git clone <url-del-repo> C:\wamp64\www\Servicios-PHP
```
Así `git pull` trae el trabajo de todos y `git push` comparte el tuyo.

**4. Probar un servicio en el navegador:**
```
http://localhost/Servicios-PHP/facturacion/ws_factura_listar.php
```
Si ves JSON, funciona.

---

## Estructura

```
Servicios-PHP/
├── conexion.php          ← conexión compartida (incluir con include '../conexion.php')
├── sql/                  ← los 3 scripts de la base
├── facturacion/          ← Kevin
├── catalogos/            ← Caleb
├── turistas/             ← Adam
├── agencias-paquetes/    ← César
└── inscripciones/        ← Adonis
```

## Convenciones

- **Nombre de servicio:** `ws_<tabla>_<accion>.php` (ej. `ws_turista_listar.php`).
- **Respuesta JSON:**
  - Consultas → arreglo: `[ {...}, {...} ]`
  - Operaciones → objeto: `{"resultado":"1","mensaje":"..."}`
- **Conexión:** todos los servicios hacen `include '../conexion.php';` (está en la raíz).
- **Datos WAMP por defecto:** servidor `localhost`, usuario `root`, contraseña vacía.

## Meta de entrega

Mínimo **10 servicios** en total. Cada quien hace **mínimo 2** de su submódulo.