const items = document.querySelectorAll(".accommodatie");
const accFilters = document.querySelectorAll('input[name="accommodatie"]');
const ligFilters = document.querySelectorAll('input[name="ligging"]');
const resultText = document.getElementById("resultaatTekst");

const filter = () => {
  if (!items.length) return;

  const acc = document.querySelector('input[name="accommodatie"]:checked');
  const lig = document.querySelector('input[name="ligging"]:checked');

  let count = 0;

  items.forEach((item) => {
    let show = true;

    if (acc && item.dataset.type !== acc.value) show = false;
    if (lig && item.dataset.ligging !== lig.value) show = false;

    item.style.display = show ? "flex" : "none";

    if (show) count += 1;
  });

  if (resultText) {
    resultText.textContent = `${count} beschikbaarheden gevonden`;
  }
};

if (accFilters.length || ligFilters.length) {
  accFilters.forEach((filterInput) => filterInput.addEventListener("change", filter));
  ligFilters.forEach((filterInput) => filterInput.addEventListener("change", filter));
  filter();
}

const dateStart = document.getElementById("datestart");
if (dateStart) {
  const vandaag = new Date();
  const jaar = vandaag.getFullYear();
  const maand = String(vandaag.getMonth() + 1).padStart(2, "0");
  const dag = String(vandaag.getDate()).padStart(2, "0");
  dateStart.value = `${jaar}-${maand}-${dag}`;
}
