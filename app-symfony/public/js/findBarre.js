document.addEventListener("turbo:load", function () {
    /**
     * On récupère l'input de la barre de recherche
     * On crée une div dans la qu'elle on mettera les résultats.
     * On lui ajoute un style et la rajoute dans la vue
     */
    let searchInput = document.querySelector("#search");
    let resultBox = document.createElement("div");
    resultBox.className = "absolute bg-white border rounded shadow w-full max-w-md mt-2 p-2 hidden";
    searchInput.parentNode.appendChild(resultBox);

    /* On défini un timer */
    let typingTimer;
    const delay = 1000;

    /* AbortController */
    let controller;

    searchInput.addEventListener("input", function () {
                    clearTimeout(typingTimer);
        typingTimer = setTimeout(() => { //On lance la requet après une sec
            if (controller) controller.abort(); // Annule la requête en cours
            controller = new AbortController(); // Crée une nouvelle instance
            const { signal } = controller;

            
            let query = searchInput.value.trim();
            /* On regarde si la valeur dans le champ fait plus de 2 */
            if (query.length > 2) {
                fetch(`/app_search/${query}`, { signal })
                    .then(response => response.json())
                    .then(data => {
                        console.log(1);
                        resultBox.innerHTML = "";
                        resultBox.classList.add("max-h-72", "overflow-y-auto", "scrollbar-thin", "scrollbar-thumb-gray-400", "scrollbar-track-gray-200");
                        resultBox.classList.remove("hidden");

                        /**
                         * Si erreur on l'affiche, sinon
                         * On crée le lien des éléments trouvé
                         */
                        if (data.error) {

                            resultBox.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                        } else {
                            // Affichage des utilisateurs s'ils existent
                            if (data.users.length > 0) {
                                let userTitle = document.createElement("h3");
                                userTitle.textContent = "Utilisateurs trouvés :";
                                userTitle.className = "font-bold text-gray-800 mt-2";
                                resultBox.appendChild(userTitle);

                                data.users.forEach(user => {
                                    let userItem = document.createElement("a");
                                    userItem.className = "block text-gray-700 hover:bg-gray-200 p-2 cursor-pointer";
                                    userItem.textContent = `${user.name} (ID: ${user.id})`;
                                    userItem.href = `/profil/${user.id}`;
                                    userItem.style.textDecoration = "none";
                                    resultBox.appendChild(userItem);
                                });

                                let separator = document.createElement("hr"); // Ligne de séparation entre Users et Sessions
                                separator.className = "my-2 border-gray-300";
                                resultBox.appendChild(separator);
                            }

                            // Affichage des sessions si elles existent
                            if (data.sessions.length > 0) {
                                let sessionTitle = document.createElement("h3");
                                sessionTitle.textContent = "Sessions trouvées :";
                                sessionTitle.className = "font-bold text-gray-800 mt-2";
                                resultBox.appendChild(sessionTitle);

                                data.sessions.forEach(session => {
                                    let sessionItem = document.createElement("a");
                                    sessionItem.className = "block text-blue-700 hover:bg-blue-200 p-2 cursor-pointer";
                                    sessionItem.textContent = `${session.sessionName} (ID: ${session.id})`;
                                    sessionItem.href = `/session/${session.id}`; // Adapté pour pointer vers une session
                                    sessionItem.style.textDecoration = "none";
                                    resultBox.appendChild(sessionItem);
                                });
                            }
                        }
                    })
                    /* En cas d'erreur non gérer et les récupère ici et les affiches */
                    .catch(error => {
                        if (error.name === "AbortError") {
                            console.log("Requête annulée (nouvelle entrée détectée)");
                        } else {
                            resultBox.innerHTML = `<p class="text-red-500">Erreur de recherche</p>`;
                            resultBox.classList.remove("hidden");
                        }
                    });
            } else {
                resultBox.classList.add("hidden");
            }
            }, delay);
    });
});
