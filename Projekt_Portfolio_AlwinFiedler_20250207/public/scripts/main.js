document.addEventListener('DOMContentLoaded', () => { 
    const button = document.getElementById('show-pdf-button');
    const pdfViewer = document.getElementById('pdf-viewer');

    button.addEventListener('click', () => {    
        if (pdfViewer.style.display === 'none') {
            pdfViewer.style.display = 'block';
            button.textContent = 'Lebenslauf ausblenden';
        } else {
            pdfViewer.style.display = 'none';
            button.textContent = 'Lebenslauf anzeigen';
        }
    });
});