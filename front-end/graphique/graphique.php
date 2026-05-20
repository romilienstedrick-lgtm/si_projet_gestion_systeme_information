<?php

try {

    $bddPDO = new PDO(
        'mysql:host=localhost;dbname=si_gestion',
        'root',
        ""
    );

    $bddPDO->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e){

    die("Erreur : " . $e->getMessage());

}

# =========================================
# NOMBRE TOTAL APPRENANTS
# =========================================

$requeteTotal = $bddPDO->query("
SELECT COUNT(*) AS total
FROM apprenants
");

$totalApprenants = $requeteTotal->fetch();

# =========================================
# NOMBRE HOMMES
# =========================================

$requeteHomme = $bddPDO->query("
SELECT COUNT(*) AS total_homme
FROM apprenants
WHERE sexe = 'homme'
");

$totalHomme = $requeteHomme->fetch();

# =========================================
# NOMBRE FEMMES
# =========================================

$requeteFemme = $bddPDO->query("
SELECT COUNT(*) AS total_femme
FROM apprenants
WHERE sexe = 'femme'
");

$totalFemme = $requeteFemme->fetch();

# =========================================
# PARTIE LISTE DYNAMIQUE
# =========================================

$type = $_GET['type'] ?? 'total';

if($type == 'homme'){

    $sqlListe = "
    SELECT *
    FROM apprenants
    WHERE sexe = 'homme'
    ORDER BY nom ASC
    ";

} elseif($type == 'femme'){

    $sqlListe = "
    SELECT *
    FROM apprenants
    WHERE sexe = 'femme'
    ORDER BY nom ASC
    ";

} else {

    $sqlListe = "
    SELECT *
    FROM apprenants
    ORDER BY nom ASC
    ";

}

$requeteListe = $bddPDO->query($sqlListe);

$listeApprenants = $requeteListe->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-graphique">
    <div class="partie-info-nombre-apprenants">
        <a href="?type=total" class="<?= ($type == 'total') ? 'active-card' : ''; ?>">
            <div class="info-nbr-total-apprenants">
            <h3 class="h1-nbr-totaux">Nombre Total Apprenants</h3>
                <div class="nbr-totaux">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round-icon lucide-users-round">
                        <path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/>
                    <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>
                <p class="nbr-totaux-value"> <?= $totalApprenants['total'] ?> </p>
                </div>
            </div>
        </a>

        <a href="?type=homme" class="<?= ($type == 'homme') ? 'active-card' : ''; ?>">
            <div class="info-nbr-apprenants-masculin">
            <h3 class="h1-nbr-totaux">Nombre Total Masculin</h3>
                <div class="nbr-totaux">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mars-icon lucide-mars">
                    <path d="M16 3h5v5"/><path d="m21 3-6.75 6.75"/><circle cx="10" cy="14" r="6"/></svg>
                <p class="nbr-totaux-value"> <?= $totalHomme['total_homme'] ?> </p>
                </div>
            </div>
        </a>

        <a href="?type=femme" class="<?= ($type == 'femme') ? 'active-card' : ''; ?>">
            <div class="info-nbr-apprenants-feminin">
            <h3 class="h1-nbr-totaux">Nombre Total Féminin</h3>
                <div class="nbr-totaux">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-venus-icon lucide-venus">
                    <path d="M12 15v7"/><path d="M9 19h6"/><circle cx="12" cy="9" r="6"/></svg>
                <p class="nbr-totaux-value"> <?= $totalFemme['total_femme'] ?> </p>
                </div>
            </div>
        </a>
    </div>

    <div class="partie-affichage-nombre">
        <ul class="liste-nombre-apprenants">

        <?php if(!empty($listeApprenants)): ?>

        <?php foreach($listeApprenants as $apprenant): ?>

        <li>

            <?= htmlspecialchars($apprenant['nom']) ?>
            <?= htmlspecialchars($apprenant['prenom']) ?>

            <?php
                $sexe = strtolower(trim($apprenant['sexe']));
            ?>

            <span class="
                badge-sexe
                <?= ($sexe == 'homme') ? 'homme' : 'femme'; ?>
            ">

                <?= ($sexe == 'homme') ? 'H' : 'F'; ?>

            </span>

        </li>

        <?php endforeach; ?>

        <?php else: ?>

    <li>Aucun apprenant trouvé</li>

        <?php endif; ?>

    </ul>
</div>


<!-- Partie pour le graphique avec Chart.js -->
<div class="my-chart-container">
    <canvas id="myChart"></canvas>
</div>

<div class="my-chart-container-2">
    <canvas id="myChart2"></canvas>
</div>

</div>