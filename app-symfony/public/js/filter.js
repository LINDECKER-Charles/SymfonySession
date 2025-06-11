document.addEventListener("turbo:load", function () {
    const inputs = document.querySelectorAll("input");
    const searchInput = document.querySelector('input[name="search"]');
    const container = document.querySelector("#resultats");
    const loader = document.getElementById("loader");

    let typingTimer;
    const delay = 800;
    let controller;

    inputs.forEach(input => {
        input.addEventListener("input", () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                if (controller) controller.abort();
                controller = new AbortController();
                const signal = controller.signal;

                const params = {
                    utilisateurs: document.querySelector('input[value="users"]').checked,
                    sessions: document.querySelector('input[value="sessions"]').checked,
                    ordre: document.querySelector('input[name="order"]:checked')?.value === 'asc',
                    maxRes: parseInt(document.querySelector('input[name="limit"]').value) || 100,
                    search: searchInput?.value.trim() || ''
                };

                const url = `/recherche/${encodeURIComponent(JSON.stringify(params))}`;

                loader.classList.remove("hidden");
                container.classList.add("opacity-50", "pointer-events-none");

                fetch(url, { signal })
                    .then(res => res.json())
                    .then(data => {
                        loader.classList.add("hidden");
                        container.classList.remove("opacity-50", "pointer-events-none");
                        container.innerHTML = "";

                            if (data.length === 0) {
                                const emptyMsg = document.createElement("p");
                                emptyMsg.className = "text-center text-gray-500 italic w-full";
                                emptyMsg.textContent = "Aucune donnée trouvée.";
                                container.appendChild(emptyMsg);
                                return;
                            }

                        data.forEach(item => {
                            const div = document.createElement("div");
                            div.className = `rounded shadow border p-4 w-full sm:w-[48%] md:w-[38%] lg:w-[28%] xl:w-[24%] 2xl:w-[14%] overflow-y-scroll ${
                                item.type === 'user' ? 'bg-blue-50' : 'bg-green-50'
                            }`;

                            if (item.type === 'user') {
                                div.innerHTML = `
                                    <h3 class="text-lg font-bold text-[#3B4C66]">
                                        <a href="/profil/${item.id}">${item.name}</a>
                                    </h3>
                                    <p class="text-sm text-gray-500">👤 Utilisateur</p>
                                    <p class="mt-2">📧 
                                        <a href="mailto:${item.email}" class="text-blue-600 hover:underline hover:text-blue-800 transition">${item.email}</a>
                                    </p>
                                `;
                            } else {
                                div.innerHTML = `
                                    <h3 class="text-lg font-bold text-[#3B4C66]">
                                        <a href="/session/${item.id}">${item.name}</a>
                                    </h3>
                                    <p class="text-sm text-gray-500">📅 Session</p>
                                    <p class="mt-2">👥 ${item.places}</p>
                                `;
                            }

                            container.appendChild(div);
                        });
                    })
                    .catch(err => {
                        loader.classList.add("hidden");
                        container.classList.remove("opacity-50", "pointer-events-none");
                        if (err.name !== "AbortError") {
                            console.error("Erreur lors de la requête : ", err);
                        }
                    });
            }, delay);
        });
    });
});
