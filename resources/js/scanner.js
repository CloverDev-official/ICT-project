// resources/js/scanner.js
import { Html5QrcodeScanner } from "html5-qrcode";

let scanner;
let lastCode = null;

function onScanSuccess(decodedText) {
    if (decodedText === lastCode) return;
    lastCode = decodedText;
    Livewire.dispatch('verifiedQRCode', decodedText);

}

function qrboxFunction (viewfinderWidth, viewfinderHeight) {
    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
    let qrboxSize = Math.floor(minEdgeSize * 1); 
    
    return {
        width: qrboxSize,
        height: qrboxSize
    };
}

window.initScanner = () => {
    scanner = new Html5QrcodeScanner("reader", { 
        fps: 20,
        qrbox: qrboxFunction,
        aspectRatio: 1.0,
        showTorchButtonIfSupported: true
    }, false);

    scanner.render(onScanSuccess);
};