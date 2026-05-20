<?php

$pdo = new PDO('mysql:host=localhost;dbname=si_gestion', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(!empty($_POST['id'])) {

    $sql = "UPDATE apprenants SET
        nom = :nom,
        prenom = :prenom,
        email = :email,
        telephone = :telephone,
        adresse = :adresse
        WHERE id_apprenant = :id";

    $stmt = $pdo->prepare($sql);

    $ok = $stmt->execute([
        ':id' => $_POST['id'],
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':email' => $_POST['email'],
        ':telephone' => $_POST['telephone'],
        ':adresse' => $_POST['adresse']
    ]);

    echo $ok ? "ok" : "error";

} else {
    echo "missing_id";
}