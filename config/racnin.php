<?php

/*
 * Every drink comes in the same two cup sizes, so the size picker is
 * built once and reused with each drink's own prices.
 */
$sizes = fn (int $regular, int $large): array => [
    'label' => 'Size',
    'choices' => ['16 oz' => $regular, '22 oz' => $large],
];

$single = fn (int $price): array => [
    'label' => null,
    'choices' => ['Regular' => $price],
];

$flavors = fn (string $label, array $names, int $price): array => [
    'label' => $label,
    'choices' => array_fill_keys($names, $price),
];

return [

    /*
    |--------------------------------------------------------------------------
    | Shop Details
    |--------------------------------------------------------------------------
    |
    | Static details shown across the landing page and used to build the
    | order message. "messenger" should be the page's m.me link; while it
    | is null, the "Send on Messenger" button opens the Facebook page.
    |
    */

    'shop' => [
        'name' => 'RacNin Drink Series',
        'tagline' => 'Drink Your Own Story',
        'callout' => 'Dalikyat na!',
        'motto' => 'Sarap na, sulit pa!',
        'address' => [
            'street' => 'Juan Luna Street',
            'area' => 'Poblacion 1',
            'city' => 'Hindang, Leyte',
            'zip' => '6423',
            'landmark' => 'Beside 7-Eleven',
        ],
        'hours' => [
            'open' => '10:00',
            'close' => '21:00',
            'label' => '10 AM – 9 PM',
        ],
        'timezone' => 'Asia/Manila',
        'email' => 'bongoc.regine@gmail.com',
        'facebook' => 'https://www.facebook.com/search/top?q=RacNin%20Drink%20Series',
        'messenger' => null,
        'maps' => 'https://www.google.com/maps/search/?api=1&query=Juan+Luna+Street+Poblacion+1+Hindang+Leyte',
        'map_embed' => 'https://maps.google.com/maps?q=7-Eleven%20Hindang%2C%20Leyte&z=17&output=embed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Account
    |--------------------------------------------------------------------------
    |
    | The first admin the database seeder creates outside local development,
    | so a shared default password never reaches a live site.
    |
    */

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Categories
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'iced-coffee' => 'Iced Coffee',
        'non-coffee' => 'Non-Coffee',
        'frappe' => 'Frappe',
        'fruit-soda' => 'Fruit Soda',
        'rice-meals' => 'Rice Meals',
        'snacks' => 'Snacks',
        'combos' => 'Combos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Prices are in pesos. "tint" colors the card behind each illustration
    | and "art" describes the illustration itself (see MenuArt component).
    | Liquid layers are listed bottom to top with the share of the cup.
    |
    */

    'items' => [

        'cafe-mocha' => [
            'name' => 'Cafe Mocha',
            'category' => 'iced-coffee',
            'description' => 'Espresso, rich chocolate and fresh milk poured over ice.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#9a6440',
            'art' => ['type' => 'iced', 'layers' => [['#4a2716', 0.3], ['#8a5535', 0.35], ['#e2c3a0', 0.35]]],
        ],

        'caramel-macchiato' => [
            'name' => 'Caramel Macchiato',
            'category' => 'iced-coffee',
            'description' => 'Vanilla milk, a bold espresso float and a caramel drizzle.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#d08a3c',
            'art' => ['type' => 'iced', 'layers' => [['#f0dcc0', 0.5], ['#c58a4a', 0.2], ['#6b3a1e', 0.3]]],
        ],

        'spanish-latte' => [
            'name' => 'Spanish Latte',
            'category' => 'iced-coffee',
            'description' => 'Sweet, creamy and smooth. Our condensed-milk latte.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#c99566',
            'art' => ['type' => 'iced', 'layers' => [['#f5e6cf', 0.35], ['#c99566', 0.65]]],
        ],

        'dirty-matcha' => [
            'name' => 'Dirty Matcha',
            'category' => 'iced-coffee',
            'description' => 'Earthy matcha milk crowned with a shot of espresso.',
            'options' => $sizes(45, 55),
            'badges' => ['bestseller'],
            'tint' => '#8fa84c',
            'art' => ['type' => 'iced', 'layers' => [['#7f9a3e', 0.42], ['#eef0dc', 0.3], ['#6b3a1e', 0.28]]],
        ],

        'strawberry-milk' => [
            'name' => 'Strawberry Milk',
            'category' => 'non-coffee',
            'description' => 'Strawberry jam swirled into cold fresh milk. Pink and proud.',
            'options' => $sizes(39, 49),
            'badges' => ['bestseller'],
            'tint' => '#e0587a',
            'art' => ['type' => 'iced', 'layers' => [['#d33a5a', 0.32], ['#f7c6d2', 0.68]]],
        ],

        'ube-latte' => [
            'name' => 'Ube Latte',
            'category' => 'non-coffee',
            'description' => 'Purple yam goodness, creamy and just sweet enough.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#8a5bb0',
            'art' => ['type' => 'iced', 'layers' => [['#6a3a8c', 0.3], ['#c6a8e0', 0.7]]],
        ],

        'cookies-and-cream' => [
            'name' => 'Cookies & Cream',
            'category' => 'non-coffee',
            'description' => 'Crushed chocolate cookies stirred into sweet, cold milk.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#6b5d55',
            'art' => ['type' => 'iced', 'layers' => [['#3a302b', 0.18], ['#ece6dd', 0.82]], 'speckle' => '#2f2622'],
        ],

        'choco-berry' => [
            'name' => 'Choco Berry',
            'category' => 'non-coffee',
            'description' => 'Chocolate milk meets strawberry. A sweet little duo.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#b0475a',
            'art' => ['type' => 'iced', 'layers' => [['#d33a5a', 0.25], ['#5c331f', 0.3], ['#caa08a', 0.45]]],
        ],

        'chocolate' => [
            'name' => 'Signature Chocolate',
            'category' => 'non-coffee',
            'description' => 'Thick, creamy chocolate milk for the chocolate lover.',
            'options' => $sizes(39, 49),
            'badges' => [],
            'tint' => '#7a4a2b',
            'art' => ['type' => 'iced', 'layers' => [['#4a2716', 0.45], ['#9b6644', 0.55]]],
        ],

        'caramel-frappe' => [
            'name' => 'Caramel Frappe',
            'category' => 'frappe',
            'description' => 'The cup on our logo. Blended caramel, whipped cream, extra drizzle.',
            'options' => $sizes(49, 59),
            'badges' => ['signature'],
            'tint' => '#ee7f1a',
            'art' => ['type' => 'frappe', 'layers' => [['#c9781f', 0.25], ['#f7a826', 0.75]], 'whip' => '#f6dcc0', 'drizzle' => '#a65a12'],
        ],

        'chocolate-frappe' => [
            'name' => 'Chocolate Frappe',
            'category' => 'frappe',
            'description' => 'Rich, creamy and indulgent. Blended ice-cold with a choco drizzle.',
            'options' => $sizes(49, 59),
            'badges' => ['bestseller'],
            'tint' => '#6e4127',
            'art' => ['type' => 'frappe', 'layers' => [['#5b331f', 0.2], ['#8d5b3d', 0.8]], 'whip' => '#fff3e4', 'drizzle' => '#4a2716'],
        ],

        'strawberry-frappe' => [
            'name' => 'Strawberry Frappe',
            'category' => 'frappe',
            'description' => 'Everything berry. Blended strawberries and cream under a cloud of whip.',
            'options' => $sizes(49, 59),
            'badges' => [],
            'tint' => '#e0587a',
            'art' => ['type' => 'frappe', 'layers' => [['#d33a5a', 0.2], ['#f4a7b9', 0.8]], 'whip' => '#fff0f3', 'drizzle' => '#d33a5a'],
        ],

        'cookies-and-cream-frappe' => [
            'name' => 'Cookies & Cream Frappe',
            'category' => 'frappe',
            'description' => 'Blended cookies and cream with a whipped topping and crumbs.',
            'options' => $sizes(49, 59),
            'badges' => [],
            'tint' => '#5d524c',
            'art' => ['type' => 'frappe', 'layers' => [['#e6dfd5', 1]], 'speckle' => '#2f2622', 'whip' => '#ffffff', 'drizzle' => '#2f2622'],
        ],

        'mango-soda' => [
            'name' => 'Mango Fruit Soda',
            'category' => 'fruit-soda',
            'description' => 'Sweet mango over fizzy soda and ice. Sunshine in a cup.',
            'options' => $sizes(29, 39),
            'badges' => [],
            'tint' => '#f7a826',
            'art' => ['type' => 'soda', 'layers' => [['#f08a1c', 0.35], ['#fcc94a', 0.65]]],
        ],

        'strawberry-soda' => [
            'name' => 'Strawberry Fruit Soda',
            'category' => 'fruit-soda',
            'description' => 'Bright strawberry fizz. The best partner for siomai.',
            'options' => $sizes(29, 39),
            'badges' => ['bestseller'],
            'tint' => '#e8506b',
            'art' => ['type' => 'soda', 'layers' => [['#c92a4a', 0.35], ['#f27a92', 0.65]]],
        ],

        'green-apple-soda' => [
            'name' => 'Green Apple Fruit Soda',
            'category' => 'fruit-soda',
            'description' => 'Crisp, tangy green apple with plenty of bubbles.',
            'options' => $sizes(29, 39),
            'badges' => [],
            'tint' => '#7fb241',
            'art' => ['type' => 'soda', 'layers' => [['#5f9a2a', 0.35], ['#a8d65f', 0.65]]],
        ],

        'blueberry-soda' => [
            'name' => 'Blueberry Fruit Soda',
            'category' => 'fruit-soda',
            'description' => 'Deep blueberry fizz that tastes as good as it looks.',
            'options' => $sizes(29, 39),
            'badges' => [],
            'tint' => '#6a5acd',
            'art' => ['type' => 'soda', 'layers' => [['#4b3a9e', 0.35], ['#9b8cf0', 0.65]]],
        ],

        'lumpia-silog' => [
            'name' => 'Lumpia Silog',
            'category' => 'rice-meals',
            'description' => 'Crispy lumpia, garlic rice and a sunny-side-up egg. Simple, satisfying, addictive.',
            'options' => $single(50),
            'badges' => ['new'],
            'tint' => '#f7a826',
            'art' => ['type' => 'plate', 'side' => 'lumpia'],
        ],

        'siomai-rice' => [
            'name' => 'Siomai Rice Meal',
            'category' => 'rice-meals',
            'description' => 'Three siomai, garlic rice and egg. Simple, satisfying, sarap.',
            'options' => $single(45),
            'badges' => [],
            'tint' => '#a8a754',
            'art' => ['type' => 'plate', 'side' => 'siomai'],
        ],

        'buldak-carbonara' => [
            'name' => 'Buldak Carbonara',
            'category' => 'rice-meals',
            'description' => 'Creamy, cheesy, spicy Korean noodles. Not for the weak.',
            'options' => $single(79),
            'badges' => ['spicy'],
            'tint' => '#e8501e',
            'art' => ['type' => 'noodles'],
        ],

        'siomai-sa-tisa' => [
            'name' => 'Siomai sa Tisa',
            'category' => 'snacks',
            'description' => 'Four Cebu-style siomai with chili-garlic oil and calamansi.',
            'options' => $single(25),
            'badges' => ['sulit'],
            'tint' => '#c9361e',
            'art' => ['type' => 'tisa'],
        ],

        'fries' => [
            'name' => 'Flavored Fries',
            'category' => 'snacks',
            'description' => 'Crispy fries shaken with your favorite flavor.',
            'options' => $flavors('Flavor', ['Cheese', 'Sour Cream', 'BBQ'], 35),
            'badges' => [],
            'tint' => '#f7a826',
            'art' => ['type' => 'fries'],
        ],

        'nachos' => [
            'name' => 'Nachos',
            'category' => 'snacks',
            'description' => 'Crunchy chips, cheese sauce and all the good toppings.',
            'options' => $single(45),
            'badges' => [],
            'tint' => '#e0a030',
            'art' => ['type' => 'nachos'],
        ],

        'dried-veggies' => [
            'name' => 'Crispy Dried Veggies',
            'category' => 'snacks',
            'description' => 'Dried mixed fruits and veggies. Real veggies, real crunch.',
            'options' => $single(35),
            'badges' => [],
            'tint' => '#6f8f3a',
            'art' => ['type' => 'pouch'],
        ],

        'combo-soda-siomai' => [
            'name' => 'Soda + Siomai',
            'category' => 'combos',
            'description' => 'Any 16 oz fruit soda with Siomai sa Tisa. Fresh, flavorful, perfect together.',
            'options' => $flavors('Soda', ['Mango', 'Strawberry', 'Green Apple', 'Blueberry'], 49),
            'regular_price' => 54,
            'badges' => ['sulit'],
            'tint' => '#e8506b',
            'art' => ['type' => 'combo', 'parts' => [
                ['type' => 'soda', 'layers' => [['#c92a4a', 0.35], ['#f27a92', 0.65]]],
                ['type' => 'tisa'],
            ]],
        ],

        'combo-coffee-siomai' => [
            'name' => 'Sip, Savor, Repeat',
            'category' => 'combos',
            'description' => 'Any 16 oz iced coffee with Siomai sa Tisa. Your perfect combo for any mood.',
            'options' => $flavors('Coffee', ['Cafe Mocha', 'Caramel Macchiato', 'Spanish Latte'], 59),
            'regular_price' => 64,
            'badges' => ['sulit'],
            'tint' => '#9a6440',
            'art' => ['type' => 'combo', 'parts' => [
                ['type' => 'iced', 'layers' => [['#f0dcc0', 0.5], ['#c58a4a', 0.2], ['#6b3a1e', 0.3]]],
                ['type' => 'tisa'],
            ]],
        ],

        'combo-frappe-fries' => [
            'name' => 'Frappe + Fries',
            'category' => 'combos',
            'description' => 'Any 16 oz frappe with cheese fries. The after-class reward.',
            'options' => $flavors('Frappe', ['Caramel', 'Chocolate', 'Strawberry', 'Cookies & Cream'], 79),
            'regular_price' => 84,
            'badges' => ['sulit'],
            'tint' => '#6e4127',
            'art' => ['type' => 'combo', 'parts' => [
                ['type' => 'frappe', 'layers' => [['#5b331f', 0.2], ['#8d5b3d', 0.8]], 'whip' => '#fff3e4', 'drizzle' => '#4a2716'],
                ['type' => 'fries'],
            ]],
        ],

    ],

];
