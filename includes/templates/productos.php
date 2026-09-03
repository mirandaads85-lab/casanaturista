<?php while($producto = mysqli_fetch_assoc($resultado)): ?>

    <?php $tieneOferta = !empty($producto['precio']) && $producto['precio'] > 0 && $producto['precio'] < $producto['precio_original']; ?>
    <?php $tieneAlgunPrecio = !empty($producto['precio_original']) && $producto['precio_original'] > 0; ?>

    <div class="producto">

        <div class="producto-imagen">
             <!-- IMAGEN -->
            <img src="/imagenes/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">

            <?php if($tieneOferta): ?>
                <?php
                    $descuentoCard = 100 - (($producto['precio'] / $producto['precio_original']) * 100);
                ?>
                <span class="badge-oferta-card">-<?php echo round($descuentoCard); ?>%</span>
            <?php endif; ?>
        </div>


        <div class="contenido-producto">

            <!-- NOMBRE -->
            <h3><?php echo $producto['nombre']; ?></h3>

            <!-- DESCRIPCIÓN -->
            <p class="descripcion">
                <?php echo $producto['descripcion']; ?>
            </p>

            <!-- 🔥 PRECIO -->
            <div class="precio-card">

                <?php if($tieneOferta): ?>

                    <span class="pantes">Antes S/.</span>
                    <span class="precio-original">
                        <?php echo $producto['precio_original']; ?>
                    </span>

                    <span class="precio">
                        Oferta S/.<?php echo $producto['precio']; ?>
                    </span>

                <?php elseif($tieneAlgunPrecio): ?>

                    <span class="precio">
                        S/.<?php echo $producto['precio_original']; ?>
                    </span>

                <?php else: ?>

                    <span class="precio-consultar">
                        Consultar precio
                    </span>

                <?php endif; ?>

            </div>

            <!-- BOTONES -->
            <div class="acciones-producto">

                <a href="producto.php?id=<?php echo $producto['id']; ?>" class="botonp">
                    Ver Producto
                </a>

                <!-- 🔥 FORM AJAX -->
                <form class="form-agregar">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button type="submit" class="btn-comprar-card">Comprar</button>
                </form>

            </div>

        </div>

    </div>

<?php endwhile; ?>