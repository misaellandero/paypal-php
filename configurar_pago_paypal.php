<?php
//en esta primera parte se verifica que el cliente y la informacion sea correcta
    header('Content-Type: application/json');
    session_start(); 
    $id_usuario = $_SESSION['id_session'];

    //archivo de conexion a la base de datos 
    $conn = require_once '../conexion.php';
    //en este apartado incluyo mis fucniones para traer mi datos como mis llaves privadas o los precios y productos
    include '../configuracion/cargar_datos_empresa.php';
    include '../inventarios/consulta_precio_e_impuestos.php';
    include '../inventarios/consulta_total_de_apartados.php';  
    include 'calcular_comision_paypal.php';
    $datosEmpresa = cargarDatosEmpresa($conn);
    $datosTienda = cargarDatosTienda($conn); 

    //solo para calcular las comisiones de paypal
    $comisionFijaPayPal = $datosTienda[0]['paypal_com_fija'];
    $comisionPorciento = $datosTienda[0]['paypal_com_tarjeta'];

    //en esta parte reviso que el cliente tenga todos sus datos de perfil correctos para comprar con tarjeta (su crendencial para validar su firma etc...) y evitar reclamos 
    include '../users/permisos_perfil.php';
    $datosUsuario = datosUsuario($id_usuario,$conn);
    $status = $datosUsuario[0]['pedido'];
    $verificado = $datosUsuario[0]['verificado'];

    if ($verificado < 2) {
      echo 'Tu identidad no ha sido verificada, por lo que no puedes comprar mas que en efectivo';
      die();
    }

 //dependiendo si estamos en modo de pruebas o produccion se ejecuta una clase u otra
    $datosEmpresa = cargarDatosEmpresa($conn);
    $datosTienda = cargarDatosTienda($conn);
    $prod = $datosTienda[0]['modo_produccion']; 
   

//calculando el costo tota ya con las comisiones de paypal
    $total = totalApartadosYEnvio($status,$id_usuario,$conn);
    $totalpaypal = calcularPrecioConComision($total,$comisionFijaPayPal,$comisionPorciento,$conn);
            

    // configurar el pago 
    $totalpaypal = round($totalpaypal,2);
    if ($prod == 1) {
      $paypalClientID = $datosTienda[0]['paypal_cliente_id'];
       $paypalSecret = $datosTienda[0]['paypal_secret'];
      include 'PaypalExpress.class.php';
    } else {
      $paypalClientID = $datosTienda[0]['paypal_cliente_id_sandbox'];
       $paypalSecret = $datosTienda[0]['paypal_secret_sandbox'];
      include 'PaypalExpressSandBox.class.php';
    }

    //revisar si el pago puede ser aprovado 
     $paypal = new PaypalExpress;

     $Setupthepayment = $paypal->Setupthepayment($totalpaypal,$paypalClientID,$paypalSecret);

    //respuesta de paypal
    echo $Setupthepayment;


?>
