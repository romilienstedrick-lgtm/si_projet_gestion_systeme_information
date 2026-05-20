<?php

try {

    $bddPDO = new PDO('mysql:host=localhost;dbname=gestion_si', 'root', "");

    $bddPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sql = "SELECT nom, prenom FROM apprenants ORDER BY id_apprenant DESC";
        $firstrequete = $bddPDO->query($sql);
    
        $apprenants = $firstrequete->fetchAll(PDO::FETCH_ASSOC);
    
    } catch (Exception $e) {
        $apprenants = []; // évite l'erreur si problème
        echo "Erreur : " . $e->getMessage();
    }

    // Récupération des prix des cours
    $prixWord = 0;
    $prixExcel = 0;
    $prixPowerPoint = 0;

    $requeteCours = $bddPDO->query("SELECT nom_cours, prix FROM cours");

    while($coursBDD = $requeteCours->fetch()){

        if($coursBDD['nom_cours'] == 'Word'){
            $prixWord = $coursBDD['prix'];
        }

        if($coursBDD['nom_cours'] == 'Excel'){
            $prixExcel = $coursBDD['prix'];
        }

        if($coursBDD['nom_cours'] == 'Power Point'){
            $prixPowerPoint = $coursBDD['prix'];
        }

    }

} catch(PDOException $e){

    die("Erreur : " . $e->getMessage());

}

if(isset($_POST['envoyer-formulaire'])){

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $sexe = $_POST['sexe'];

    // IMPORTANT
    $id_session = $_POST['session'];

    // Vérification des champs
    if(
        !empty($nom) &&
        !empty($prenom) &&
        !empty($email) &&
        !empty($telephone) &&
        !empty($adresse) &&
        !empty($sexe) &&
        !empty($id_session)
    ){

        // Calcul automatique du montant
        $montant_total = 0;

        if(isset($_POST['Word'])){
            $montant_total += $prixWord;
        }

        if(isset($_POST['Excel'])){
            $montant_total += $prixExcel;
        }

        if(isset($_POST['PowerPoint'])){
            $montant_total += $prixPowerPoint;
        }

        // Requête adaptée à ta table
        $sql = "INSERT INTO apprenants
        (nom, prenom, email, telephone, adresse, sexe, id_session, montant_total)

        VALUES
        (:nom, :prenom, :email, :telephone, :adresse, :sexe, :id_session, :montant_total)";

        $stmt = $bddPDO->prepare($sql);

        $stmt->bindvalue(':nom', $nom);
        $stmt->bindvalue(':prenom', $prenom);
        $stmt->bindvalue(':email', $email);
        $stmt->bindvalue(':telephone', $telephone);
        $stmt->bindvalue(':adresse', $adresse);
        $stmt->bindvalue(':sexe', $sexe);
        $stmt->bindvalue(':id_session', $id_session);
        $stmt->bindvalue(':montant_total', $montant_total);

        try {

            $stmt->execute();

            echo '<script>' . PHP_EOL;
                echo '    alert("Apprenant ajouté avec succès !");' . PHP_EOL;
                echo '    document.querySelector("form").reset();' . PHP_EOL;
            echo '</script>' . PHP_EOL;
        } catch(PDOException $e){

            // Erreur doublon email
            if($e->getCode() == 23000){

                echo '<script>' . PHP_EOL;
                    echo '    alert("Cet email existe déjà !");' . PHP_EOL;
                    echo '    document.querySelector("form").reset();' . PHP_EOL;
                echo '</script>' . PHP_EOL;

            } else {

                $msg = addslashes($e->getMessage());
                echo '<script>' . PHP_EOL;
                    echo '    alert("Erreur lors de l\'ajout de l\'apprenant : ' . $msg . '");' . PHP_EOL;
                    echo '    document.querySelector("form").reset();' . PHP_EOL;
                echo '</script>' . PHP_EOL;

            }

        }

    } else {

        echo '<script>' . PHP_EOL;
            echo '    alert("Veuillez remplir tous les champs.");' . PHP_EOL;
            echo '    document.querySelector("form").reset();' . PHP_EOL;
        echo '</script>' . PHP_EOL;

    }



}

?>

<div class="container-ajoute">
    <div class="ajout-apprenant">

    <h1>Ajouter un apprenant</h1>

        <form method="post">

            <div class="form-group">
            <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez son nom">
            </div>

            <div class="form-group">
            <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez son prénom">
            </div>

            <div class="form-group">
            <label for="email">Email :</label>
                <input type="email" id="email" name="email" placeholder="Entrez son email">
            </div>

            <div class="form-group">
            <label for="telephone">Téléphone :</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Entrez son numéro de téléphone">
            </div>

            <div class="form-group">
            <label for="Adresse"> Adresse :</label>
                <input type="text" id="adresse" name="adresse" placeholder="Entrez son adresse">
            </div>

            <div class="form-group">
            <label for="sexe">Sexe :</label>

                <select id="sexe" name="sexe">

                <option value="">Sélectionnez votre sexe</option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>

                </select>
            </div>

            <!-- Section pour le choix des cours -->
            <div class="choix-cours-session">

                <div class="form-group-choix-cours">

                <h2>Choix du cours :</h2>

                    <div class="cours-disponibles">

                        <div class="cours-option">
                        <label>Word</label>
                            <input type="checkbox" id="Word" name="Word" value="Word">
                        </div>

                        <div class="cours-option">
                        <label>Excel</label>
                            <input type="checkbox" id="Excel" name="Excel" value="Excel">
                        </div>

                        <div class="cours-option">
                        <label>Power Point</label>
                            <input type="checkbox" id="PowerPoint" name="PowerPoint" value="PowerPoint">
                        </div>

                    </div>

                </div>

                <div class="form-group-session">

                <label for="session">Session :</label>

                    <select id="session" name="session" required>

                    <option value="">Sélectionnez une session</option>
                    <option value="1">Janvier - Mars 2026</option>
                    <option value="2">Avril - Juin 2026</option>
                    <option value="3">Juillet - Septembre 2026</option>
                    <option value="4">Octobre - Décembre 2026</option>

                    </select>

                </div>

            </div>

            <div class="prix-et-envoye-formulaire">

                <div class="prix-cours">

                    <h2>
                        Prix du cours :
                    <span id="prix-total">0 AR</span>
                    </h2>

                </div>

                <button
                class="envoyer-formulaire"
                type="submit"
                name="envoyer-formulaire">

                Payer et enregistrer

            </button>

        </div>

    </form>

</div>


<div class="liste-apprenants">

<h2>Liste des apprenants ajoutés</h2>

    <ul>
    <?php if (!empty($apprenants)): ?>
        <?php foreach ($apprenants as $apprenant): ?>
            <li><?= $apprenant['nom'] ?> <?= $apprenant['prenom'] ?></li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>Aucun apprenant trouvé</li>
    <?php endif; ?>
    </ul>

</div>
</div>