<?php

session_start();

new Amigo();

Class Amigo {

  public function __construct() {
    $route = $_GET['rt'];
    $this->$route();
  }

  public function listarAmigos() {
    $xml = new DOMDocument();
    $xml->validateOnParse = true;
    $xml->load('nomes.xml');

    $amigos = array();

    foreach($xml->getElementsByTagName('amigo') as $amigo){

      $attrs = array();
      for ($i = 0; $i < $amigo->attributes->length; $i++) {
        $node = $amigo->attributes->item($i);
        $attrs[$node->name] = $node->nodeValue;
      }

      $amigos[] = array(
        'id' => $attrs['id'],
        'nome' => $amigo->nodeValue,
        'foto' => "http://graph.facebook.com/$attrs[id]/picture",
        'sorteou' => $attrs['sorteou'],
        'sorteado' => $attrs['sorteado']
        );

    }

    echo json_encode($amigos);
    return true;
  }


  public function sortearAmigo() {
    $xml = new DOMDocument();
    $xml->validateOnParse = true;
    $xml->load('nomes.xml');

    $sorteou = $xml->getElementById($_POST['sorteou']);

    if($sorteou->getAttribute('xml:sorteou') == "false" && !isset($_SESSION["sorteou"])){

      $sorteou->setAttribute('xml:sorteou', 'true');

      $amigos = array();

      foreach ($xml->getElementsByTagName('amigo') as $amigo) {
        $err = false;
        $id = "";
        for ($i = 0; $i < $amigo->attributes->length; $i++) {
          $node = $amigo->attributes->item($i);
          switch ($node->name) {
            case 'id':
            if( $node->nodeValue == $_POST['sorteou'] )
              $err = true;
            else
              $id = $node->nodeValue;
            break;

            case 'sorteado':
            if( $node->nodeValue == "true" )
              $err = true;
            break;
          }
        }
        if($err == false)
          $amigos[] = $id;
      }

      shuffle($amigos);
      $amigo = $xml->getElementById( $amigos[0] );
      $amigo->setAttribute('xml:sorteado', 'true');
      $xml->save('nomes.xml');

      $_SESSION["sorteou"] = true;

      echo json_encode(array("erro" => false, "msg" => "Seu amigo secreto é...", "amigo" => $amigos[0]));
      return true;

    } else {
      echo json_encode(array("erro" => true, "msg" => "Heeey .. você já sorteou seu amigo secreto!"));
      return false;
    }

  }

}