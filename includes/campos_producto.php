<?php /* Variables: $v (valores), $imagenActual (opcional) */ ?>
<label>Nombre
    <input type="text" name="nombre" maxlength="100" required value="<?= e($v['nombre'] ?? '') ?>">
</label>
<div class="fila">
    <label>Precio ($)
        <input type="number" name="precio" min="1" required value="<?= e($v['precio'] ?? '') ?>">
    </label>
    <label>Stock (unidades)
        <input type="number" name="stock" min="0" max="9999" required value="<?= e($v['stock'] ?? '') ?>">
    </label>
</div>
<label>Descripción
    <textarea name="descripcion" maxlength="1000"><?= e($v['descripcion'] ?? '') ?></textarea>
</label>
<label>Imagen (JPG, PNG o WEBP, máximo 2 MB)
    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
</label>
<?php if (!empty($imagenActual)): ?>
    <p class="nota">Imagen actual: <?= img_producto($imagenActual, 'Imagen actual', 'img-mini') ?> (si no elegís otra, se mantiene)</p>
<?php endif; ?>
