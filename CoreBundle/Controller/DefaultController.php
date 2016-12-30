<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/core")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/test")
     */
    public function indexAction()
    {
        $input = "i8PSNhUkPYUqPp1i08LPc/2Ii4cLU9uEjyKihCfQJUbU1tdTL2gZ+I3ZyUfWPT+FhtUfmjvLGAP4QTBHcIa8xOQpf5WbRNxZUgfvWGkm8HeOGQqbNPzNG+KWXXIz9dyexB5C7EGCKmRxDnclDIG9ZDH1p8k8tSUsz4net1pa63fw=";

        $encoded = $this->base64_url_encode($input);
        print_r($encoded);

        $decoded = $this->base64_url_decode($encoded);
        print_r($decoded);

        echo "<br>";
        echo $input . "<br>";
        echo $decoded . "<br>";
        exit();

        $userCredentials = array(
          "UserCredentials" => array(
              "UniqueCorporateId" => "dbb7c36d-b002-41f7-8e3f-d81edca244d0",
              "UniqueProfileGuid" => "c72cc546-b8f0-42a8-9380-c9dbacaf8ecc",
              "UserName" => "Muarali Vaddiparthi",
              "Email" => "muraliguru2015@gmail.com",
              "Password" => "kBJPcviSNAFooH/F/x21wLrF01tk/iETWpEjf6DunQn7Hxkjs/fs73duSHHaa5J8Tqy36OmV1Eb9/LAo/ElYOqj/87+M72GfQwgS7vIto2gY8pmXDyNGXwlUyX9EUiRJR3nOJH1j8Lt1kvcj54+9Kd3LbMTgk6SN1LZ3EnWQsFI=",
              "Enabled" => true,
              "ProductCredentials" => array(
                  "ProductName" => "ETS",

              )

          )
        );


        return $this->render('CoreBundle:Default:index.html.twig');
    }

    function base64_url_encode($input) {
        return strtr(base64_encode($input), '+/=', '-_,');
    }

    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
}
