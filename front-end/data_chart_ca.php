<?php
try {
    $bddPDO = new PDO('mysql:host=localhost;dbname=si_gestion', 'root', "");
    $bddPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT 
            s.nom_session,
            SUM(a.montant_total) AS ca_total
        FROM apprenants a
        INNER JOIN sessions s ON a.id_session = s.id_session
        GROUP BY s.id_session
        ORDER BY s.id_session
    ";

    $stmt = $bddPDO->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>