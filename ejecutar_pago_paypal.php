<?php
    //header('Content-Type: application/json');
    session_start();
    $conn = require_once '../conexion.php';
    $id_usuario = $_SESSION['id_session'];
 
    
    include '../configuracion/cargar_datos_empresa.php';
    include '../inventarios/consulta_precio_e_impuestos.php';
    include '../inventarios/consulta_total_de_apartados.php';  
    include '../inventarios/funciones_movimiento.php';
    include 'calcular_comision_paypal.php';


    include '../users/permisos_perfil.php';
    $datosUsuario = datosUsuario($id_usuario,$conn);
    $status = $datosUsuario[0]['pedido'];

    $datosEmpresa = cargarDatosEmpresa($conn);
    $datosTienda = cargarDatosTienda($conn); 
    $comisionFijaPayPal = $datosTienda[0]['paypal_com_fija'];
    $comisionPorciento = $datosTienda[0]['paypal_com_tarjeta']; 
    $prod = $datosTienda[0]['modo_produccion']; 
 
    $total = totalApartadosYEnvio($status,$id_usuario,$conn);
    $totalpaypal = calcularPrecioConComision($total,$comisionFijaPayPal,$comisionPorciento,$conn);
           

    $paymentID = ($_POST['paymentID']);
    $payerID = ($_POST['payerID']); 

    
    if ($prod == 1) {
      $paypalClientID = $datosTienda[0]['paypal_cliente_id'];
       $paypalSecret = $datosTienda[0]['paypal_secret'];
      include 'PaypalExpress.class.php';
    } else {
      $paypalClientID = $datosTienda[0]['paypal_cliente_id_sandbox'];
       $paypalSecret = $datosTienda[0]['paypal_secret_sandbox'];
      include 'PaypalExpressSandBox.class.php';
    }


    $paypal = new PaypalExpress;
    
    $executeThePayment = $paypal->executeThePayment($paymentID,$payerID,$totalpaypal,$paypalClientID,$paypalSecret);

    $data = json_decode($executeThePayment);

     if (($data->state) == "approved") { 
      $medioPago = 3;
      $registro =  registrarPago($medioPago,$conn,$id_usuario,$totalpaypal,null);
      if ($registro == "Aprobado") {
        echo "Su pago ha sido aprovado.";
      }
     } else {
            echo $data;
     }





?>
