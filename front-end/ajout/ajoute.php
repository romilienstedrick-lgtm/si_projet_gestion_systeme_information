<?php
try {

    $bddPDO = new PDO('mysql:host=localhost;dbname=si_gestion', 'root', "");

    $bddPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    $nom = strtoupper(trim($_POST['nom']));
    $prenom = ucwords((trim($_POST['prenom'])));
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $sexe = trim($_POST['sexe']);
    $id_session = trim($_POST['session']);

    if(
        !empty($nom) &&
        !empty($prenom) &&
        !empty($email) &&
        !empty($telephone) &&
        !empty($adresse) &&
        !empty($sexe) &&
        !empty($id_session)
    ){

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

        $sql = "INSERT INTO apprenants
        (nom, prenom, email, telephone, adresse, sexe, id_session, montant_total)

        VALUES
        (:nom, :prenom, :email, :telephone, :adresse, :sexe, :id_session, :montant_total)";

        $stmt = $bddPDO->prepare($sql);

        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telephone', $telephone);
        $stmt->bindValue(':adresse', $adresse);
        $stmt->bindValue(':sexe', $sexe);
        $stmt->bindValue(':id_session', $id_session);
        $stmt->bindValue(':montant_total', $montant_total);

        try {

            $stmt->execute();
            /*
            |--------------------------------------------------------------------------
            | RECUPERATION ID APPRENANT
            |--------------------------------------------------------------------------
            */
            $id_apprenant = $bddPDO->lastInsertId();

            /*
            |--------------------------------------------------------------------------
            | INSERTION DES COURS CHOISIS
            |--------------------------------------------------------------------------
            */

            // WORD
            if(isset($_POST['Word'])){
                $sqlCours = "INSERT INTO apprenant_cours
                (id_apprenant, id_cours)
                VALUES
                (:id_apprenant, :id_cours)";
                $stmtCours = $bddPDO->prepare($sqlCours);
                $stmtCours->execute([
                        ':id_apprenant' => $id_apprenant,
                        ':id_cours' => 1
                ]);
            }

            // EXCEL
            if(isset($_POST['Excel'])){
                $sqlCours = "INSERT INTO apprenant_cours
                (id_apprenant, id_cours)
                VALUES
                (:id_apprenant, :id_cours)";
                $stmtCours = $bddPDO->prepare($sqlCours);
                $stmtCours->execute([
                        ':id_apprenant' => $id_apprenant,
                        ':id_cours' => 2
                ]);

            }
            // POWERPOINT
            if(isset($_POST['PowerPoint'])){
                $sqlCours = "INSERT INTO apprenant_cours
                (id_apprenant, id_cours)
                VALUES
                (:id_apprenant, :id_cours)";
                $stmtCours = $bddPDO->prepare($sqlCours);
                $stmtCours->execute([
                        ':id_apprenant' => $id_apprenant,
                        ':id_cours' => 3
                ]);
            }
            /*
            |--------------------------------------------------------------------------
            | REDIRECTION
            |--------------------------------------------------------------------------
            */
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } catch(PDOException $e){

            if($e->getCode() == 23000){
                if(str_contains($e->getMessage(), 'email')){
                    $erreur = "Cet email existe déjà.";
                } elseif(str_contains($e->getMessage(), 'id_session')){
                    $erreur = "Session invalide.";
                } else {
                    $erreur = "Donnée déjà existante ou invalide.";
                }
            } else {
                $erreur = "Une erreur est survenue.";
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }

}


try {
    $sql = "SELECT * FROM apprenants ORDER BY id_apprenant DESC";
    $firstrequete = $bddPDO->query($sql);
    $apprenants = $firstrequete->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $apprenants = [];
    $erreur = $e->getMessage();
}

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if(isset($_GET['success'])): ?>

    <script>

        Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Apprenant ajouté avec succès',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
        }).then(() => {

            // nettoyage champs texte + select
            ["nom","prenom","email","telephone","adresse","sexe","session"].forEach(name => {
                localStorage.removeItem("form_" + name);
        });

        // nettoyage checkbox cours
        ["Word","Excel","PowerPoint"].forEach(name => {
            localStorage.removeItem("form_" + name);
    });

    // nettoyage prix
    localStorage.removeItem("form_prix_total");

});

// Supprime ?success=1 de l'URL
window.history.replaceState({}, document.title, window.location.pathname);

</script>

<?php endif; ?>

<?php if(isset($erreur)): ?>

<script>

    Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '<?= addslashes($erreur) ?>',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
    });

</script>

<?php endif; ?>


<div class="container-ajoute">
    <div class="ajout-apprenant">

    <h1>Ajouter un apprenant</h1>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">

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
            <label for="adresse"> Adresse :</label>
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
                        <label for="Word">Word</label>
                            <input type="checkbox" id="Word" name="Word" value="Word">
                        </div>

                        <div class="cours-option">
                        <label for="Excel">Excel</label>
                            <input type="checkbox" id="Excel" name="Excel" value="Excel">
                        </div>

                        <div class="cours-option">
                        <label for="PowerPoint">Power Point</label>
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

        <li data-id="<?= $apprenant['id_apprenant'] ?>">

            <span>
                <?= htmlspecialchars($apprenant['nom']) ?>
                <?= htmlspecialchars($apprenant['prenom']) ?>
            </span>

            <div class="actions">

                <!-- INFO -->
                <button class="btn-info"
                data-apprenant='<?= htmlspecialchars(json_encode($apprenant), ENT_QUOTES, "UTF-8") ?>'>

                <!-- Lucide info icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>

            </svg>

        </button>

        <!-- DELETE -->
        <button class="btn-delete" data-id="<?= $apprenant['id_apprenant'] ?>">

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-x-icon lucide-user-round-x">
                <path d="M2 21a8 8 0 0 1 11.873-7"/><circle cx="10" cy="8" r="5"/><path d="m17 17 5 5"/>
            <path d="m22 17-5 5"/></svg>


        </button>

    </div>

</li>

<?php endforeach; ?>

<?php else: ?>

<li>Aucun apprenant trouvé</li>

    <?php endif; ?>

</ul>

</div>
</div>


<!-- Modal pour afficher les détails de l'apprenant et permettre la modification -->
<div id="modal-info" class="modal hidden">

    <div class="modal-content">

    <h2>Détails / Modification apprenant</h2>

        <div id="view-mode">

        <p><b>Nom :</b> <span id="view-nom"></span></p>
        <p><b>Prénom :</b> <span id="view-prenom"></span></p>
        <p><b>Email :</b> <span id="view-email"></span></p>
        <p><b>Téléphone :</b> <span id="view-telephone"></span></p>
        <p><b>Adresse :</b> <span id="view-adresse"></span></p>

        </div>

        <div id="edit-mode" class="hidden">

            <input type="hidden" id="edit-id">

            <input type="text" id="edit-nom" placeholder="Nom">
            <input type="text" id="edit-prenom" placeholder="Prénom">
            <input type="email" id="edit-email" placeholder="Email">
            <input type="text" id="edit-telephone" placeholder="Téléphone">
            <input type="text" id="edit-adresse" placeholder="Adresse">

        </div>

        <div class="modal-actions">

        <button id="btn-edit">Modifier</button>
        <button id="btn-save" class="hidden">Enregistrer</button>
        <button id="btn-close">Fermer</button>

        </div>

    </div>

</div>