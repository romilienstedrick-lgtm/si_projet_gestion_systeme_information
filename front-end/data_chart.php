<?php

$bddPDO = new PDO('mysql:host=localhost;dbname=si_gestion', 'root', '');
$bddPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
SELECT c.nom_cours, COUNT(ac.id_apprenant) AS total
FROM cours c
LEFT JOIN apprenant_cours ac ON c.id_cours = ac.id_cours
GROUP BY c.id_cours
";

$stmt = $bddPDO->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);