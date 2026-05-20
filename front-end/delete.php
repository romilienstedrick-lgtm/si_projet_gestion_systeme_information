<?php

try {
    $bddPDO = new PDO('mysql:host=localhost;dbname=si_gestion', 'root', "");
    $bddPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['id'])) {

        $id = $_POST['id'];

        $stmt = $bddPDO->prepare("DELETE FROM apprenants WHERE id_apprenant = ?");
        $stmt->execute([$id]);

        echo "ok";

    }

}catch (Exception $e) {

    echo $e->getMessage();

}

?>