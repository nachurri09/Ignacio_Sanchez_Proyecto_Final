<?php /* Campos comunes. Variables: $v (valores), $conRol (bool), $passObligatoria (bool), $confirmar (bool) */ ?>
<div class="fila">
    <label>Nombre
        <input type="text" name="nombre" maxlength="60" required value="<?= e($v['nombre'] ?? '') ?>">
    </label>
    <label>Apellido
        <input type="text" name="apellido" maxlength="60" required value="<?= e($v['apellido'] ?? '') ?>">
    </label>
</div>
<div class="fila">
    <label>DNI
        <input type="text" name="dni" inputmode="numeric" pattern="\d{7,8}" maxlength="8" required value="<?= e($v['dni'] ?? '') ?>">
    </label>
    <label>Correo electrónico
        <input type="email" name="email" maxlength="120" required value="<?= e($v['email'] ?? '') ?>">
    </label>
</div>
<div class="fila">
    <label><?= $passObligatoria ? 'Contraseña' : 'Nueva contraseña (opcional)' ?>
        <input type="password" name="password" minlength="8" autocomplete="new-password" <?= $passObligatoria ? 'required' : '' ?>>
    </label>
    <?php if (!empty($confirmar)): ?>
    <label>Repetir contraseña
        <input type="password" name="password2" minlength="8" autocomplete="new-password" required>
    </label>
    <?php endif; ?>
    <?php if (!empty($conRol)): ?>
    <label>Rol
        <select name="rol">
            <option value="usuario" <?= ($v['rol'] ?? 'usuario') === 'usuario' ? 'selected' : '' ?>>Comprador</option>
            <option value="admin"   <?= ($v['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </label>
    <?php endif; ?>
</div>
