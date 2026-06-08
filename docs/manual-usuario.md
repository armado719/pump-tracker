# Manual de Usuario — Pump Tracker GRS
**General Rigs Services S.A.S.**
Versión 1.0 — Junio 2026

---

## Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Dashboard Principal](#3-dashboard-principal)
4. [Gestión de Equipos (Rigs)](#4-gestión-de-equipos-rigs)
5. [Gestión de Bombas](#5-gestión-de-bombas)
6. [Registro Diario de Operaciones](#6-registro-diario-de-operaciones)
7. [Reemplazo de Componentes](#7-reemplazo-de-componentes)
8. [Centro de Alertas](#8-centro-de-alertas)
9. [Reportes PDF](#9-reportes-pdf)
10. [Cable TM](#10-cable-tm)
    - [Dashboard Cable TM](#101-dashboard-cable-tm)
    - [Nueva Operación TM](#102-nueva-operación-tm)
    - [Historial de Cables](#103-historial-de-cables)
    - [Configuración del Equipo](#104-configuración-del-equipo)
11. [Panel Gerencial](#11-panel-gerencial)
12. [Administración](#12-administración)
    - [Gestión de Usuarios](#121-gestión-de-usuarios)
    - [Gestión de Pozos](#122-gestión-de-pozos)
    - [Configuración de Correo SMTP](#123-configuración-de-correo-smtp)
    - [Auditoría del Sistema](#124-auditoría-del-sistema)
13. [Perfil y Contraseña](#13-perfil-y-contraseña)

---

## 1. Introducción

**Pump Tracker GRS** es una aplicación web de seguimiento operacional desarrollada para General Rigs Services S.A.S. Permite registrar y monitorear en tiempo real el estado de los equipos de perforación (rigs), las bombas de lodo, los componentes críticos, el cable de perforación TM y las horas de operación diarias.

### Roles del sistema

| Rol | Permisos |
|-----|----------|
| **Administrador** | Acceso total: crear, editar, eliminar registros y gestionar usuarios |
| **Operador** | Consulta y registro de operaciones diarias; no puede crear ni eliminar equipos |

---

## 2. Acceso al Sistema

### 2.1 Pantalla de Login

La pantalla de inicio de sesión es el punto de entrada al sistema. El usuario debe ingresar su correo electrónico y contraseña registrados.

---

**[IMAGEN 1 — Pantalla de Login]**
> *Insertar captura de pantalla de la pantalla de inicio de sesión completa, mostrando el logo GRS, los campos de correo y contraseña, y el botón "Iniciar sesión".*

---

**Campos:**
- **Correo electrónico:** dirección de correo asociada a la cuenta.
- **Contraseña:** contraseña asignada por el administrador.
- **Recordarme:** mantiene la sesión activa en el navegador.

**Nota:** Si olvidó su contraseña, use el enlace *"¿Olvidaste tu contraseña?"* para recibir un correo de recuperación.

---

## 3. Dashboard Principal

El Dashboard es la pantalla principal del sistema. Muestra un resumen general del estado operacional de todos los equipos en tiempo real.

---

**[IMAGEN 2 — Dashboard Principal]**
> *Insertar captura de pantalla del dashboard completo mostrando las tarjetas de resumen (Rigs Activos, Bombas Operando, Alertas Críticas, Horas Prom.), la tabla Estado de Rigs, las Alertas Recientes y el gráfico de Horas de Trabajo.*

---

### Tarjetas de resumen

| Tarjeta | Descripción |
|---------|-------------|
| **Rigs Activos** | Número total de equipos registrados en el sistema |
| **Bombas Operando Hoy** | Bombas con registro de horas en el día actual |
| **Alertas Críticas** | Componentes en estado crítico que requieren atención inmediata |
| **Horas Prom. (7D)** | Promedio de horas trabajadas por las bombas en los últimos 7 días |
| **Cables TM — Alertas** | Cables de perforación que superaron el umbral de alerta configurado |

### Estado de Rigs

Tabla que muestra por cada equipo: el pozo asignado, número de bombas, horas trabajadas hoy, fecha de última actualización y estado (Activo / Inactivo).

### Alertas Recientes

Lista de los componentes con mayor acumulación de horas que han superado el umbral de advertencia o crítico.

### Estado Cable TM por Equipo

Tabla con el progreso de ton-millas acumuladas de cada cable versus el máximo configurado para corte.

### Gráfico de Horas de Trabajo

Gráfico de líneas con las horas diarias trabajadas por los rigs en los últimos 30 días.

---

## 4. Gestión de Equipos (Rigs)

### 4.1 Lista de Equipos

Muestra todos los equipos de perforación registrados con su información básica.

---

**[IMAGEN 3 — Lista de Rigs]**
> *Insertar captura de la tabla de equipos mostrando nombre del rig, ubicación y acciones disponibles.*

---

### 4.2 Crear Equipo *(solo Administrador)*

Formulario para registrar un nuevo equipo de perforación en el sistema.

---

**[IMAGEN 4 — Formulario Crear Rig]**
> *Insertar captura del formulario de creación de rig con los campos: Nombre del equipo y demás datos requeridos.*

---

**Campos requeridos:**
- **Nombre del equipo:** identificador único del rig (ej. RIG158).

### 4.3 Detalle del Equipo

Vista detallada de un equipo específico. Muestra las bombas asociadas, los pozos asignados y el historial de operaciones.

---

**[IMAGEN 5 — Detalle del Rig]**
> *Insertar captura de la página de detalle de un rig mostrando sus bombas asociadas, pozos y opciones de administración.*

---

### 4.4 Editar Equipo *(solo Administrador)*

Permite modificar la información de un equipo existente.

---

**[IMAGEN 6 — Editar Rig]**
> *Insertar captura del formulario de edición de rig.*

---

---

## 5. Gestión de Bombas

### 5.1 Lista de Bombas

Muestra todas las bombas registradas con su equipo asociado, número de bomba y estado de componentes.

---

**[IMAGEN 7 — Lista de Bombas]**
> *Insertar captura de la tabla de bombas con columnas de rig, número de bomba y estado general.*

---

### 5.2 Crear Bomba *(solo Administrador)*

Formulario para registrar una nueva bomba y asociarla a un equipo.

---

**[IMAGEN 8 — Formulario Crear Bomba]**
> *Insertar captura del formulario de creación de bomba mostrando campos de equipo, número y tipo de bomba.*

---

### 5.3 Detalle de Bomba

Vista completa de una bomba. Muestra los ensambles (izquierdo, derecho, superior) con sus componentes, las horas acumuladas de cada pieza y el estado (OK / Alerta / Crítico).

---

**[IMAGEN 9 — Detalle de Bomba con Componentes]**
> *Insertar captura de la página de detalle de bomba mostrando los tres ensambles con sus componentes, barras de progreso de horas y badges de estado.*

---

**Estados de componentes:**

| Estado | Color | Descripción |
|--------|-------|-------------|
| **OK** | Verde | Horas acumuladas dentro del rango seguro |
| **Alerta** | Amarillo/Naranja | Próximo al límite recomendado |
| **Crítico** | Rojo | Superó el límite; requiere reemplazo inmediato |

### 5.4 Historial de Reemplazos

Registro histórico de todos los reemplazos de componentes realizados en la bomba.

---

**[IMAGEN 10 — Historial de Reemplazos]**
> *Insertar captura del historial de reemplazos mostrando fecha, componente reemplazado y horas al momento del reemplazo.*

---

### 5.5 Personal de Bomba *(solo Administrador)*

Asignación del personal operativo responsable de cada bomba.

---

## 6. Registro Diario de Operaciones

### 6.1 Lista de Registros

Historial de registros diarios de una bomba ordenados por fecha descendente.

---

**[IMAGEN 11 — Lista de Registros Diarios]**
> *Insertar captura de la tabla de registros diarios con columnas de fecha, pozo, horas trabajadas y observaciones.*

---

### 6.2 Crear Registro Diario

Formulario para registrar las horas de operación diarias de una bomba.

---

**[IMAGEN 12 — Formulario Registro Diario]**
> *Insertar captura del formulario de registro diario mostrando campos de fecha, pozo, horas trabajadas y observaciones.*

---

**Campos:**
- **Fecha:** día del registro (por defecto: hoy).
- **Pozo:** pozo en el que operó la bomba ese día.
- **Horas trabajadas:** horas de operación efectiva.
- **Observaciones:** notas adicionales del operador.

### 6.3 Detalle del Registro

Vista del registro diario individual con toda la información ingresada.

---

**[IMAGEN 13 — Detalle del Registro Diario]**
> *Insertar captura de la vista de detalle de un registro diario.*

---

---

## 7. Reemplazo de Componentes

Cuando un componente llega a estado crítico, el operador puede registrar su reemplazo desde la página de detalle de la bomba.

---

**[IMAGEN 14 — Formulario de Reemplazo de Componente]**
> *Insertar captura del formulario de reemplazo mostrando el componente a reemplazar, las horas actuales y el campo para confirmar el reemplazo.*

---

Al registrar un reemplazo:
- Las horas acumuladas del componente se reinician a cero.
- Queda registrado en el historial de reemplazos con la fecha y las horas al momento del cambio.

---

## 8. Centro de Alertas

Pantalla centralizada que muestra todos los componentes y cables TM que requieren atención.

---

**[IMAGEN 15 — Centro de Alertas]**
> *Insertar captura del centro de alertas mostrando las tarjetas de resumen (Bombas Críticas, Bombas en Alerta, Cables TM Críticos, Cables TM en Alerta) y las tablas de alertas detalladas.*

---

### Secciones

| Sección | Descripción |
|---------|-------------|
| **Alertas de Bombas — Componentes** | Lista de componentes en estado alerta o crítico con rig, bomba, ensamble y horas acumuladas |
| **Alertas de Cable TM** | Cables que superaron el porcentaje de alerta configurado |

### Notificación por correo

El botón **"Notificar por email"** envía un resumen de todas las alertas activas al correo configurado en el sistema.

---

**[IMAGEN 16 — Botón Notificar y Confirmación de Envío]**
> *Insertar captura del botón de notificación y del mensaje de confirmación tras el envío exitoso.*

---

---

## 9. Reportes PDF

### 9.1 Panel de Reportes

Permite generar reportes en formato PDF del estado de los equipos y del cable TM.

---

**[IMAGEN 17 — Panel de Reportes]**
> *Insertar captura del panel de reportes mostrando las opciones disponibles: reporte de bombas y reporte de cable TM.*

---

### Tipos de reporte

| Reporte | Contenido |
|---------|-----------|
| **Reporte de Bombas** | Estado de todos los componentes de todas las bombas de un rig, con horas acumuladas y estado |
| **Reporte Cable TM** | Resumen de ton-millas acumuladas, operaciones registradas e historial del cable activo |

---

**[IMAGEN 18 — Ejemplo de Reporte PDF generado]**
> *Insertar captura del PDF generado abierto en el navegador, mostrando la portada y la tabla de componentes.*

---

---

## 10. Cable TM

Módulo especializado para el seguimiento del cable de perforación mediante el cálculo de Ton-Millas (TM) acumuladas.

### 10.1 Dashboard Cable TM

Panel principal del módulo Cable TM. Muestra el estado del cable activo, el progreso de ton-millas y las últimas operaciones registradas.

---

**[IMAGEN 19 — Dashboard Cable TM sin cable activo]**
> *Insertar captura del dashboard cuando no hay cable registrado, mostrando el formulario de registro de nuevo cable.*

---

**[IMAGEN 20 — Dashboard Cable TM con cable activo]**
> *Insertar captura del dashboard con un cable activo mostrando: barra de progreso TM, datos del cable (tipo, diámetro, grado, fecha instalación) y tabla de últimas operaciones.*

---

**Información del cable activo:**
- Tipo y especificación del cable
- Diámetro en pulgadas
- Grado (EIP / EEIP / IPS)
- Fecha de instalación
- Longitud inicial
- TM acumuladas vs. TM máximo para corte
- Porcentaje de progreso con indicador visual de color

### 10.2 Nueva Operación TM

Registro de una operación de perforación que suma ton-millas al cable activo.

---

**[IMAGEN 21 — Formulario Nueva Operación TM]**
> *Insertar captura del formulario de nueva operación mostrando campos de tipo de operación, peso del bloque, número de líneas, profundidad y distancia recorrida.*

---

**Campos:**
- **Tipo de operación:** tipo de maniobra realizada (ej. perforación, viaje, etc.)
- **Peso del bloque (lb):** carga sobre el gancho
- **Número de líneas activas:** líneas en el sistema de poleas
- **Profundidad (ft):** profundidad del pozo
- **Distancia recorrida (ft):** longitud de cable movida en la operación

El sistema calcula automáticamente las ton-millas de la operación y las suma al acumulado del cable.

### 10.3 Historial de Cables

Registro completo de todos los cables instalados y reemplazados en el equipo, incluyendo cables ya retirados.

---

**[IMAGEN 22 — Historial de Cables]**
> *Insertar captura del historial mostrando la tabla de cables con fechas de instalación, TM acumuladas y estado (Activo / Retirado).*

---

### 10.4 Configuración del Equipo

Parámetros técnicos del rig que se usan para el cálculo de ton-millas y las alertas del cable.

---

**[IMAGEN 23 — Formulario de Configuración Cable TM]**
> *Insertar captura del formulario de configuración mostrando todos los campos: altura torre, diámetro tambor con selector de unidad, líneas activas, TM máx. para corte, tipo de cable, peso de bloque default y umbral de alerta.*

---

**Parámetros configurables:**

| Campo | Descripción | Default |
|-------|-------------|---------|
| Altura Torre (ft) | Altura de la torre de perforación | 105 ft |
| Diámetro Tambor | Diámetro del tambor del malacate (in / cm / ft) | 18 in |
| N° Líneas Activas | Número de líneas en el sistema de poleas | 8 |
| TM Máx. para Corte | Ton-millas máximas antes de reemplazar el cable | 1200 |
| Tipo de Cable | Clasificación del cable (EIP / EEIP / IPS) | EIP |
| Peso Bloque Default (lb) | Peso del bloque de viaje por defecto | 25,000 lb |
| Umbral de Alerta (%) | Porcentaje del TM máximo que activa la alerta | 80% |

---

## 11. Panel Gerencial

Vista ejecutiva con indicadores clave de desempeño (KPIs) consolidados de todos los equipos. Diseñada para gerencia y supervisión de alto nivel.

---

**[IMAGEN 24 — Panel Gerencial]**
> *Insertar captura del panel gerencial mostrando gráficas de producción, resumen de alertas por rig y métricas de eficiencia operacional.*

---

---

## 12. Administración

Las siguientes secciones son exclusivas para usuarios con rol **Administrador**.

### 12.1 Gestión de Usuarios

Permite crear, editar, activar/desactivar y eliminar usuarios del sistema.

---

**[IMAGEN 25 — Lista de Usuarios]**
> *Insertar captura de la tabla de usuarios mostrando nombre, correo, rol, estado (Activo/Inactivo) y botones de acción.*

---

**[IMAGEN 26 — Formulario Crear/Editar Usuario]**
> *Insertar captura del formulario de usuario mostrando campos de nombre, correo, rol (admin/operador) y contraseña.*

---

**Acciones disponibles:**
- **Crear usuario:** registrar nuevo acceso al sistema.
- **Editar:** modificar nombre, correo o rol.
- **Activar / Desactivar:** controlar el acceso sin eliminar el historial del usuario.
- **Eliminar:** remover permanentemente el usuario.

### 12.2 Gestión de Pozos

Administración centralizada del catálogo de pozos disponibles para asignar a los registros diarios.

---

**[IMAGEN 27 — Lista de Pozos]**
> *Insertar captura de la tabla de pozos con nombre y acciones de editar/eliminar.*

---

**[IMAGEN 28 — Formulario Crear/Editar Pozo]**
> *Insertar captura del formulario de pozo mostrando el campo de nombre.*

---

### 12.3 Configuración de Correo SMTP

Configura el servidor de correo que usa el sistema para enviar notificaciones de alertas.

---

**[IMAGEN 29 — Configuración SMTP]**
> *Insertar captura del formulario SMTP mostrando campos de servidor, puerto, usuario y opciones de encriptación.*

---

**Campos:**
- **Host SMTP:** servidor de correo saliente (ej. smtp.gmail.com)
- **Puerto:** puerto del servidor (ej. 587 para TLS)
- **Usuario:** dirección de correo remitente
- **Encriptación:** TLS o SSL
- **Nombre remitente:** nombre que aparece en los correos enviados
- **Correo destino de alertas:** dirección que recibe las notificaciones

El botón **"Enviar correo de prueba"** verifica que la configuración sea correcta.

### 12.4 Auditoría del Sistema

Registro detallado de todas las acciones realizadas por los usuarios: creaciones, ediciones y eliminaciones de registros.

---

**[IMAGEN 30 — Log de Auditoría]**
> *Insertar captura del log de auditoría mostrando columnas de fecha, usuario, acción, modelo afectado e identificador del registro.*

---

---

## 13. Perfil y Contraseña

Cada usuario puede actualizar su propia información de perfil y cambiar su contraseña desde el menú inferior del sidebar.

---

**[IMAGEN 31 — Perfil de Usuario]**
> *Insertar captura de la página de perfil mostrando el formulario de actualización de nombre y correo, y el formulario de cambio de contraseña.*

---

**Opciones disponibles:**
- **Actualizar información:** cambiar nombre y dirección de correo.
- **Cambiar contraseña:** requiere ingresar la contraseña actual para confirmar.
- **Eliminar cuenta:** opción permanente que requiere confirmación con contraseña.

---

## Glosario

| Término | Definición |
|---------|-----------|
| **Rig** | Equipo de perforación terrestre |
| **Bomba de lodo** | Bomba de alta presión usada en la circulación del fluido de perforación |
| **Ensamble** | Conjunto de componentes de una bomba (izquierdo, derecho, superior) |
| **TM / Ton-Milla** | Unidad de medida de desgaste del cable de perforación |
| **Umbral** | Límite de horas/TM a partir del cual se genera una alerta |
| **Malacate** | Equipo de izaje que enrolla el cable de perforación |
| **EIP / EEIP / IPS** | Clasificaciones de resistencia del cable de perforación |

---

*Documento generado para uso interno de General Rigs Services S.A.S.*
*Pump Tracker GRS — Todos los derechos reservados © 2026*
