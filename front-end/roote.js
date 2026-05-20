document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.section');

    // Fonction pour activer une section et le lien correspondant
    function activateSection(sectionId) {
        // Cacher toutes les sections
        sections.forEach(section => {
            section.classList.remove('active');
            section.style.display = 'none';
        });

        // Afficher la section demandée
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.add('active');
            targetSection.style.display = 'flex';
        }

        // Retirer la classe active de tous les liens
        navLinks.forEach(link => link.classList.remove('active'));

        // Activer le lien correspondant
        const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }

        // Sauvegarder dans localStorage
        localStorage.setItem('currentSection', sectionId);
    }

    // Gestion du clic sur les liens
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const sectionId = link.getAttribute('href').substring(1); // enlève le #

            if (sectionId) {
                e.preventDefault();        // Empêche le saut brutal
                activateSection(sectionId);
                
                // Optionnel : mettre à jour l'URL sans recharger
                history.pushState(null, null, `#${sectionId}`);
            }
        });
    });

    // Récupérer la dernière section au chargement
    function loadLastSection() {
        const savedSection = localStorage.getItem('currentSection');
        const hashSection = window.location.hash.substring(1);

        let sectionToLoad = 'section-ajout'; // Section par défaut

        if (hashSection && document.getElementById(hashSection)) {
            sectionToLoad = hashSection;
        } else if (savedSection && document.getElementById(savedSection)) {
            sectionToLoad = savedSection;
        }

        activateSection(sectionToLoad);
    }

    // Charger la section au démarrage
    loadLastSection();

    // Gérer le bouton précédent / suivant du navigateur
    window.addEventListener('hashchange', () => {
        const hashSection = window.location.hash.substring(1);
        if (hashSection && document.getElementById(hashSection)) {
            activateSection(hashSection);
        }
    });
});