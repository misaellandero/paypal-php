<?php
function calcularPrecioConComision($cantidad,$comisionFijaPayPal,$comisionPorciento,$conn)
{

 $cantidadMasComision = $cantidad + $comisionFijaPayPal;

 $porcentajeBase = 100 - $comisionPorciento;
 $cantidadFinal = (100 * $cantidadMasComision )/$porcentajeBase;

 return round($cantidadFinal,2) ;

}


function calcularPrecioConComisionOpenPay($cantidad,$comision_fija,$comisionPorciento,$conn)
{
 
 $cantidadMasComision = $cantidad + $comision_fija;
 $porcentajeBase = 100 - ($comisionPorciento * 1.16);
 $cantidadFinal = (100 * $cantidadMasComision)/$porcentajeBase;

 return round($cantidadFinal,2) ;

}

?>