<?php

class PaypalExpress{

   const paypalURL = "https://api.paypal.com";
   //Las constantes aqui pueden ser usadas si guardas tus varibles en el servidor, sin embargo para mi fue mas util pasarlas en la funcion pues las jalo desde mi base de datos 
   //const paypalClientID  = 'tu id de paypal';
   //const paypalSecret   = 'tu llave privada de paypal';


    public function executeThePayment($paymentID,$payerID,$total,$paypalClientID,$paypalSecret){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::paypalURL."/v1/payments/payment/".$paymentID."/execute");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD,$paypalClientID.":".$paypalSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //pago en pesos mexicanos
        $data = '{
                            "payer_id": "'.$payerID.'",

                          "transactions":[
                            {
                              "amount":{
                                "total":'.$total.',
                                "currency":"MXN"
                              }
                            }
                          ]
                        }';

        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);

        if(empty($response)){
            return false;
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            var_dump($httpStatusCode); 
        }else{
             // Transaction data
          return $response;
            /*$result = json_decode($response, true);
            return $result['id'];*/
        }

        curl_close($ch);

    }

    public function Setupthepayment($total,$paypalClientID,$paypalSecret){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::paypalURL."/v1/payments/payment");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD,$paypalClientID.":".$paypalSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // datos  de regreso para el cliente
        $data = '{
                          "intent":"sale",
                          "redirect_urls":{
                            "return_url":"http://tuSitioWeb/index.php",
                            "cancel_url":"http://tuSitioWeb/operacionCancelada.php"
                          },
                          "payer":{
                            "payment_method":"paypal"
                          },
                          "transactions":[
                            {
                              "amount":{
                                "total":'.$total.',
                                "currency":"MXN"
                              }
                            }
                          ]
                        }';

        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);

        if(empty($response)){
            return false;
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            var_dump($httpStatusCode);
        }else{
             // Transaction data
          return $response;
            /*$result = json_decode($response, true);
            return $result['id'];*/
        }

        curl_close($ch);

    }

}
?>
