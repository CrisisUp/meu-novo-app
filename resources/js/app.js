import './bootstrap';

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

Alpine.plugin(mask);

// Configuração do Modo Escuro Global via Store
Alpine.store('theme', {
    darkMode: localStorage.getItem('theme') === 'dark',
 
    init() {
        // Aplica a classe no início
        this.apply();
    },
 
    toggle() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        this.apply();
    },

    apply() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

window.Alpine = Alpine;
Alpine.start();
