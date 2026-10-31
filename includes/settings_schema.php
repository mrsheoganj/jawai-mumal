<?php
/**
 * Every editable piece of site content lives here. key => [group, label, type, default, help]
 * Types: text, textarea, image, number, select:val1|val2, url
 * The admin "Site Content" screen is generated from this list, so adding a row here makes it editable.
 */
function settings_schema(): array {
    return [
        // ---------------- Brand
        'site_name'      => ['Brand', 'Business name', 'text', 'Jawai Mumal'],
        'site_tagline'   => ['Brand', 'Tagline', 'text', 'Hotel · Restaurant · Safari'],
        'site_url'       => ['Brand', 'Website address', 'url', 'https://jawaimumal.com'],
        'logo_dark'      => ['Brand', 'Logo for light backgrounds (transparent PNG)', 'image', 'assets/brand/wordmark-dark.png'],
        'logo_light'     => ['Brand', 'Logo for dark backgrounds (transparent PNG)', 'image', 'assets/brand/wordmark-light.png'],
        'logo_emblem'    => ['Brand', 'Round emblem logo', 'image', 'assets/brand/emblem-512.png'],
        'meta_description' => ['Brand', 'Search engine description', 'textarea', 'Jawai Mumal — hotel, restaurant and leopard safari experiences in Bera, Pali, Rajasthan, the heart of Jawai leopard country.'],

        // ---------------- Opening status
        'opening_soon'   => ['Opening status', 'Show "Opening Soon" mode', 'select:1|0', '1', '1 = bookings are collected as pre-booking requests. Set to 0 once the hotel is open.'],
        'opening_label'  => ['Opening status', 'Badge text', 'text', 'Opening Soon'],
        'opening_note'   => ['Opening status', 'Notice shown near booking forms', 'textarea', 'We are putting the finishing touches on the property. Send a pre-booking request and our team will confirm availability and rates personally.'],

        // ---------------- Hero
        'hero_eyebrow'   => ['Home banner', 'Small line above title', 'text', 'Bera · Jawai · Rajasthan'],
        'hero_title'     => ['Home banner', 'Main title', 'text', 'Stay close to the wild side of Rajasthan'],
        'hero_text'      => ['Home banner', 'Short description', 'textarea', 'A new boutique hotel and restaurant at the edge of Jawai leopard country. Comfortable rooms, honest Rajasthani food and guided safaris through granite hills.'],
        'hero_image'     => ['Home banner', 'Banner image', 'image', 'assets/images/hero-savanna.jpg'],

        // ---------------- About
        'about_title'    => ['About', 'Heading', 'text', 'A new home in Jawai country'],
        'about_text'     => ['About', 'First paragraph', 'textarea', 'Jawai Mumal is a family-run hotel and restaurant in Bera village on the Bera–Bali road, a short drive from the granite hills where leopards share the land with Rabari herders. We built it for travellers, families and celebrations that want comfort without losing the character of the place.'],
        'about_text2'    => ['About', 'Second paragraph', 'textarea', 'Good food, a comfortable stay and happy moments — a place where every guest feels special. Our team can also arrange your Jawai safari, village visits and private functions.'],
        'about_image'    => ['About', 'Second hotel image (evening view)', 'image', 'assets/images/hotel-front.jpg'],
        'property_image' => ['About', 'Main hotel photo (daytime, clean view)', 'image', 'assets/images/hotel-day.jpg'],
        'founders_line'  => ['About', 'Founders line', 'text', 'Founded by Devendra Singh & Nilesh Singh Chouhan'],

        // ---------------- Stats
        'stat1_value' => ['Highlights', 'Highlight 1 – number', 'text', '50+'],
        'stat1_label' => ['Highlights', 'Highlight 1 – label', 'text', 'Leopards roam the Jawai hills'],
        'stat2_value' => ['Highlights', 'Highlight 2 – number', 'text', '3'],
        'stat2_label' => ['Highlights', 'Highlight 2 – label', 'text', 'Experiences under one roof'],
        'stat3_value' => ['Highlights', 'Highlight 3 – number', 'text', 'Bera'],
        'stat3_label' => ['Highlights', 'Highlight 3 – label', 'text', 'On the Bera–Bali road'],
        'stat4_value' => ['Highlights', 'Highlight 4 – number', 'text', '24/7'],
        'stat4_label' => ['Highlights', 'Highlight 4 – label', 'text', 'Guest support on call'],

        // ---------------- Events
        'events_title' => ['Events', 'Heading', 'text', 'Parties, celebrations & private functions'],
        'events_text'  => ['Events', 'Description', 'textarea', 'Birthdays, family get-togethers, small weddings, corporate meets or a quiet personal celebration — party and personal bookings are both available. Tell us your date, guest count and menu ideas and we will plan the rest.'],
        'events_image' => ['Events', 'Image', 'image', 'assets/images/dining-hall.jpg'],

        // ---------------- Contact
        'owner1_name'  => ['Contact', 'Owner 1 – name', 'text', 'Nilesh Singh Chouhan'],
        'owner1_role'  => ['Contact', 'Owner 1 – role', 'text', 'Owner'],
        'owner1_phone' => ['Contact', 'Owner 1 – phone', 'text', '+91 63679 68353'],
        'owner2_name'  => ['Contact', 'Owner 2 – name', 'text', 'Devendra Singh'],
        'owner2_role'  => ['Contact', 'Owner 2 – role', 'text', 'Owner'],
        'owner2_phone' => ['Contact', 'Owner 2 – phone', 'text', '+91 95113 09522'],
        'whatsapp'     => ['Contact', 'WhatsApp number (digits only, with country code)', 'text', '916367968353'],
        'email'        => ['Contact', 'Email (leave empty to hide)', 'text', ''],
        'address'      => ['Contact', 'Address', 'textarea', "Near Up-Tehsil Office, Bera Village,\nBera–Bali Road, District Pali,\nRajasthan, India"],
        'map_query'    => ['Contact', 'Map search text', 'text', 'Bera, Pali, Rajasthan'],
        'reception_hours' => ['Contact', 'Reception hours', 'text', 'Open daily · 7:00 AM – 11:00 PM'],

        // ---------------- Social
        'instagram' => ['Social links', 'Instagram link', 'url', ''],
        'facebook'  => ['Social links', 'Facebook link', 'url', ''],

        // ---------------- Booking rules
        'tax_percent'   => ['Booking rules', 'GST % added to stays', 'number', '12'],
        'checkin_time'  => ['Booking rules', 'Check-in time', 'text', '12:00 PM'],
        'checkout_time' => ['Booking rules', 'Check-out time', 'text', '11:00 AM'],
        'max_guests'    => ['Booking rules', 'Max guests per booking', 'number', '12'],
        'dine_slots'    => ['Booking rules', 'Dining time slots (comma separated)', 'text', '08:00 AM, 12:30 PM, 01:30 PM, 07:30 PM, 08:30 PM, 09:30 PM'],

        // ---------------- Facilities
        'facilities'    => ['Facilities', 'Facilities list (one per line: icon|Title|Text)', 'textarea',
"bed|Comfortable rooms|Clean, well-lit rooms with proper beds and hot water
utensils|Restaurant & café|Rajasthani classics, grills and all-day café
wifi|Free Wi-Fi|Stay connected across the property
car-park|Parking|Safe parking for cars, jeeps and bikes
compass|Safari desk|Leopard safari and village trips arranged
party|Party hall|Space for celebrations and private functions"],

        // ---------------- Footer
        'footer_text'   => ['Footer', 'Footer blurb', 'textarea', 'Good food, comfortable stays and happy moments in the heart of Jawai leopard country.'],
    ];
}
