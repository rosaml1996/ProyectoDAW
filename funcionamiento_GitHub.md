# Normas del proyecto en Git

* `main`: versión final y estable. No se trabaja aquí.
* `develop`: rama de desarrollo. Aquí se integran las features terminadas.
* `feature/*`: una rama por cada tarea o funcionalidad, creada desde `develop`.
* `release/*`: rama para preparar una versión final antes de pasar a `main`.

## Flujo

### 1. Actualizar `develop`

```bash
git checkout develop
git pull origin develop
```

### 2. Crear una nueva feature desde `develop`

```bash
git checkout -b feature/nombre-tarea
```

### 3. Trabajar en la feature y guardar cambios

```bash
git add .
git commit -m "Mensaje claro"
git push -u origin feature/nombre-tarea
```

Si la rama ya estaba subida antes, después bastará con:

```bash
git push
```

### 4. Antes del Pull Request, actualizar la feature con `develop`

```bash
git checkout develop
git pull origin develop
git checkout feature/nombre-tarea
git pull origin feature/nombre-tarea
git merge develop
git push
```

Esto sirve para que la rama `feature` reciba los últimos cambios de `develop` antes de fusionarse.
Si hay conflictos, hay que resolverlos antes de seguir.

### 5. Crear Pull Request en GitHub

Cuando la feature esté terminada:

1. Entrar en el repositorio de GitHub
2. Ir a la pestaña **Pull requests**
3. Pulsar en **New pull request**
4. Elegir:

   * **base:** `develop`
   * **compare:** `feature/nombre-tarea`
5. Pulsar en **Create pull request**
6. Revisar la pestaña **Files changed**
7. Si todo está bien, pulsar en **Merge pull request**
8. Confirmar el merge
9. Borrar la rama si ya no se va a seguir usando

```bash
git branch -d feature/nombre-tarea
git push origin --delete feature/nombre-tarea
````

### 6. Después del merge, actualizar `develop` en local

```bash
git checkout develop
git pull origin develop
```

### 7. Cuando `develop` esté estable, crear una release

```bash
git checkout develop
git pull origin develop
git checkout -b release/v1.0
git push -u origin release/v1.0
```

### 8. La release se fusiona en:

* `main`
* `develop`

Primero se hace Pull Request de `release/v1.0` a `main`, y después otro de `release/v1.0` a `develop`.

## Normas

* no trabajar directamente en `main`
* no trabajar directamente en `develop`
* toda tarea nueva debe hacerse en una rama `feature/*`
* avisar si dos personas van a tocar el mismo archivo
* revisar siempre `base` y `compare` antes de mergear
* revisar siempre **Files changed** antes de confirmar un Pull Request
* si una rama ya se ha fusionado y no se va a seguir usando, se elimina
