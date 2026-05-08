import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PJAX partial page loading — intercept link clicks & replace only #page-content
import './ajax-router';
