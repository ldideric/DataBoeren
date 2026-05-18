@extends('layouts.app')

@section('header')
  Annuleren
@endsection

@section('content')
  <div class="max-w-4xl mx-auto py-8 px-6">
    <div class="flex justify-center items-center min-h-[60vh]">
      <form action="">
        <input type="email" id="emailInput" class="border border-gray-300 rounded-lg p-4 w-full max-w-md" placeholder="Voer uw e-mailadres in" />
        <button type="button" onclick="if(document.getElementById('emailInput').value) { if(confirm('Weet u zeker dat u wilt annuleren?')) { alert('Uw registratie is geannuleerd.' + '\n' + 'U krijgt binnen een paar minuten een bevestigingsmail.'); } } else { alert('Voer alstublieft een e-mailadres in'); }" class="bg-blue-500 p-4 rounded-lg border-2 border-gray-300 cursor-pointer">
          Annuleren
        </button>
      </form>
    </div>
  </div>
@endsection