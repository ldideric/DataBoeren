@extends('layouts.app')

@section('content')
    <livewire:booking-form
        :campsite="$campsite"
        :check-in="$checkIn->format('Y-m-d')"
        :check-out="$checkOut->format('Y-m-d')"
        :adults="$adults"
        :children="$children"
        :vehicles="$vehicles"
    />
@endsection
