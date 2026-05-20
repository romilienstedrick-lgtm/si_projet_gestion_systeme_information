<?php

$conn = new mysqli("localhost", "root", "", "si_gestion");

if ($conn->connect_error) {
    die(json_encode([
        "error" => "Connexion échouée"
    ]));
}

$conn->set_charset("utf8");

$sql = "
SELECT 
    a.id_apprenant AS id,
    a.nom,
    a.prenom,
    a.email,
    a.telephone,
    a.adresse,
    a.sexe,
    a.date_inscription,
    a.montant_total,
    s.nom_session,
    GROUP_CONCAT(c.nom_cours SEPARATOR ', ') AS formation

FROM apprenants a

LEFT JOIN sessions s
ON a.id_session = s.id_session

LEFT JOIN apprenant_cours ac
ON a.id_apprenant = ac.id_apprenant

LEFT JOIN cours c
ON ac.id_cours = c.id_cours

GROUP BY a.id_apprenant
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');

echo json_encode($data);