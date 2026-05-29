import "./bootstrap";
// import "./chart";
import "./toastFlash";
import "./scanner";
import "./progress";
import "./generateQR";
import "./generateCard";

document.addEventListener('livewire:init', async () => {
    if (document.getElementById('chart')) {
        await import('./chart.js');
    }
});