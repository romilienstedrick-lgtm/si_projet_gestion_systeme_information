<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS INFORMATIQUE</title>
        <link rel="stylesheet" href="../asset/css/constante.css">
        <link rel="stylesheet" href="roote.css">
        <link rel="stylesheet" href="ajout/ajoute.css">
        <link rel="stylesheet" href="graphique/graphique.css">
        <link rel="stylesheet" href="certificat/certificat.css">
        <link rel="icon" type="image/png" sizes="32x32" href="../asset/image/js_informatique.jpg">
        <link rel="icon" type="image/png" sizes="16x16" href="../asset/image/js_informatique.jpg">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body>
        <div class="top-header">
            <div class="photo">
                <img src="../asset/image/js_informatique.jpg" alt="JS INFORMATIQUE">
            </div>
            <nav class="navigation-bar">
                <ul>
                    <!--bouton pour voir la section d'ajout des apprenants -->
                    <li><a href="#section-ajout" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-pen-icon lucide-user-round-pen">
                            <path d="M2 21a8 8 0 0 1 10.821-7.487"/>
                        <path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/><circle cx="10" cy="8" r="5"/></svg>
                        Ajout
                    </a>
                </li>

                <!--bouton pour voir la section des graphes des apprenants -->
                <li> <a href="#section-graphique" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-no-axes-combined-icon lucide-chart-no-axes-combined">
                        <path d="M12 16v5"/><path d="M16 14v7"/>
                    <path d="M20 10v11"/><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"/><path d="M4 18v3"/><path d="M8 14v7"/></svg>
                    Graphiques
                </a>
            </li>

            <!--bouton pour voir la section de certification des apprenants -->
            <li> <a href="#section-certificat" class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-graduation-cap-icon lucide-graduation-cap">
                    <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/>
                <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
            Certificat</a>
        </li>
    </ul>
</nav>
</div>

<!--Section pour l'ajout des apprenants -->
<section id='section-ajout' class="section">
    <?php include 'ajout/ajoute.php'; ?>
</section>

<!--Section pour vooir les graphiques concernants les apprenants -->
<section id='section-graphique' class="section">
    <?php include 'graphique/graphique.php'; ?>
</section>

<!--Section pour la certifications des apprenants -->
<section id='section-certificat' class="section">
    <?php include 'certificat/certificat.php'; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="roote.js"></script>
<script src="graphique/graphique.js"></script>
<script src="graphique/line_chart.js"></script>
<script src="certificat/certificat.js"></script>

<!-- Script pour le calcul du prix total en fonction des cours sélectionnés dans le formulaire de ajout.php-->
<script>

        const word = document.getElementById("Word");
        const excel = document.getElementById("Excel");
        const powerpoint = document.getElementById("PowerPoint");

        const prixTotal = document.getElementById("prix-total");

        // Prix venant de PHP/MySQL
        const prixWord = <?php echo $prixWord; ?>;
        const prixExcel = <?php echo $prixExcel; ?>;
        const prixPowerPoint = <?php echo $prixPowerPoint; ?>;

        function calculerPrix(){

                let total = 0;

                if(word.checked){
                    total += prixWord;
                }

                if(excel.checked){
                    total += prixExcel;
                }

                if(powerpoint.checked){
                    total += prixPowerPoint;
                }

                prixTotal.textContent = total + " AR";

            }

            word.addEventListener("change", calculerPrix);
            excel.addEventListener("change", calculerPrix);
            powerpoint.addEventListener("change", calculerPrix);


            // Partie pour le bouton de suppression
            document.querySelectorAll('.btn-delete').forEach(button => {

                button.addEventListener('click', function () {

                    const id = this.dataset.id;
                    const li = this.closest('li');

                    Swal.fire({
                            title: 'Supprimer ?',
                            text: "Cette action est irréversible",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Oui supprimer',
                            cancelButtonText: 'Annuler'
                    }).then((result) => {

                        if (result.isConfirmed) {

                                fetch("delete.php", {
                                    method: "POST",
                                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                    body: "id=" + encodeURIComponent(id)
                            })
                            .then(res => res.text())
                            .then(data => {

                                if (data.trim() === "ok") {

                                        li.remove();

                                        Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Apprenant supprimé',
                                                showConfirmButton: false,
                                                timer: 2000
                                        });

                                        setTimeout(() => {
                                        
                                            location.reload();
                                        
                                        }, 1500);

                                    } else {

                                        Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'error',
                                                title: 'Erreur de suppression',
                                                timer: 2000
                                        });

                                    }

                            });

                        }

                });

        });

});


// Partie pour le MODAL UPDATE
    const modal = document.getElementById("modal-info");

    let current = null;

    // OUVRIR MODAL
    document.querySelectorAll(".btn-info").forEach(btn => {

        btn.addEventListener("click", function () {

            current = JSON.parse(this.dataset.apprenant);

            // VIEW MODE
            document.getElementById("view-nom").textContent = current.nom;
            document.getElementById("view-prenom").textContent = current.prenom;
            document.getElementById("view-email").textContent = current.email ?? '';
            document.getElementById("view-telephone").textContent = current.telephone ?? '';
            document.getElementById("view-adresse").textContent = current.adresse ?? '';

            // EDIT MODE RESET
            document.getElementById("edit-mode").classList.add("hidden");
            document.getElementById("view-mode").classList.remove("hidden");

            document.getElementById("btn-save").classList.add("hidden");
            document.getElementById("btn-edit").classList.remove("hidden");

            modal.classList.remove("hidden");

    });

});

// PASSER EN MODE EDIT
document.getElementById("btn-edit").addEventListener("click", () => {

    document.getElementById("edit-id").value = current.id_apprenant;
    document.getElementById("edit-nom").value = current.nom;
    document.getElementById("edit-prenom").value = current.prenom;
    document.getElementById("edit-email").value = current.email ?? '';
    document.getElementById("edit-telephone").value = current.telephone ?? '';
    document.getElementById("edit-adresse").value = current.adresse ?? '';

    document.getElementById("view-mode").classList.add("hidden");
    document.getElementById("edit-mode").classList.remove("hidden");

    document.getElementById("btn-edit").classList.add("hidden");
    document.getElementById("btn-save").classList.remove("hidden");

});

// ENREGISTRER MODIFICATION
document.getElementById("btn-save").addEventListener("click", () => {

    const data = new FormData();

    data.append("id", document.getElementById("edit-id").value);
    data.append("nom", document.getElementById("edit-nom").value);
    data.append("prenom", document.getElementById("edit-prenom").value);
    data.append("email", document.getElementById("edit-email").value);
    data.append("telephone", document.getElementById("edit-telephone").value);
    data.append("adresse", document.getElementById("edit-adresse").value);

    fetch("update.php", {
        method: "POST",
        body: data
})
.then(res => res.text())
.then(res => {

    if(res.trim() === "ok") {

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: "success",
            title: "Mise à jour réussie",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        setTimeout(() => {
            location.reload();
        }, 2000);

    } else {

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: "error",
            title: "Erreur de la mise à jour",
            showConfirmButton: false,
            timer: 2000
        });

    }

});

});

// FERMER MODAL
document.getElementById("btn-close").addEventListener("click", () => {
    modal.classList.add("hidden");
});


// FERMER MODAL EN CLIQUANT EN DEHORS
window.addEventListener("click", (e) => {

    if (e.target === modal) {
        modal.classList.add("hidden");
    }

});

// FERMER MODAL AVEC ECHAP
window.addEventListener("keydown", (e) => {

    if (e.key === "Escape") {
        modal.classList.add("hidden");
    }

});

// localStorage pour garder le theme choisi par l'utilisateur même après le rechargement de la page
const form = document.querySelector("form");

// champs texte + selects
const textFields = ["nom", "prenom", "email", "telephone", "adresse", "sexe", "session"];

// checkboxes cours
const courseFields = ["Word", "Excel", "PowerPoint"];

// SAUVEGARDE AUTOMATIQUE Champs texte + select
textFields.forEach(name => {

    const el = document.querySelector(`[name="${name}"]`);

    if (el) {

        el.addEventListener("input", () => {
            localStorage.setItem("form_" + name, el.value);
        });

        el.addEventListener("change", () => {
            localStorage.setItem("form_" + name, el.value);
        });

    }

});

// CHECKBOXES COURS
courseFields.forEach(name => {

    const el = document.getElementById(name);

    if (el) {

        el.addEventListener("change", () => {
            localStorage.setItem("form_" + name, el.checked);
        });

    }

});

// RESTAURATION DES VALEURS AU CHARGEMENT
window.addEventListener("DOMContentLoaded", () => {

    // TEXT + SELECT
    textFields.forEach(name => {

        const el = document.querySelector(`[name="${name}"]`);
        const saved = localStorage.getItem("form_" + name);

        if (el && saved !== null) {
            el.value = saved;
        }

    });

    // CHECKBOX
    courseFields.forEach(name => {

        const el = document.getElementById(name);
        const saved = localStorage.getItem("form_" + name);

        if (el && saved !== null) {
            el.checked = (saved === "true");
        }

    });

    calculerPrix();

});

</script>

</body>
</html>