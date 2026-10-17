@props(['shop', 'menu'])

@php
    $address = $shop['address'];
    [$cheapest, $priciest] = $menu->priceRange();

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'CafeOrCoffeeShop',
        'name' => $shop['name'],
        'slogan' => $shop['tagline'],
        'email' => $shop['email'],
        'image' => asset('images/logo-mark.png'),
        'priceRange' => "₱{$cheapest}–₱{$priciest}",
        'servesCuisine' => ['Coffee', 'Frappe', 'Fruit Soda', 'Filipino'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => "{$address['street']}, {$address['area']}",
            'addressLocality' => 'Hindang',
            'addressRegion' => 'Leyte',
            'postalCode' => $address['zip'],
            'addressCountry' => 'PH',
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => $shop['hours']['open'],
            'closes' => $shop['hours']['close'],
        ],
    ];
@endphp

<script type="application/ld+json">@json($structuredData)</script>
