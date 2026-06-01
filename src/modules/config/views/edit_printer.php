 <?php $config = Help::configPrinter($_GET['id']); ?>
 <div class="section-wrapper">
     <div class="align-content clearfix">
         <div class="float-left">
             <h1><i class="fas fa-receipt"></i> Configuración tickets</h1>
         </div>
     </div>

     <p class="title-info">Configura los datos de impresión para los tickets.</p>
 </div>

 <div class="generalContainer-medium">
     <form action="" method="POST" id="formUpdatePrinter" enctype="multipart/form-data">

         <div class="container row">
             <div class="form-group col-md-8">

                 <!-- ================= IMPRESORA ================= -->
                 <h5 class="mb-2">Impresora</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-5 text-right">
                         Nombre Impresora <span class="text-danger">*</span>
                     </label>

                     <select class="form-custom col-sm-12 ml-3 mb-3"
                         id="impresoraSelect"
                         name="printer_name"
                         required>
                         <option value="<?= $config['printer_name'] ?? '' ?>" selected>
                             <?= $config['printer_name'] ?? '' ?>
                         </option>
                     </select>
                     <input type="hidden" name="printer_id" value="<?= $config['id'] ?>">
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-5 text-right">
                         Usuario <span class="text-danger">*</span>
                     </label>

                     <select class="form-custom search col-sm-6 ml-3" name="user_id" required>
                         <?php
                            $users = Help::loadUsers();
                            while ($element = $users->fetch_assoc()):

                                $nombreCompleto = trim(
                                    ($element['nombre'] ?? '') . ' ' .
                                        ($element['apellidos'] ?? '')
                                );
                            ?>
                             <option value="<?= $element['usuario_id'] ?>"
                                 <?= ($config['usuario_id'] ?? '') == $element['usuario_id'] ? 'selected' : '' ?>>
                                 <?= ucwords($nombreCompleto) ?>
                             </option>
                         <?php endwhile; ?>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-5 text-right">
                         Tipo de Impresora <span class="text-danger">*</span>
                     </label>

                     <select class="form-custom search col-sm-4 ml-3" name="printer_type" required>
                         <option value="main" <?= ($config['printer_type'] ?? '') == 'main' ? 'selected' : '' ?>>Principal</option>
                         <option value="kitchen" <?= ($config['printer_type'] ?? '') == 'kitchen' ? 'selected' : '' ?>>Cocina</option>
                         <option value="warehouse" <?= ($config['printer_type'] ?? '') == 'warehouse' ? 'selected' : '' ?>>Almacén</option>
                     </select>
                 </div>

                 <!-- Lenguaje -->
                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Lenguaje<span class="text-danger">*</span></label>
                     <select class="form-custom search col-sm-4 ml-3" name="printer_language" required>
                         <option value="ESCPOS" <?= ($config['printer_language'] ?? '') == 'ESCPOS' ? 'selected' : '' ?>>ESC / POS</option>
                         <option value="ZPL" <?= ($config['printer_language'] ?? '') == 'ZPL' ? 'selected' : '' ?>>ZPL</option>
                         <option value="TSPL" <?= ($config['printer_language'] ?? '') == 'TSPL' ? 'selected' : '' ?>>TSPL</option>
                         <option value="EPL" <?= ($config['printer_language'] ?? '') == 'EPL' ? 'selected' : '' ?>>EPL</option>
                     </select>
                 </div>

                 <!-- Método -->
                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Método</label>
                     <select class="form-custom search col-sm-4 ml-3" name="print_method">
                         <option value="RAW" <?= ($config['print_method'] ?? '') == 'RAW' ? 'selected' : '' ?>>RAW</option>
                         <option value="HTML" <?= ($config['print_method'] ?? '') == 'HTML' ? 'selected' : '' ?>>HTML</option>
                         <option value="IMAGE" <?= ($config['print_method'] ?? '') == 'IMAGE' ? 'selected' : '' ?>>IMAGE</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Escala logo</label>
                     <select class="form-custom search col-sm-4 ml-3" name="logo_density">
                         <option value="single"
                             <?= ($config['logo_density'] ?? '') == 'single' ? 'selected' : '' ?>>
                             Normal
                         </option>

                         <option value="double"
                             <?= ($config['logo_density'] ?? '') == 'double' ? 'selected' : '' ?>>
                             Alta calidad
                         </option>
                     </select>
                 </div>

                 <hr>

                 <!-- ================= PAPEL ================= -->
                 <h5 class="mb-2">Configuración de papel</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Papel (mm)<span class="text-danger">*</span></label>
                     <input class="form-custom col-sm-4 ml-3"
                         type="number"
                         value="<?= $config['paper_width'] ?? 80 ?>"
                         name="paper_width"
                         required>
                 </div>
                 <hr>

                 <!-- ================= OPCIONES ================= -->
                 <h5 class="mb-2">Opciones de impresión</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Copias<span class="text-danger">*</span></label>
                     <input class="form-custom col-sm-4 ml-3"
                         type="number"
                         name="copies"
                         value="<?= $config['copies'] ?? 1 ?>"
                         min="1"
                         max="99"
                         required>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Auto corte</label>
                     <select class="form-custom search col-sm-4 ml-3" name="auto_cut">
                         <option value="1" <?= ($config['auto_cut'] ?? '') == 1 ? 'selected' : '' ?>>Sí</option>
                         <option value="0" <?= ($config['auto_cut'] ?? '') == 0 ? 'selected' : '' ?>>No</option>
                     </select>

                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Abrir gaveta</label>
                     <select class="form-custom search col-sm-4 ml-3" name="open_cash_drawer">
                         <option value="1" <?= ($config['open_cash_drawer'] ?? '') == 1 ? 'selected' : '' ?>>Sí</option>
                         <option value="0" <?= ($config['open_cash_drawer'] ?? '') == 0 ? 'selected' : '' ?>>No</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Utilizar firma</label>
                     <select class="form-custom search col-sm-4 ml-3" name="signature">
                         <option value="1" <?= ($config['signature'] ?? '') == 1 ? 'selected' : '' ?>>Sí</option>
                         <option value="0" <?= ($config['signature'] ?? '') == 0 ? 'selected' : '' ?>>No</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Footer Políticas</label>
                     <textarea class="form-custom col-sm-8 ml-3" name="policy_footer" rows="3"><?= htmlspecialchars($config['policy_footer'] ?? 'No hay devoluciones') ?></textarea>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Footer ticket</label>
                     <textarea class="form-custom col-sm-8 ml-3" name="ticket_footer" rows="3"><?= htmlspecialchars($config['ticket_footer'] ?? 'Gracias por su visita') ?></textarea>
                 </div>

                 <hr>

                 <!-- ================= CÓDIGO DE BARRAS ================= -->
                 <h5 class="mb-2">Código de barras</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Usar barcode</label>
                     <select class="form-custom search col-sm-4 ml-3" name="use_barcode">
                         <option value="1" <?= ($config['use_barcode'] ?? '') == 1 ? 'selected' : '' ?>>Sí</option>
                         <option value="0" <?= ($config['use_barcode'] ?? '') == 0 ? 'selected' : '' ?>>No</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Tipo</label>
                     <select class="form-custom search col-sm-4 ml-3" name="barcode_type">
                         <option value="CODE39">Code 39</option>
                         <option value="CODE128">Code 128</option>
                         <option value="EAN13">EAN-13</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Altura</label>
                     <input class="form-custom col-sm-4 ml-3" type="number" value="<?= $config['barcode_height'] ?? 80 ?>"
                         name="barcode_height">
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Ancho</label>
                     <input type="number" class="form-custom col-sm-4 ml-3"
                         value="<?= $config['barcode_width'] ?? 2 ?>"
                         name="barcode_width">
                 </div>

                 <hr>

                 <!-- ================= QR ================= -->
                 <h5 class="mb-2">Código QR</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Usar QR</label>
                     <select class="form-custom search col-sm-4 ml-3" name="use_qr">
                         <option value="1" <?= ($config['use_qr'] ?? '') == 1 ? 'selected' : '' ?>>Sí</option>
                         <option value="0" <?= ($config['use_qr'] ?? '') == 0 ? 'selected' : '' ?>>No</option>
                     </select>
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Tamaño</label>
                     <input type="number" class="form-custom col-sm-4 ml-3"
                         value="<?= $config['qr_size'] ?? 4 ?>"
                         name="qr_size">
                 </div>
                 <hr>

                 <!-- ================= ESPACIADO ================= -->
                 <h5 class="mb-2">Espaciado del ticket</h5>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Feed inicial</label>
                     <input type="number" class="form-custom col-sm-4 ml-3"
                         value="<?= $config['feed_start'] ?? 1 ?>"
                         name="feed_start">
                 </div>

                 <div class="form-group d-flex">
                     <label class="col-sm-3 text-right">Feed final</label>
                     <input class="form-custom col-sm-4 ml-3" type="number" value="4" name="feed_end">
                 </div>

             </div>
         </div>

         <p class="info-sm mt-2">
             Los campos marcados con <span class="text-danger">*</span> son obligatorios
         </p>

         <div class="buttons clearfix">
             <div class="floatButtons">

                 <button type="button" class="btn-custom btn-info" id="btnQzDiagnostico">
                     <i class="fas fa-stethoscope"></i>
                     <p>Diagnóstico</p>
                 </button>

                 <button type="submit" class="btn-custom btn-green">
                     <i class="fas fa-save"></i>
                     <p>Guardar</p>
                 </button>

             </div>
         </div>
     </form>
 </div>