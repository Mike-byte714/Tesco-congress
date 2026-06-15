// ========== CUENTA REGRESIVA ==========
function updateCountdown() {
    const eventDate = new Date("Nov 15, 2027 09:00:00").getTime();
    const now = new Date().getTime();
    const diff = eventDate - now;
    if (diff < 0) {
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) countdownElement.innerHTML = "<h3>Evento iniciado</h3>";
        return;
    }
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (86400000)) / (3600000));
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    
    if (daysEl) daysEl.innerText = days;
    if (hoursEl) hoursEl.innerText = hours;
    if (minutesEl) minutesEl.innerText = minutes;
    if (secondsEl) secondsEl.innerText = seconds;
}

setInterval(updateCountdown, 1000);
updateCountdown();

// ========== CAMBIO DE IDIOMA ==========
const langSwitcher = document.getElementById('langSwitcher');
if (langSwitcher) {
    langSwitcher.addEventListener('change', function() {
        window.location.href = '../controllers/LangController.php?lang=' + this.value;
    });
}

// ========== ESTADÍSTICAS DINÁMICAS ==========
function loadStats() {
    fetch('../controllers/StatsController.php?action=dashboard')
        .then(res => res.json())
        .then(data => {
            const participantsEl = document.getElementById('totalParticipants');
            const projectsEl = document.getElementById('totalProjects');
            const countriesEl = document.getElementById('totalCountries');
            const institutionsEl = document.getElementById('totalInstitutions');
            
            if (participantsEl) participantsEl.innerText = data.participants || 0;
            if (projectsEl) projectsEl.innerText = data.projects || 0;
            if (countriesEl) countriesEl.innerText = data.countries || 0;
            if (institutionsEl) institutionsEl.innerText = data.institutions || 0;
        })
        .catch(err => console.log("Estadísticas no disponibles aún"));
}

// Cargar estadísticas solo si estamos en la página principal
if (document.getElementById('totalParticipants')) {
    loadStats();
}

// ========== HEADER SCROLL EFFECT ==========
const header = document.querySelector('header');
if (header) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

// ========== MODO OSCURO ==========
const darkModeToggle = document.getElementById('darkModeToggle');
const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

// Función para actualizar el ícono del botón
function updateDarkModeIcon(isDark) {
    if (!darkModeToggle) return;
    const icon = darkModeToggle.querySelector('i');
    if (isDark) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
}

// Verificar preferencia guardada
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    updateDarkModeIcon(true);
} else if (savedTheme === 'light') {
    document.body.classList.remove('dark-mode');
    updateDarkModeIcon(false);
} else if (prefersDarkScheme.matches) {
    // Si no hay preferencia guardada, usar la del sistema
    document.body.classList.add('dark-mode');
    updateDarkModeIcon(true);
}

// Evento click del botón
if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateDarkModeIcon(isDark);
    });
}

// ========== FILTRO DE PROGRAMA (si existe) ==========
const dayFilter = document.getElementById('dayFilter');
if (dayFilter) {
    dayFilter.addEventListener('change', function() {
        let val = this.value;
        document.querySelectorAll('#scheduleList .card').forEach(card => {
            if (val === 'all' || card.getAttribute('data-day') === val) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// ========== INICIALIZAR MAPA (si existe el contenedor) ==========
if (document.getElementById('worldMap')) {
    var map = L.map('worldMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { 
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>' 
    }).addTo(map);
    
    fetch('../controllers/StatsController.php?action=participantsByCountry')
        .then(res => res.json())
        .then(data => {
            if (data && data.length) {
                data.forEach(c => {
                    if (c.lat && c.lng) {
                        L.marker([c.lat, c.lng]).addTo(map)
                            .bindPopup(`${c.country_name}: ${c.count} participantes`);
                    }
                });
            }
        })
        .catch(err => console.log("Mapa no disponible"));
}