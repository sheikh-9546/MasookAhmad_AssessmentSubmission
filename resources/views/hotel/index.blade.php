<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel room reservation</title>
    @vite(['resources/css/app.css', 'resources/js/hotel.js'])
</head>
<body class="hotel-body">
    <div class="hotel-shell">
        <h1 class="hotel-sr-only">Hotel room reservation</h1>

        <section class="hotel-controls" aria-label="Booking controls">
            <label class="hotel-field">
                <span class="hotel-label">No of Rooms</span>
                <input type="number" id="room-count" min="1" max="{{ \App\Services\HotelBookingService::MAX_ROOMS_PER_BOOKING }}" value="2" inputmode="numeric">
            </label>
            <button type="button" class="hotel-btn hotel-btn-primary" id="btn-book">Book</button>
            <button type="button" class="hotel-btn" id="btn-reset">Reset</button>
            <button type="button" class="hotel-btn" id="btn-random">Random</button>
        </section>

        <p class="hotel-message" id="hotel-message" role="status"></p>

        <div class="hotel-layout" id="hotel-layout">
            <div class="hotel-lift" aria-hidden="true" title="Stairs / lift"></div>
            <div class="hotel-rows" id="hotel-rows"></div>
        </div>

        <footer class="hotel-legend">
            <span><span class="swatch swatch-free"></span> Available</span>
            <span><span class="swatch swatch-blocked"></span> Occupied (random)</span>
            <span><span class="swatch swatch-booked"></span> Your booking</span>
        </footer>
    </div>
</body>
</html>
