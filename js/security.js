// public/js/security.js

// Disable right-click
document.addEventListener('contextmenu', event => event.preventDefault());

// Disable text selection and copy-paste
document.addEventListener('copy', event => event.preventDefault());
document.addEventListener('cut', event => event.preventDefault());
document.addEventListener('paste', event => event.preventDefault());