<!DOCTYPE html>
<html>
    <head>
        <title>Formulario de Datos del Paciente</title>
        <link rel="stylesheet" href="public/css/contenido.css">
        <link rel="stylesheet" href="public/css/footer.css">
        <link rel="stylesheet" href="public/css/cabecera.css">
        <link rel="Shortcut Icon" href="img/favicon.ico" type=”image/x-icon” />

      </head>
<body class = "contenido"> 
   <main>
   <?php include "app/views/partials/cabecera.view.php"; ?>
     <header id="nombre_paciente">
         <h1>Turno del Paciente: <?= $this->lista_datos[0]['valor']?></h1>
     </header>
      <section class="contenedor_principal">
       <article class="formulario_cargado">

        <form action="/save_formulario" method = 'POST' enctype="multipart/form-data" class="form_ingreso">
        <!-- <section class="datos_del_paciente"> -->
          <fieldset class="datos_del_paciente">
          <legend>Datos del Paciente</legend>

            <?php foreach($this->lista_datos as $id_campo => $campo):?>  
            
            <label for  ="<?= $campo['nombre_campo']?>"><?= $campo['nombre_campo']?></label>
            <?php if($campo['tipo'] == 'date'){ ?>  <!-- FECHA NACIMIENTO o FECHA TURNO-->
              <input type="date" name='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' id='<?= $campo['nombre_campo']?>' <?= $campo['obligatorio']?>>
            <?php }else if($campo['tipo'] == 'email'){ ?>  <!-- EMAIL -->
              <input type="text" name='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' id='<?= $campo['nombre_campo']?>' pattern = <?= $campo['restriccion']?> placeholder = "ej: pepe@servidor.com" <?= $campo['obligatorio']?>>
             <?php }else if($campo['tipo'] == 'tel'){ ?>    <!-- TELEFONO -->
            
              <label for="phone">Ingrese el Nro de Telefono:</label>
              <input type="tel" id="<?= $campo['nombre_campo']?>" name="<?= $campo['nombre_campo']?>" value = '<?= $campo['valor']?>' placeholder="11-3438-7233" pattern="<?= $campo['restriccion']?>" <?= $campo['obligatorio']?>>
              

            <?php }else if($campo['tipo'] == 'altura'){   // ALTURA
              list($min,$max) = explode("-",$campo['restriccion'])
              ?>  
              <input type="range" name='<?= $campo['nombre_campo']?>' id='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' placeholder="Entre {<?= $min?>} y {<?= $max?>}" min="100" max="280" <?= $campo['obligatorio']?>>
            <?php }else if($campo['tipo'] == 'calzado' || $campo['tipo'] == 'edad' ){  // CALZADO O EDAD 
              list($min, $max) = explode("-",$campo['restriccion']);
              ?>  
              <input type="number" name='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' id='<?= $campo['nombre_campo']?>' min="<?= $min?>" max="<?= $max?>" <?= $campo['obligatorio']?>>
            <?php }else if($campo['tipo'] == 'pelo'){ ?>  <!-- PELO -->
              <select name="<?= $campo['nombre_campo']?>">
               <?php $pelo = explode("-",$campo['restriccion']); 
                       foreach ($pelo as $color):?>
                          <option value="<?= $color?>"><?= $color?></option>
                <?php  endforeach  ?>        
              </select>   
            <?php }else{ ?>
              <input type="text" name='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' id='<?= $campo['nombre_campo']?>' <?= $campo['obligatorio']?>>
            <?php } ?>                
            
            <?php  endforeach?>
            </fieldset>
            <!-- </section> -->
            <!-- <section class="datos_del_turno"> -->
            <fieldset class="datos_del_turno">
            <legend>Datos del Turno</legend>
              <?php foreach($this->lista_datos_del_turno as $id_campo => $campo):?>  
               <?php if($campo['tipo'] == 'horario_turno'){ ?>  <!-- HORARIO TURNO -->
                <label for="hora_turno"><?= $campo['nombre_campo']?></label>
              <select name="<?= $campo['nombre_campo']?>">
               <?php 
                  list($desde, $hasta, $intervalo) = explode("-", $campo['restriccion']);
                  for ($hora = $desde; $hora < $hasta; $hora++) {
                    for($min = 0; $min < 60; $min = $min + $intervalo) { ?>
                        <option value="<?= $hora?>:<?= $min?>"><?= $hora?>:<?= $min?></option>
                <?php  } } ?>        
                <option value="<?= $hora?>:<?= $min?>"><?= $hora?>:00</option>       
                <option value="<?= $campo['valor']?>" selected><?= $campo['valor']?></option>       
                </select>   

             <?php }?>         
             <?php if($campo['tipo'] == 'date'){ ?>  <!-- FECHA DEL TURNO-->
              <label for="fecha_turno"><?= $campo['nombre_campo']?></label>
              <input type="date" name='<?= $campo['nombre_campo']?>' value = '<?= $campo['valor']?>' id='<?= $campo['nombre_campo']?>' <?= $campo['obligatorio']?>>
              <?php }?>                
            <?php  endforeach?>
              
              <br>Imagen de la Receta<br>  
              <input type="file" name="imagen_receta" id="imagen_receta">  
              </fieldset> 
            <!-- </section>   -->
              <?php if($this->id_turno_update <> 0){?>

                <input class="boton" type="submit" name='enviar' value="Enviar">
            <input class="boton" type="reset" name='limpiar' value="Limpiar">
                  </form>
                  
                </article>
                   <section  class = "receta_cargada">  
                   <!-- <fieldset> -->
                     <legend>Receta cargada en el Sistema</legend>
                     <img src='<?php echo("{$this->imgController->cargarImagen()}"); ?>' alt="receta_cargada" class="receta_cargada">                    
                   <!-- </fieldset>                  -->
                      
                   </section>
                   
                   
                  </section>  
    
              <?php }else{?>
                  <!-- <input type="submit" name='corregir_turno' value= "corregir" class="btn_corregir"> -->
                  <input class="boton" type="submit" name='enviar' value="Enviar">
                  <input class="boton" type="reset" name='limpiar' value="Limpiar">

                </form>
                </article>

                </section>
              <?php }?> 
              <?php include "app/views/partials/footer.view.php"; ?>

    </main>
</body>
</html>