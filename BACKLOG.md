# 📋 BACKLOG - Sistema de Gestión Institucional

Este documento contiene las funcionalidades, mejoras e ideas pendientes del proyecto.

> **Regla:** Ninguna mejora se implementa inmediatamente si no es necesaria para completar la funcionalidad actual. Primero se registra aquí y luego se planifica su desarrollo.

---

# 🔴 Alta prioridad

## Reservas

- [ ] Implementar sub-bloques de 45 minutos.
- [ ] Validar disponibilidad considerando sub-bloques.
- [ ] Mostrar correctamente reservas parciales en el horario.
- [ ] Impedir conflictos entre bloque completo y sub-bloques.

## Entrega de archivos

- [ ] Integración con el NAS.
- [ ] Crear automáticamente la estructura de carpetas.
- [ ] Guardar archivos según:
  - Año
  - Curso
  - Asignatura
  - Actividad
  - Alumno
- [ ] Reemplazar archivo del mismo alumno.
- [ ] Mostrar fecha y hora de entrega.
- [ ] Permitir cierre manual.
- [ ] Permitir cierre automático por fecha.
- [ ] Listado de entregas por actividad.
- [ ] Descargar todos los archivos de una actividad.

---

# 🟡 Prioridad media

## Bitácora

- [ ] Registrar observaciones.
- [ ] Registrar incidentes.
- [ ] Registrar recursos utilizados.
- [ ] Historial de utilización de la sala.

## Usuarios

- [ ] Implementar rol Super Administrador.
- [ ] Administración de roles.
- [ ] Activar / desactivar usuarios.
- [ ] Historial de cambios.

## Reportes

- [ ] Uso por docente.
- [ ] Uso por curso.
- [ ] Uso por asignatura.
- [ ] Uso mensual.
- [ ] Horas utilizadas.

---

# 🔵 Baja prioridad

## Interfaz

- [ ] Mensajes cuando no existan bloques.
- [ ] Botón "Administrar bloques".
- [ ] Mejorar colores del horario.
- [ ] Tooltip con información de la reserva.
- [ ] Mejoras responsive.

## Administración

- [ ] Configuración general del establecimiento.
- [ ] Configuración de bloques.
- [ ] Configuración de horarios.

## Seguridad

- [ ] Registro de acciones (logs).
- [ ] Protección CSRF.
- [ ] Auditoría de accesos.

---

# 📚 Documentación

- [ ] Manual de usuario.
- [ ] Manual técnico.
- [ ] Diccionario de Base de Datos.
- [ ] Estándares de desarrollo.

---

# 🧹 Refactorización

- [ ] Revisión completa del módulo Reservas.
- [ ] Eliminar código duplicado.
- [ ] Homogeneizar nombres de variables.
- [ ] Revisar comentarios.
