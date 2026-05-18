@extends('layouts.app')
<!DOCTYPE html>
<html lang="nl">
@section('header')
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Camping Voorkeuren</title>

  <link rel="stylesheet" href="boekstyle.css">
</head>
@endsection
@section('content')
<body>

  <div class="container">

    <aside class="sidebar">

      
 <div class="top-space">

  <label for="datestart">Start datum</label>

  <input 
    type="date" 
    id="datestart" 
    name="datestart" 
    
    min="2026-05-18"

  >   

</div>

      <h1>Voorkeuren</h1>

    
      <div class="card">

        <h2>Accommodatie type</h2>

        <p class="info-text">
          Je mag maar één accommodatie type selecteren.
        </p>

        <label class="option">
          <input type="radio" name="accommodatie" value="Trekkersveldje">
          <span>Trekkersveldje</span>
        </label>

        <label class="option">
          <input type="radio" name="accommodatie" value="Camper">
          <span>Camper</span>
        </label>

        <label class="option">
          <input type="radio" name="accommodatie" value="Caravan">
          <span>Caravan</span>
        </label>

        <label class="option">
          <input type="radio" name="accommodatie" value="Tent">
          <span>Tent</span>
        </label>

      </div>

    
      <div class="card">

        <h2>Ligging</h2>

        <p class="info-text">
          Je mag maar één ligging voorkeur selecteren.
        </p>

        <label class="option">
          <input type="radio" name="ligging" value="Dichter bij het water">
          <span>Dichter bij het water</span>
        </label>

        <label class="option">
          <input type="radio" name="ligging" value="Dichtbij de speeltuin">
          <span>Dichtbij de speeltuin</span>
        </label>

      </div>

      {{-- <button id="toonVoorkeuren">
        Bekijk voorkeuren
      </button> --}}

    </aside>

<main class="content">

  <h2>Camping Boekingspagina</h2>

  {{-- <p class="subtitle">
    Bekijk alle beschikbare accommodaties
  </p> --}}

  <div id="resultaatTekst" class="resultaat-tekst">
    20 beschikbaarheden gevonden
  </div>

  <div class="accommodaties">

  <div class="accommodatie" data-type="Trekkersveldje" data-ligging="Water">
    <img src="https://images.unsplash.com/photo-1504851149312-7a075b496cc7?q=80&w=1200">

    <div class="info">
      <h3>Trekkersveldje</h3>
      <p class="beschrijving">
        Kleine rustige natuurplekken, ideaal voor wandelaars en fietsers.
      </p>
      <p class="extra">✔ Natuur • ✔ Budget • ✔ Rustig</p>
      <p class="beschikbaar">Nog <strong>5</strong> plekken beschikbaar</p>
    </div>

    <div class="prijs">vanaf €21</div>
  </div>

  <div class="accommodatie" data-type="Camper" data-ligging="Water">
    <img src="https://images.unsplash.com/photo-1516939884455-1445c8652f83?q=80&w=1200">

    <div class="info">
      <h3>Camperplaats</h3>
      <p class="beschrijving">
        Ruime camperplaatsen met stroom en uitzicht op water.
      </p>
      <p class="extra">✔ Stroom • ✔ Ruim • ✔ Comfort</p>
      <p class="beschikbaar">Nog <strong>5</strong> plekken beschikbaar</p>
    </div>

    <div class="prijs">vanaf €38</div>
  </div>

  <div class="accommodatie" data-type="Caravan" data-ligging="Speeltuin">
    <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=1200">

    <div class="info">
      <h3>Caravanplaatsen</h3>
      <p class="beschrijving">
        Comfortabele vaste staanplaatsen dichtbij faciliteiten.
      </p>
      <p class="extra">✔ Vast • ✔ Comfort • ✔ Familie</p>
      <p class="beschikbaar">Nog <strong>5</strong> plekken beschikbaar</p>
    </div>

    <div class="prijs">vanaf €33</div>
  </div>

  <div class="accommodatie" data-type="Tent" data-ligging="Water">
    <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?q=80&w=1200">

    <div class="info">
      <h3>Tentplekken</h3>
      <p class="beschrijving">
        Natuurlijke grasvelden met mooie plekken in de natuur.
      </p>
      <p class="extra">✔ Gras • ✔ Natuur • ✔ Rust</p>
      <p class="beschikbaar">Nog <strong>5</strong> plekken beschikbaar</p>
    </div>

    <div class="prijs">vanaf €18</div>
  </div>

</div>

</main>


  </div>

  <script src="boekenscript.js"></script>

</body>
@endsection
</html>