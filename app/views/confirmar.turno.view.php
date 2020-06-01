<!DOCTYPE html>

<html>
    <head>
        <title>Reserva de Turno</title>
        <link rel="stylesheet" href="public/css/contenido.css">
        <link rel="stylesheet" href="public/css/footer.css">
        <link rel="stylesheet" href="public/css/cabecera.css">
        <link rel="Shortcut Icon" href="img/favicon.ico" type=”image/x-icon” />
    </head>
    <body>
        <main>
        <?php include "app/views/partials/cabecera.view.php"; ?>
        <h1>Reserva de Turno</h1>
        <form action="/turno_confirmado" method = 'POST' enctype="multipart/form-data">
            <table id="turnos">
                <tr>
                    <th>Nombre del Paciente</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Edad</th>
                    <th>Talla del Calzado</th>
                    <th>Altura</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Color de Pelo</th>
                    <th>Fecha del Turno</th>
                    <th>Horario del Turno</th>
                    <th>Receta Cargada</th>
                </tr>
                <?php $turno = $this->datos_reserva;?>
                <tr>
                    <td><?= $turno['nombre_paciente']; ?></td> 
                    <td><?= $turno['email']; ?></td>
                    <td><?= $turno['telefono'];?></td>
                    <td><?= $turno['edad'];?></td>
                    <td><?= $turno['talla_calzado'];?></td>
                    <td><?= $turno['altura'];?></td>
                    <td><?= $turno['fecha_nacimiento'];?></td>
                    <td><?= $turno['color_pelo'];?></td>
                    <td><?= $turno['fecha_turno'];?></td>
                    <td><?= $turno['hora_turno'];?></td> 
                    <td><img class="receta_cargada" src="data:image/<?= $turno['tipo_imagen'] ?>;base64,<?= base64_encode($turno['archivo_imagen']) ?>"></td>
                    
                </tr>                                                        
            </table>
                <input class="input_oculto" type="text" value="<?= $turno['nombre_paciente']; ?>" name="nombre_paciente" >
                <input class="input_oculto" type="text" value="<?= $turno['email']; ?>" name="email" >
                <input class="input_oculto" type="text" value="<?= $turno['telefono']; ?>" name="telefono" >
                <input class="input_oculto" type="text" value="<?= $turno['edad']; ?>" name="edad" >
                <input class="input_oculto" type="text" value="<?= $turno['talla_calzado']; ?>" name="talla_calzado" >
                <input class="input_oculto" type="text" value="<?= $turno['altura']; ?>" name="altura" >
                <input class="input_oculto" type="text" value="<?= $turno['fecha_nacimiento']; ?>" name="fecha_nacimiento" >
                <input class="input_oculto" type="text" value="<?= $turno['color_pelo']; ?>" name="color_pelo" >
                <input class="input_oculto" type="text" value="<?= $turno['fecha_turno']; ?>" name="fecha_turno" >
                <input class="input_oculto" type="text" value="<?= $turno['hora_turno']; ?>" name="hora_turno" >
                <input class="input_oculto" type="hidden" name="archivo_imagen" value="<?= base64_encode($turno['archivo_imagen']); ?>">
                <input class="input_oculto" type="text" value="<?= $turno['tipo_imagen']; ?>" name="tipo_imagen" >
        <?php 
          if (!empty($this->datos_mal_cargados)){ ?>
            <input class="boton" type="submit" name='enviar' value="Confirmar Turno" disabled>
            <input class="boton" type="submit" name='corregir' value="Corregir Turno">
        </form>    

            <ul> Errores Encontrados:
            <?php foreach ($this->datos_mal_cargados as $error):?>
                <li>Error: <?= $error ?></li>
            <?php endforeach; ?>
            </ul>
        <?php }else{ ?>
                <input class="boton" type="submit" name='enviar' value="Confirmar Turno">
            </form>    

           <?php echo "<h1>Datos correctamente cargados</h1>";} ?>
      </main>     
      <?php include "app/views/partials/footer.view.php"; ?>

    </body>
</html>