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
        'font_pair'      => ['Brand', 'Website fonts', 'select:playfair-inter|baskerville-source|dmserif-dmsans|cormorant-jost', 'playfair-inter', 'Applies to the whole website and the admin panel.'],
        'meta_description' => ['Brand', 'Search engine description', 'textarea', 'Jawai Mumal — hotel, pure-vegetarian Rajasthani restaurant and leopard safaris in Bera, Pali, Rajasthan, in the heart of Jawai leopard country.'],

        'show_open_badge' => ['Brand', 'Show the “Now Open” badge on the home banner', 'select:1|0', '1'],
        'open_label'     => ['Brand', 'Badge text', 'text', 'Now Open'],

        
        
        // ---------------- Stats
        'stat1_value' => ['Highlights', 'Highlight 1 – number', 'text', '50–70'],
        'stat1_label' => ['Highlights', 'Highlight 1 – label', 'text', 'Estimated leopards in the Jawai hills (no official count)'],
        'stat2_value' => ['Highlights', 'Highlight 2 – number', 'text', '1957'],
        'stat2_label' => ['Highlights', 'Highlight 2 – label', 'text', 'Year Jawai Dam was completed'],
        'stat3_value' => ['Highlights', 'Highlight 3 – number', 'text', '300+'],
        'stat3_label' => ['Highlights', 'Highlight 3 – label', 'text', 'Bird species recorded around Jawai'],
        'stat4_value' => ['Highlights', 'Highlight 4 – number', 'text', '2'],
        'stat4_label' => ['Highlights', 'Highlight 4 – label', 'text', 'Safari drives a day: dawn & dusk'],

        
        // ---------------- Contact
        'owner1_name'  => ['Contact', 'Contact 1 – name', 'text', 'Nilesh Singh Chouhan'],
        'owner1_role'  => ['Contact', 'Contact 1 – role', 'text', 'Reservations'],
        'owner1_phone' => ['Contact', 'Contact 1 – phone', 'text', '+91 63679 68353'],
        'owner2_name'  => ['Contact', 'Contact 2 – name', 'text', 'Devendra Singh'],
        'owner2_role'  => ['Contact', 'Contact 2 – role', 'text', 'Reservations'],
        'owner2_phone' => ['Contact', 'Contact 2 – phone', 'text', '+91 95113 09522'],
        'whatsapp'     => ['Contact', 'WhatsApp number (digits only, with country code)', 'text', '916367968353'],
        'email'        => ['Contact', 'Email (leave empty to hide)', 'text', ''],
        'address'      => ['Contact', 'Address', 'textarea', "Near Up-Tehsil Office, Bera Village,\nBera–Bali Road, District Pali,\nRajasthan, India"],
        'map_lat'      => ['Contact', 'Hotel latitude', 'text', '25.0011888', 'Currently set to the centre of Bera village. For the exact pin: in Google Maps press and hold on the hotel, copy the two numbers shown, and paste them here. All maps, directions and distances use these.'],
        'map_lng'      => ['Contact', 'Hotel longitude', 'text', '73.1774854'],
        'map_query'    => ['Contact', 'Map search text (used when coordinates are empty)', 'text', 'Bera, Pali, Rajasthan'],
        'reception_hours' => ['Contact', 'Reception hours', 'text', 'Open daily · 7:00 AM – 11:00 PM'],

        // ---------------- Social
        'instagram' => ['Social links', 'Instagram link', 'url', ''],
        'facebook'  => ['Social links', 'Facebook link', 'url', ''],

        // ---------------- Booking rules
        'tax_percent'   => ['Booking rules', 'GST % on room tariff up to the limit below', 'number', '5', 'Please confirm the current rates with your accountant.'],
        'tax_threshold' => ['Booking rules', 'Tariff limit per night (₹)', 'number', '7500'],
        'tax_percent_high' => ['Booking rules', 'GST % on room tariff above the limit', 'number', '18'],
        'notify_email'  => ['Booking rules', 'Email me every new booking request (optional)', 'text', '', 'Needs mail to be enabled on your hosting. WhatsApp and the admin Bookings page always work.'],
        'checkin_time'  => ['Booking rules', 'Check-in time', 'text', '12:00 PM'],
        'checkout_time' => ['Booking rules', 'Check-out time', 'text', '11:00 AM'],
        'max_guests'    => ['Booking rules', 'Max guests per booking', 'number', '12'],
        'dine_slots'    => ['Booking rules', 'Dining time slots (comma separated)', 'text', '08:00 AM, 12:30 PM, 01:30 PM, 07:30 PM, 08:30 PM, 09:30 PM'],

        // ---------------- Facilities
        'facilities'    => ['Facilities', 'Facilities list (one per line: icon|Title|Text)', 'textarea',
"bed|Comfortable rooms|Clean, well-lit rooms with proper beds and hot water
utensils|Vegetarian restaurant & café|Pure-veg Rajasthani village food, tandoor dishes and café
wifi|Free Wi-Fi|Stay connected across the property
car-park|Parking|Safe parking for cars, jeeps and bikes
compass|Safari desk|Leopard safaris and village visits arranged by us
party|Party hall|Space for celebrations and private functions"],

        // ---------------- Footer
        'footer_text'   => ['Footer', 'Footer blurb', 'textarea', 'Good food, comfortable stays and happy moments in the heart of Jawai leopard country.'],
    ];
}
