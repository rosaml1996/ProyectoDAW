## Norma del proyecto en Git:

* `main`: versión final y estable. No se trabaja aquí.
* `develop`: rama de desarrollo. Aquí se integran las features terminadas.
* `feature/*`: una rama por cada tarea o funcionalidad, creada desde `develop`.
* `release/*`: rama para preparar una versión final antes de pasar a `main`.

Flujo:

1. Actualizar `develop`

```bash
git checkout develop
git pull origin develop
```

2. Crear feature

```bash
git checkout -b feature/nombre-tarea
```

3. Trabajar y guardar cambios

```bash
git add .
git commit -m "Mensaje claro"
git push -u origin feature/nombre-tarea
```

4. Antes del PR, actualizar la feature con develop

```bash
git checkout develop
git pull origin develop
git checkout feature/nombre-tarea
git merge develop
```

5. Crear Pull Request:

* base: `develop`
* compare: `feature/nombre-tarea`

6. Cuando `develop` esté estable, crear release

```bash
git checkout develop
git pull origin develop
git checkout -b release/v1.0
git push -u origin release/v1.0
```

7. La release se fusiona en:

* `main`
* `develop`

Normas:

* no trabajar directamente en `main`
* no trabajar directamente en `develop`
* avisar si dos personas van a tocar el mismo archivo
* revisar siempre base y compare antes de mergear
