const savedTheme = localStorage.getItem('app-theme') || 'blue';
document.documentElement.setAttribute('data-theme', savedTheme);