function boekNu() {
  alert("Je boeking wordt gestart");
}

function annuleerBoeking() {
  alert("Annuleringspagina wordt geopend...");
}
document.getElementById("boekBtn").addEventListener("click", boekNu);

document.getElementById("annuleerBtn").addEventListener("click", function(event) {
  event.preventDefault();
  annuleerBoeking();
});

