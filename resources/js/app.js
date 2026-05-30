import "./bootstrap";
import "./toastFlash";
// import "./chart";
// import "./scanner";
// import "./progress";
// import "./generateQR";
// import "./generateCard";

if (document.getElementById("reader")) {
  import("./scanner");
}

if (document.querySelector("[data-generate-qr]")) {
  import("./generateQR");
}

if (document.querySelector("[data-generate-card]")) {
  import("./generateCard");
}

if (document.getElementById("main-murid") || document.getElementById("main-guru")) {
  import("./chart").then(() => window.initCharts?.());
}