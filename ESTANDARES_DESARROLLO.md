# 📖 Guía oficial de desarrollo del Sistema de Gestión Institucional

Este documento define las reglas, estándares y buenas prácticas para el desarrollo del Sistema de Gestión Institucional.

Su objetivo es mantener un código consistente, reutilizable, fácil de mantener y escalable.

---

# 1. Metodología de desarrollo

Cada archivo debe seguir el siguiente ciclo:

1. Objetivo.
2. Desarrollo funcional.
3. Pruebas.
4. Refinamiento.
5. Estilos.
6. Validaciones.
7. Limpieza.
8. Archivo finalizado.
9. Commit.

**No comenzar un nuevo archivo hasta finalizar completamente el actual.**

---

# 2. Organización del proyecto

```
assets/
config/
includes/
modules/
uploads/
```

Los recursos compartidos deben mantenerse separados de los módulos.

---

# 3. Principios de desarrollo

## Reutilización

Antes de crear código nuevo, verificar si existe una solución reutilizable.

Siempre que sea posible:

- reutilizar funciones;
- reutilizar componentes;
- reutilizar estilos;
- reutilizar estructuras HTML.

Evitar duplicar código.

---

## Agrupación

Agrupar elementos comunes antes de crear elementos específicos.

### Correcto

```css
.grupo-formulario input,
.grupo-formulario select,
.grupo-formulario textarea {
}
```

### Incorrecto

```css
.grupo-formulario input {
}

.grupo-formulario select {
}

.grupo-formulario textarea {
}
```

Crear reglas específicas únicamente cuando un elemento requiera un comportamiento o apariencia diferente.

---

## Consistencia

Las soluciones similares deben implementarse de forma similar en todo el proyecto.

Mantener una estructura uniforme facilita el mantenimiento, la lectura y la evolución del sistema.

---

# 4. Archivos CSS

## Sección y Subsección

Los archivos CSS deben mantener una jerarquía uniforme de comentarios.

Sección principal:

/_=====================================================
VARIABLES
=====================================================_/

Subseccion:

/_-----------------------------------------------------
COLORES INSTITUCIONALES
-----------------------------------------------------_/

```css
/*=====================================================
  VARIABLES
=====================================================*/
:root {
  /*-----------------------------------------------------
  COLORES INSTITUCIONALES
-----------------------------------------------------*/

  --color-primario: #6b1d44;
  --color-primario-hover: #5b1739;
  --color-primario-original: #4e0227;

  /*-----------------------------------------------------
  FONDOS
-----------------------------------------------------*/

  --color-fondo: #ffffff;
  --color-fondo-secundario: #f8f2f5;
}
```

Cada archivo CSS tiene una responsabilidad específica.

## estilos.css

Estilos generales del sistema.

## formularios.css

Estilos reutilizables para todos los formularios del sistema.

  ### Espaciado de formularios

  Los formularios reutilizables deben mantener un margen vertical moderado.

  El valor estándar será:

  ```css
  .contenedor-formulario {
    ...
    margin: 12px auto;
    ...
  }
  ```
  El espacio entre grupos de controles será:

  ```css
  .contenedor-formulario form {
      ...
      gap: 15px;
      ...
  }
  ```
Los controles deben priorizar la comodidad de uso. No se reducirá el padding para solucionar problemas de layout; si aparece scroll, primero se revisarán los márgenes y el espaciado general.

## botones.css

Estilos reutilizables para todos los botones del sistema.

## tablas.css

Estilos reutilizables para todas las tablas del sistema.

## reservas.css

Estilos exclusivos del módulo Reservas.

### Orden de carga

Los archivos CSS deben cargarse en el siguiente orden:

1. `estilos.css`
2. Archivos reutilizables (`formularios.css`, `botones.css`, `tablas.css`, etc.).
3. Archivos específicos de cada módulo (`reservas.css`, `usuarios.css`, etc.).

`estilos.css` debe cargarse siempre primero, ya que define las variables y estilos base utilizados por el resto del sistema.


**No agregar estilos genéricos dentro de archivos específicos de un módulo.**

---

# 5. Variables

Las variables deben describir claramente su contenido.

### Correcto

```php
$resultadoDocentes
$resultadoCursos
$stmtBloque
$stmtReserva
$cursoId
$fechaReserva
```

### Incorrecto

```php
$resultado
$stmt
$tmp
$datos
```

Las variables deben mantener un criterio uniforme en todo el proyecto.

Variables de alcance corto como `$fila` pueden mantenerse cuando su uso sea evidente.

Evitar reutilizar variables genéricas cuando representen entidades distintas.

---

# 6. SQL

Las consultas deben escribirse de forma uniforme.

```sql
SELECT
    id,
    nombre
FROM Docentes
ORDER BY nombre;
```

Utilizar siempre `prepare()` cuando existan parámetros.

Mantener una indentación uniforme en todas las consultas SQL.

---

# 7. HTML

Todos los controles deben tener:

- `id`
- `name`
- `label` asociado mediante `for`
- `autocomplete="off"` cuando corresponda.

Agrupar los controles utilizando:

```html
<div class="grupo-formulario"></div>
```

Checkbox:

```html
<div class="grupo-checkbox"></div>
```

Mantener una estructura HTML consistente en todos los formularios.

---

# 8. PHP

- Utilizar `require_once`.
- Utilizar `htmlspecialchars()` al mostrar datos.
- Validar todas las entradas recibidas.
- Evitar código duplicado.
- Utilizar comparaciones estrictas (`===`) cuando sea posible.
- Mantener una estructura uniforme en todos los archivos PHP.

---

# 9. Base de datos

- Utilizar claves foráneas.
- Mantener nombres consistentes.
- No duplicar información.
- Preferir tablas normalizadas.
- Utilizar tipos de datos adecuados para cada campo.

---

# 10. Git

Realizar un commit por cada funcionalidad terminada.

### Correcto

```text
Implementa formulario de reservas
Refactoriza agregar.php del módulo Reservas
Agrega validaciones de usuarios
```

### Evitar

```text
Cambios
Update
Prueba
asdf
```

Los mensajes deben describir claramente el trabajo realizado.

---

# 11. Backlog

Toda mejora que no sea necesaria para completar la funcionalidad actual debe registrarse en `BACKLOG.md`.

Los errores funcionales deben corregirse inmediatamente y no enviarse al Backlog.

---

# 12. Refactorización

Al finalizar un módulo completo se realizará una revisión general para:

- eliminar código repetido;
- mejorar nombres de variables;
- optimizar consultas SQL;
- revisar comentarios;
- mejorar estilos;
- verificar consistencia con esta guía.

---

# 13. Principios del proyecto

- Cada archivo debe tener un único objetivo claramente definido.
- Finalizar completamente un módulo antes de comenzar otro.
- Priorizar la claridad sobre la complejidad.
- El código debe priorizar la legibilidad.
- Mantener consistencia en todo el sistema.
- Favorecer la reutilización antes que la duplicación.

El sistema debe reflejar el funcionamiento real del establecimiento.

Las decisiones técnicas deben responder a necesidades reales de los usuarios y no únicamente a criterios de programación.

Cada funcionalidad debe tener un propósito claro dentro del flujo de trabajo del establecimiento.
