# PROMPT_CABLE_TRACKER_v2.md
# Claude Code — Cable Ton-Mile Tracker · RIG 158
# Versión: 2.0 | Autor: ARC (Armando Ramírez Cardozo)
# Revisado y mejorado: correcciones de fórmula, CODs, auth, offline y fases

---

## CONTEXTO DEL PROYECTO

Construye una aplicación web PWA llamada **Cable TM Tracker** para el control de
Tonelada-Milla (TM) del cable de perforación del taladro GRS 158 (RIG 158).

Actualmente este control se lleva en un archivo Excel (FGOP-066). El objetivo es
reemplazarlo con una app de campo con soporte offline, alertas automáticas y
registro histórico por cable.

Stack base: **Next.js 15 (App Router) + Supabase + Tailwind CSS + shadcn/ui +
PWA/IndexedDB**. Usa el boilerplate ShipFree de `https://github.com/revokslab/ShipFree`
como punto de partida. Verificar compatibilidad con Next.js 15 + React 19 antes
de usar — omitir partes del boilerplate que entren en conflicto.

---

## DOMINIO DEL NEGOCIO

### ¿Qué es Tonelada-Milla?

La TM es la unidad de fatiga del cable de perforación. Cada operación que mueve
la sarta consume una cantidad de TM calculada como función del peso soportado y
la distancia recorrida. Cuando el acumulado supera el límite máximo configurado
(típicamente 1,200 TM), el cable debe cortarse o reemplazarse.

### Fórmula de cálculo TM

La fórmula replica el Excel FGOP-066. Pasos en orden:

**Paso 1 — Factor de aparejo según N° de líneas activas**

El aparejo multiplica o divide la carga. Tabla de factores:

| Líneas activas | Factor aparejo (Fa) |
|----------------|---------------------|
| 6              | 1 / 6               |
| 8              | 1 / 8               |
| 10             | 1 / 10              |
| 12             | 1 / 12              |

**Paso 2 — Peso efectivo en el cable (Pe)**

Considera flotabilidad del lodo:

```
Factor flotabilidad (Fb) = 1 - (densidad_lodo_ppg / 65.5)

Pe = (peso_dp_lb_ft × (prof_final_ft - long_bha_ft)
     + peso_bha_lb_ft × long_bha_ft
     + peso_bloque_lb) × Fb
```

**Paso 3 — Distancia media recorrida**

```
D_media = (prof_inicial_ft + prof_final_ft) / 2
```

**Paso 4 — TM de la operación**

```
TM_operacion = factor_operacion × Fa × Pe × D_media / 1_000_000
```

> ⚠️ **IMPORTANTE:** El archivo Excel FGOP-066 debe ser suministrado por el
> usuario antes de codificar `tm-calculator.ts`. Validar que los resultados
> de la función coincidan fila a fila con el Excel antes de continuar con
> el formulario. Si el archivo no está disponible, implementar la fórmula
> descrita aquí y marcar las pruebas como **"pendiente de validación con Excel"**.

### Tabla de factores por tipo de operación (COD)

| COD | Nombre operación                        | Factor |
|-----|-----------------------------------------|--------|
| 1   | Bajando / Sacando sarta (POOH / RIH)    | 1.0    |
| 1B  | Short Trip                              | 0.5    |
| 2   | Drilling con Top Drive                  | 1.0    |
| 3   | Drilling + Reaming con Top Drive        | 1.0    |
| 4   | Coring con Top Drive                    | 1.0    |
| 5   | Running Casing                          | 1.0    |
| 6   | Working Casing                          | 1.0    |
| 7   | Jarring Down (Martilleo abajo)          | 1.0    |
| 8   | Jarring Up (Martilleo arriba)           | 1.0    |
| 9   | Pulling on stuck pipe                   | 1.0    |
| 10  | Drilling with Kelly                     | 1.0    |
| 11  | Drilling + Reaming with Kelly           | 1.0    |
| 12  | Coring with Kelly                       | 1.0    |
| 13  | Giro de cable (solo hidráulicos)        | 0.0    |
| 14  | Corrida y corte / reemplazo de cable    | RESET  |

> **COD 1B (Short Trip):** mismo flujo que COD 1 pero factor 0.5.
> En el selector mostrar como `"1B — Short Trip"`.
>
> **COD 13 (Giro de cable):** NO genera TM. Al seleccionarlo, el formulario
> oculta los campos de profundidad y pesos, muestra solo fecha, descripción
> y notas. Se registra en `operaciones` con `tm_operacion = 0`.
>
> **COD 14 (Corte/Reemplazo):** NO suma TM — resetea el contador a 0 e inicia
> un nuevo ciclo de cable. Registrar longitud cortada en ft.

---

## DATOS TÉCNICOS DEL EQUIPO (desde hoja INFO TÉCNICA GRS)

Guardar en tabla `equipos` de Supabase:

| Campo                    | GRS 158     |
|--------------------------|-------------|
| Nombre                   | GRS 158     |
| Altura torre (ft)        | 105         |
| Diámetro tambor (in)     | 18          |
| Diámetro cable (in)      | 1 1/8"      |
| Resistencia cable (lb)   | 113,000     |
| TM máx para corte        | 1,200       |
| Long máx de corte (ft)   | 100         |
| N° líneas activas        | 8 (EIP)     |
| Tipo cable actual        | EIP         |
| Peso bloque viajero (lb) | 25,000      |

---

## MODELO DE DATOS (Supabase)

### Tabla: `equipos`
```sql
id                    uuid PK default gen_random_uuid()
nombre                text           -- "GRS 158"
altura_torre_ft       numeric
diametro_tambor_in    numeric
diametro_cable_in     text
resistencia_lb        numeric
tm_max_corte          numeric        -- 1200
long_max_corte_ft     numeric        -- 100
lineas_activas        integer
tipo_cable            text           -- EIP | EEIP | IPS
peso_bloque_lb        numeric        -- 25000 (default para formulario)
created_at            timestamptz default now()
```

### Tabla: `cables`
```sql
id                    uuid PK default gen_random_uuid()
equipo_id             uuid FK → equipos
serial                text           -- "4332421-24"
referencia            text           -- "6X19S BIPEX AA RL"
fabricante            text           -- "EMCOCABLES"
grado                 text           -- EIP
fecha_instalacion     date
longitud_inicial_ft   numeric
activo                boolean        -- solo uno activo por equipo
created_at            timestamptz default now()

-- Constraint: solo un cable activo por equipo
-- Crear en Supabase como unique partial index:
-- CREATE UNIQUE INDEX cables_equipo_activo_unique
--   ON cables (equipo_id) WHERE activo = true;
```

### Tabla: `operaciones`
```sql
id                    uuid PK default gen_random_uuid()
cable_id              uuid FK → cables
equipo_id             uuid FK → equipos
user_id               uuid FK → auth.users
fecha                 date
densidad_lodo_ppg     numeric
tipo_operacion        text           -- "Bajando sarta", "Perforando C/T Drive", etc.
cod                   text           -- "1", "1B", "2" ... "14"
descripcion           text           -- texto libre del perforador
prof_inicial_ft       numeric
prof_final_ft         numeric
peso_dp_lb_ft         numeric        -- Peso Drill Pipe ajustado
peso_bha_lb_ft        numeric
long_bha_ft           numeric
peso_bloque_lb        numeric
long_parada_ft        numeric        -- longitud de stand / junta sencilla
-- Campos calculados (snapshot al momento de guardar)
tm_operacion          numeric        -- TM de esta operación
tm_acumulado          numeric        -- snapshot acumulado al guardar
                                     -- para acumulado real usar SUM() en query
tm_restante           numeric        -- tm_max - tm_acumulado al guardar
-- Para COD 14 (corte)
ft_cortados           numeric
notas                 text
created_by            text           -- nombre legible del perforador
created_at            timestamptz default now()
updated_at            timestamptz default now()
updated_by            text
```

> **Nota sobre `tm_acumulado`:** El valor guardado es un snapshot. Si se edita
> o elimina una operación, usar la consulta:
> ```sql
> SELECT SUM(tm_operacion) FROM operaciones
> WHERE cable_id = :cable_id AND created_at <= :fecha_operacion
> ```
> para obtener el acumulado real. La UI debe usar esta consulta, no el valor
> en columna.

### Tabla: `cortes_cable`
```sql
id              uuid PK default gen_random_uuid()
cable_id        uuid FK → cables
operacion_id    uuid FK → operaciones
fecha           date
ft_cortados     numeric
tm_al_corte     numeric    -- TM acumulado cuando se cortó
motivo          text
created_at      timestamptz default now()
```

### Tabla: `alertas_config`
```sql
id              uuid PK default gen_random_uuid()
equipo_id       uuid FK → equipos
umbral_pct      numeric    -- 80, 90, 95
notificacion    boolean
created_at      timestamptz default now()
```

---

## ESTRUCTURA DE LA APP

```
app/
├── (auth)/
│   └── login/page.tsx        ← Magic Link login
├── (dashboard)/
│   ├── page.tsx              ← Dashboard principal
│   ├── operaciones/
│   │   ├── page.tsx          ← Lista de operaciones del cable activo
│   │   ├── nueva/page.tsx    ← Formulario nueva operación
│   │   └── [id]/page.tsx     ← Detalle / editar operación
│   ├── cables/
│   │   ├── page.tsx          ← Historial de cables
│   │   └── nuevo/page.tsx    ← Registrar nuevo cable
│   ├── cortes/
│   │   └── page.tsx          ← Registrar corte / reemplazo
│   └── configuracion/
│       └── page.tsx          ← Datos técnicos del equipo, umbrales de alerta
├── api/
│   └── tm/
│       └── calcular/route.ts ← Endpoint de cálculo TM
lib/
├── tm-calculator.ts          ← Lógica pura de cálculo TM
├── supabase/
│   ├── client.ts
│   └── server.ts
├── offline/
│   ├── sync.ts               ← Sincronización IndexedDB → Supabase
│   └── store.ts              ← Store local IndexedDB (idb-keyval)
components/
├── TmGauge.tsx               ← Gauge circular de TM consumido
├── OperacionForm.tsx         ← Formulario inteligente de operación
├── AlertaBanner.tsx          ← Banner de alerta cuando TM > umbral
├── CableStatus.tsx           ← Card de estado del cable activo
└── HistorialTable.tsx        ← Tabla de operaciones con filtros
```

---

## FUNCIONALIDADES REQUERIDAS

### 1. Dashboard principal
- Card grande con estado del cable activo: TM acumulado / TM máximo
- **Gauge circular** (tipo velocímetro) mostrando % consumido del cable
  - Verde: 0–79% | Amarillo: 80–94% | Rojo: ≥95%
- Últimas 5 operaciones registradas
- Banner de alerta visible si TM acumulado supera umbral configurado
- Datos del cable activo: serial, fecha instalación, fabricante
- Botón rápido "+ Registrar operación"

### 2. Formulario de nueva operación (`OperacionForm`)

Campos del formulario (en orden de captura en campo):

```
Fecha*                → date picker (default hoy)
Densidad lodo (ppg)*  → número, ej: 11.5
Tipo de operación*    → select con los 14 tipos + COD
Descripción*          → texto libre ("Saco BHA #3 direccional")
Profundidad inicial*  → número en ft   [oculto si COD 13]
Profundidad final*    → número en ft   [oculto si COD 13]
Peso DP/CSC (lb/ft)*  → número         [oculto si COD 13]
Peso BHA (lb/ft)*     → número         [oculto si COD 13]
Long BHA (ft)*        → número         [oculto si COD 13]
Peso bloque (lb)*     → número, default desde config equipo (25,000) [oculto si COD 13]
Long parada (ft)*     → número         [oculto si COD 13]
```

**Si COD = 13 (Giro de cable):** mostrar solo fecha, descripción y notas.
`tm_operacion = 0`, no afecta el acumulado.

**Si COD = 14 (Corte/Reemplazo):**
- Mostrar campo adicional: `Pies cortados (ft)*`
- Mostrar campo: `Notas del corte`
- Al guardar: crear registro en `cortes_cable`, archivar cable actual
  (`activo = false`), crear nuevo cable con mismo serial base + sufijo `-B`

**Cálculo en tiempo real:** al llenar los campos, mostrar inmediatamente
el TM estimado para esta operación y el nuevo TM acumulado proyectado.

### 3. Cálculo TM (`lib/tm-calculator.ts`)

```typescript
interface OperacionInput {
  codOperacion: string;        // "1", "1B", "2" ... "14"
  profInicialFt: number;
  profFinalFt: number;
  pesoDpLbFt: number;
  pesoBhaLbFt: number;
  longBhaFt: number;
  pesoBloqueLb: number;
  longParadaFt: number;
  densidadLodoPpg: number;
  nLineas: number;             // líneas activas del aparejo
}

// Retorna TM de la operación (0 para COD 13, null para COD 14)
function calcularTM(input: OperacionInput): number

// Retorna factores intermedios para mostrar transparencia del cálculo
function calcularTMDetallado(input: OperacionInput): {
  tm: number;
  pesoEfectivo: number;
  factorFlotabilidad: number;
  distanciaMedia: number;
  factorOperacion: number;
  factorAparejo: number;
}
```

Incluir tests unitarios (`tm-calculator.test.ts`) con al menos 5 casos
de prueba. Marcar como **"pendiente validación con Excel FGOP-066"** hasta
que el usuario confirme que los resultados coinciden fila a fila.

### 4. Soporte offline (PWA)

- `@ducanh2912/next-pwa` para service worker
- IndexedDB via `idb-keyval` para cola de operaciones pendientes
- Indicador visual en la UI: `● En línea` / `○ Sin conexión — X pendientes`
- Al recuperar conexión: sincronizar automáticamente con toast de confirmación
- Toda la pantalla de nueva operación debe funcionar 100% offline

**Estrategia de conflictos al sincronizar:**
1. Orden de sync: por `created_at` local (timestamp del dispositivo)
2. Antes de insertar: verificar si ya existe registro con mismo
   `cable_id + fecha + prof_inicial + prof_final`. Si existe, mostrar
   alerta en lugar de duplicar
3. Recalcular `tm_acumulado` desde Supabase al sincronizar, ignorar
   el valor local
4. Política v1: "primer dispositivo en sincronizar gana"
5. Mostrar toast al final: `"X operaciones sincronizadas"`

### 5. Historial de cables
- Lista de todos los cables instalados en el equipo
- Para cada cable: serial, fechas, TM total acumulado al cierre, N° de cortes
- Al hacer clic: ver todas las operaciones de ese cable

### 6. Export a PDF del turno
- Botón "Exportar turno a PDF" en la pantalla de historial
- Genera una hoja equivalente al formulario FGOP-066 con las operaciones
  del día seleccionado, listos para entregar a supervisión
- Usar `@react-pdf/renderer` o `jspdf` + `html2canvas`

### 7. Sistema de alertas
- Al cargar la app, verificar si TM ≥ umbral (configurable, default 80%)
- Banner fijo en dashboard con color según criticidad
- (Opcional v2) Push notification via Web Push API

---

## DISEÑO UI

### Paleta de colores
```css
--bg:        #090909;    /* fondo principal */
--surface:   #111111;    /* cards */
--border:    #222222;
--amber:     #E8A045;    /* acento primario — ARC brand */
--red:       #FF4D2E;    /* alertas críticas */
--green:     #22C55E;    /* estado OK */
--yellow:    #EAB308;    /* advertencia */
--text:      #F0EDE8;    /* texto principal */
--muted:     #666666;    /* texto secundario */
```

### Tipografía
```
Display / headings:  Bebas Neue (Google Fonts)
Datos numéricos:     DM Mono
Cuerpo / UI:         Inter o Geist
```

### Componentes clave
- **TmGauge:** gauge semicircular estilo industrial con aguja o arco de progreso.
  Valores en DM Mono. Colores dinámicos según % consumido.
- **OperacionForm:** formulario vertical con secciones colapsables, inputs
  oscuros con bordes amber en focus, preview de TM calculado en tiempo real
  con animación de contador. Mobile-first: inputs grandes, `inputMode="decimal"`
  en todos los campos numéricos, botón guardar siempre visible (sticky bottom).
- **AlertaBanner:** barra full-width con fondo rojo/amarillo según umbral,
  ícono de advertencia, texto claro y botón "Ver detalle".
- Cards con bordes sutiles `#222`, sin sombras, estética industrial/terminal.

---

## REQUISITOS TÉCNICOS

```
Next.js           15.x (App Router)
React             19.x
TypeScript        strict
Supabase          @supabase/ssr + @supabase/supabase-js
Tailwind CSS      3.x
shadcn/ui         base components
idb-keyval        IndexedDB store offline
@ducanh2912/next-pwa  service worker
date-fns          manejo de fechas
zod               validación de schemas
react-hook-form   gestión de formularios
@react-pdf/renderer   export a PDF
recharts          gráficas de TM por fecha (dashboard)
vitest            tests unitarios para tm-calculator
```

### Variables de entorno requeridas
```env
NEXT_PUBLIC_SUPABASE_URL=
NEXT_PUBLIC_SUPABASE_ANON_KEY=
SUPABASE_SERVICE_ROLE_KEY=
```

---

## PUNTOS DE ATENCIÓN PARA IMPLEMENTACIÓN

1. **Validar fórmula contra Excel FGOP-066 antes de continuar.**
   Codificar `tm-calculator.ts` primero. El usuario debe confirmar que los
   resultados coinciden fila a fila con el archivo Excel original.
   Si el Excel no está disponible al inicio, implementar la fórmula de este
   documento y agregar un banner `⚠️ Fórmula pendiente de validación` en
   el dashboard hasta confirmar.

2. **El campo "Tipo de operación"** usa un select que muestra nombre + código
   (ej: `"1 — Bajando / Sacando sarta (POOH/RIH)"`). Al seleccionar COD 13,
   ocultar campos de cálculo. Al seleccionar COD 14, mostrar campos de corte
   y ocultar campos de perforación.

3. **Defaults inteligentes:**
   - Si existe registro previo del cable activo: pre-llenar densidad_lodo,
     peso_dp, peso_bha, long_bha, peso_bloque con valores de la última
     operación. prof_inicial = prof_final del registro anterior.
   - Si NO existe registro previo (primer uso del cable): pre-llenar
     peso_bloque desde `equipos.peso_bloque_lb` (25,000 lb) y dejar los
     demás campos vacíos.

4. **RLS Supabase:** habilitar Row Level Security.
   Policy base: `auth.uid() = user_id` para INSERT/UPDATE/DELETE.
   Para SELECT: el usuario solo ve registros de su `equipo_id`.

5. **Mobile first:** pantalla de nueva operación usable con una mano en campo.
   Inputs grandes (min 44px height), `inputMode="decimal"` en todos los
   campos numéricos, botón guardar sticky en la parte inferior.

6. **Autenticación en v1 — Supabase Magic Link (sin contraseña):**
   - El perforador ingresa su email → recibe link de 1 clic → accede
   - Supabase Auth maneja la sesión automáticamente
   - En `operaciones` guardar `user_id` (UUID de auth) y `created_by`
     (nombre legible)
   - Admin crea los usuarios desde Supabase Dashboard con sus emails
   - No implementar pantalla de registro — solo login con magic link

---

## FLUJO DE USO TÍPICO (campo)

```
1. Perforador abre la app en su tablet/celular (puede ser offline)
2. Primera vez: ingresa su email → recibe magic link → accede
   Siguientes veces: sesión activa automáticamente
3. Ve el dashboard: cable activo con TM acumulado y gauge de estado
4. Toca "+ Registrar operación"
5. Llena: fecha, densidad de lodo, tipo de operación (COD), descripción,
   profundidades, pesos
   → Si COD 13: solo fecha + descripción + notas
   → Si COD 14: agrega pies cortados + notas de corte
6. La app muestra en tiempo real:
   "Esta operación = X.XX TM → Acumulado: Y.YY / 1200 (ZZ%)"
7. Confirma → se guarda en IndexedDB local + se intenta sync a Supabase
8. Si TM acumulado supera 80% → banner de alerta visible
9. Cuando se ordena corte → COD 14 → registra ft cortados
   → app archiva el cable y abre nuevo ciclo desde 0
```

---

## ENTREGABLES — 3 FASES

### ✅ FASE 1 — Base + Cálculo
*(Implementar primero. No continuar a Fase 2 sin validar la fórmula.)*

- [ ] Proyecto Next.js 15 + Supabase conectado + variables de entorno
- [ ] Migraciones SQL para las 5 tablas con constraints, índices y RLS
- [ ] Magic Link auth funcional (login + sesión persistente)
- [ ] `tm-calculator.ts` con la fórmula completa
- [ ] Tests unitarios con `vitest` (mínimo 5 casos)
- [ ] **STOP → Usuario valida resultados contra Excel FGOP-066**

### ✅ FASE 2 — UI principal
- [ ] Dashboard con TmGauge y CableStatus
- [ ] Formulario nueva operación con cálculo en tiempo real
- [ ] Manejo COD 13 (sin campos de cálculo) y COD 14 (reset + nuevo cable)
- [ ] Historial de operaciones con tabla paginada
- [ ] Historial de cables
- [ ] Banner de alertas según umbrales

### ✅ FASE 3 — Offline + Deploy
- [ ] Soporte offline: IndexedDB + service worker + sync con detección de conflictos
- [ ] Indicador online/offline en UI con contador de pendientes
- [ ] Export turno a PDF (equivalente al formulario FGOP-066)
- [ ] Deploy en Vercel + Supabase cloud

### Out of scope v1
- Push notifications
- Multi-equipo (multi-rig)
- Export a Excel / importación del Excel histórico
- Gestión de roles de usuarios
- Pantalla de registro de nuevos usuarios (admin usa Supabase Dashboard)

---

*Generado por ARC · Full Stack · By Design*
*Basado en análisis del formulario FGOP-066 V0 · RIG 158*
*v2.0 — Revisado: fórmula TM completa, CODs corregidos, auth Magic Link,*
*estrategia offline, fases de entrega, modelo de datos con constraints y auditoría*
