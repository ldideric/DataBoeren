const boekButton = document.getElementById("boekBtn");
const annuleerButton = document.getElementById("annuleerBtn");
const annuleerForm = document.getElementById("annuleerForm");
const annuleerEmail = document.getElementById("annuleerEmail");

if (annuleerForm) {
  annuleerForm.addEventListener("submit", (event) => {
    if (!annuleerEmail || !annuleerEmail.value) {
      event.preventDefault();
      alert("Voer alstublieft een e-mailadres in");
      return;
    }

    if (!confirm("Weet u zeker dat u wilt annuleren?")) {
      event.preventDefault();
      return;
    }

    alert("Uw registratie is geannuleerd.\nU krijgt binnen een paar minuten een bevestigingsmail.");
  });
}
