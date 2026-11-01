<?php
/**
 * Starter "Jawai Travel Journal" – 18 search-optimised guides.
 * They are not linked from the menu or footer (unless you switch that on in Admin > SEO), but they are published,
 * included in sitemap.xml and open at /blog and /blog/<slug> so search engines can find and rank them.
 * Every article is editable in Admin > Journal (SEO articles). Facts are kept general; check seasonal details before travel.
 */
function jm_blog_posts(): array {
    $P = [];

    $P[] = ['jawai-leopard-safari-complete-guide', 'Jawai Leopard Safari: The Complete Guide', 'Wildlife', 'assets/images/leopard-log.jpg', '2026-09-01 09:00:00',
        'What a Jawai leopard safari is like, where it happens, how long it lasts, what you will see and how to plan it from Bera.',
        'Jawai Leopard Safari Guide: Timings, Sightings & Tips',
        'Plan a Jawai leopard safari: how it works, best timings, what you will see, how long drives last, what to wear and how to book from Bera, Pali, Rajasthan.',
        'Jawai leopard safari, Jawai safari guide, leopard safari Rajasthan, Jawai Bandh safari, Bera leopard safari, Jawai leopard hills',
        <<<'HTML'
<p>Jawai is one of the few places in India where you can look for wild leopards in daylight, on open granite hills, with villages and farms right next door. A Jawai leopard safari is quieter and more intimate than a big-park drive: small open jeeps, local trackers and long, unhurried scans of the rocks.</p>
<h2>Where the safari happens</h2>
<p>The leopards live on the granite domes and boulder fields around Bera and the Jawai Dam in the Pali district of Rajasthan. These hills are full of caves, rock ledges and small hill temples, which the animals use for shade and shelter. Much of the area is community land where herding families have lived beside leopards for generations.</p>
<h2>How a safari works</h2>
<ul>
<li><strong>Vehicle:</strong> an open 4x4 jeep with a driver and a local tracker.</li>
<li><strong>Timing:</strong> two windows each day – early morning from first light and late afternoon until sunset. Leopards are most active when it is cooler.</li>
<li><strong>Duration:</strong> roughly three hours per drive.</li>
<li><strong>Method:</strong> trackers follow pugmarks, alarm calls from langurs and birds, and the hot rocks leopards like to lie on.</li>
</ul>
<h2>What you may see</h2>
<p>Sightings can never be guaranteed, but Jawai is known for relaxed leopards resting on rocks. You may also see striped hyena, jackal, desert fox, mongoose, marsh crocodiles near the dam and a long list of birds. Estimates put the wider Jawai area at roughly fifty to seventy leopards, though there is no official count.</p>
<h2>What to wear and carry</h2>
<ul>
<li>Muted earth tones (olive, khaki, brown); avoid bright colours and strong perfume.</li>
<li>A scarf or buff for dust, sunglasses, a hat and sunscreen.</li>
<li>A light jacket for winter dawn drives, which can be cold in an open jeep.</li>
<li>Binoculars and a camera with a 200–400 mm lens if you have one.</li>
</ul>
<h2>Booking your safari from Bera</h2>
<p>The easiest way is to stay close to the hills. Our own safaris leave from Bera, so there is no long transfer before sunrise. See the current <a href="{{safari}}">Jawai safari packages</a>, then send your dates on WhatsApp from the <a href="{{book?type=safari}}">booking page</a> and we will confirm availability.</p>
<p>If you are still choosing dates, read our guide to the <a href="{{blog/best-time-to-visit-jawai}}">best time to visit Jawai</a>.</p>
HTML];

    $P[] = ['best-time-to-visit-jawai', 'Best Time to Visit Jawai: Month-by-Month', 'Planning', 'assets/images/hotel-day.jpg', '2026-09-04 09:00:00',
        'Jawai is a year-round destination. Here is what each season feels like and which months suit leopards, birds and comfort.',
        'Best Time to Visit Jawai, Rajasthan: Month-by-Month Guide',
        'The best time to visit Jawai for leopard safaris, birdwatching and comfortable weather – month by month, with notes on summer, monsoon and winter.',
        'best time to visit Jawai, Jawai weather, Jawai in winter, Jawai summer leopard, Jawai monsoon, Jawai season',
        <<<'HTML'
<p>People often ask whether Jawai has an “off season”. The leopards live here all year, so the honest answer is that every season has its own character. What changes is the weather, the light and what else you will see.</p>
<h2>October to March: comfortable and popular</h2>
<p>Days are warm and dry, evenings are cool and December–January mornings can be properly cold in an open jeep. This is the most comfortable time to travel and the busiest, especially around Diwali and the New Year. Winter is also when migratory birds gather at the Jawai Dam, so it is the best season to combine leopards with <a href="{{blog/birdwatching-in-jawai}}">birdwatching</a>. Book rooms and jeeps early for December and January.</p>
<h2>April to June: hot, but excellent for sightings</h2>
<p>Temperatures are high in the afternoon, so plan around the safari windows and rest indoors in between. As vegetation dries out and water becomes scarce, animals move between the rocks and waterholes and the hills open up, which many guides consider the best time for leopard sightings. Early starts and plenty of water make it very doable.</p>
<h2>July to September: green and quiet</h2>
<p>The monsoon turns the granite hills green and fills the dam. It is the quietest season, with fewer visitors and good photographic light between showers. Heavy rain can occasionally limit jeep routes, so keep plans flexible and confirm with us before you travel.</p>
<h2>Quick picks</h2>
<ul>
<li><strong>First-time visitors:</strong> November to February.</li>
<li><strong>Best chance of long leopard sightings:</strong> March to June.</li>
<li><strong>Birds and landscapes:</strong> December to February, and the green weeks after the monsoon.</li>
<li><strong>Quiet, lower-cost trip:</strong> July to September.</li>
</ul>
<p>Whatever the month, a stay in Bera puts you minutes from the hills. Browse our <a href="{{rooms}}">rooms</a> or ask about dates on <a href="{{contact}}">WhatsApp</a>.</p>
HTML];

    $P[] = ['how-to-reach-jawai-from-udaipur-jodhpur-jaipur', 'How to Reach Jawai from Udaipur, Jodhpur, Jaipur & Delhi', 'Planning', 'assets/images/hotel-front.jpg', '2026-09-07 09:00:00',
        'Road, rail and air routes to Jawai and Bera, with real driving distances from the nearest airports and stations.',
        'How to Reach Jawai: Airports, Trains & Road Distances',
        'How to reach Jawai and Bera, Pali, by air, train and road: Udaipur and Jodhpur airports, Jawai Bandh and Falna stations, driving times and tips.',
        'how to reach Jawai, Udaipur to Jawai, Jodhpur to Jawai, Jaipur to Jawai, Jawai Bandh railway station, Falna station, Jawai airport',
        <<<'HTML'
<p>Jawai sits in the Pali district of Rajasthan, between Udaipur and Jodhpur. There is no airport in the village itself, but the region is easy to reach by combining a flight or train with a road transfer to Bera.</p>
<h2>By air</h2>
<ul>
<li><strong>Udaipur (Maharana Pratap Airport, UDR):</strong> about 129 km by road from our hotel, roughly 1 hr 55 min. It is the nearest major airport.</li>
<li><strong>Jodhpur Airport (JDH):</strong> about 173 km, roughly 2 hr 20 min.</li>
</ul>
<h2>By train</h2>
<ul>
<li><strong>Sumerpur Jawai Bandh (JWB):</strong> the nearest station, about 18 km from the hotel.</li>
<li><strong>Falna (FA):</strong> about 37 km, on the main Delhi–Ahmedabad line, with the widest choice of trains.</li>
</ul>
<h2>By road from nearby cities</h2>
<ul>
<li><strong>Udaipur City:</strong> about 113 km, around 1 hr 45 min.</li>
<li><strong>Jodhpur:</strong> roughly 175 km, about 3 hours.</li>
<li><strong>Mount Abu:</strong> about 99 km, around 2 hours.</li>
<li><strong>Jaipur and Delhi:</strong> most travellers take a train or flight to Udaipur, Jodhpur or Falna rather than driving the full distance.</li>
</ul>
<p>Distances on our site are real road distances and can be checked on the interactive map on the <a href="{{jawai}}">Explore Jawai</a> page.</p>
<h2>Tips</h2>
<ul>
<li>Roads are good but villages and livestock crossings mean you should avoid driving fast after dark.</li>
<li>Arrive the evening before your first safari so you can rest and start at first light.</li>
<li>Ask us to arrange a taxi from the airport or station – message us on <a href="{{contact}}">WhatsApp</a> with your arrival time.</li>
</ul>
HTML];

    $P[] = ['where-to-stay-in-jawai-bera-hotels-camps', 'Where to Stay in Jawai: Hotels, Camps and Homestays Compared', 'Stay', 'assets/images/hotel-front.jpg', '2026-09-10 09:00:00',
        'How to choose a base for your Jawai trip, and why staying in Bera village puts you closest to the hills.',
        'Where to Stay in Jawai: Bera Hotels vs Camps vs Homestays',
        'Choosing where to stay in Jawai? Compare hotels, tented camps and homestays, what to ask before booking and why Bera village is a smart base.',
        'where to stay in Jawai, hotels in Jawai, Bera hotel, Jawai camps, Jawai resort, Jawai homestay, stay near Jawai leopard hills',
        <<<'HTML'
<p>Jawai offers three broad kinds of stay: luxury tented camps spread out near the hills, small homestays in villages and town hotels in and around Bera. Each suits a different trip.</p>
<h2>Tented camps</h2>
<p>Camps are scenic and secluded, usually priced for a special-occasion stay. They are often further from towns, which means longer transfers for meals, shopping or medical needs, and many operate seasonally.</p>
<h2>Homestays</h2>
<p>Village homestays give a close look at local life at a modest price. Facilities are simple and the choice of food and room types is limited.</p>
<h2>A hotel in Bera</h2>
<p>A hotel in Bera village gives you air-conditioned rooms, a working kitchen and staff on hand at any hour, a few minutes from the leopard hills and the road to Jawai Dam. It is a practical option for families, groups and anyone who wants comfort without a long drive.</p>
<h2>What to ask before you book</h2>
<ol>
<li>How far is it from the hills and how long is the drive to the safari start point?</li>
<li>Are safaris run by the property or outsourced?</li>
<li>Is the food vegetarian, and can it be adjusted for children or dietary needs?</li>
<li>What are check-in, check-out and cancellation terms?</li>
<li>Is there a reliable way to message the team before you arrive?</li>
</ol>
<h2>Why Jawai Mumal</h2>
<p>Jawai Mumal combines <a href="{{rooms}}">comfortable rooms</a>, a pure-vegetarian <a href="{{restaurant}}">Rajasthani restaurant</a> and our own <a href="{{safari}}">leopard safaris</a> under one roof in Bera, so you can sleep, eat and set out for the hills without arranging three different providers.</p>
HTML];

    $P[] = ['jawai-dam-jawai-bandh-history-birds-crocodiles', 'Jawai Dam (Jawai Bandh): History, Birds and Crocodiles', 'Nature', '', '2026-09-13 09:00:00',
        'The story behind western Rajasthan’s large Jawai reservoir and the wildlife you can see on its shores.',
        'Jawai Dam (Jawai Bandh): History, Birds & Wildlife',
        'Jawai Dam in Pali, Rajasthan: when it was built, why it matters, the birds and mugger crocodiles you can see and how to visit from Bera.',
        'Jawai Dam, Jawai Bandh, Jawai dam birds, Jawai dam history, Jawai dam crocodile, Jawai dam flamingo, Pali dam',
        <<<'HTML'
<p>Jawai Dam, also called Jawai Bandh, is one of the largest reservoirs in western Rajasthan. It was completed in 1957 on the Jawai river, a tributary in the Luni basin, and has supplied water to the surrounding districts for decades. Granite hills rise all around it, which is exactly why the landscape here is so photogenic.</p>
<h2>A lake in dry country</h2>
<p>In a part of Rajasthan better known for sand and scrub, the dam is a lifeline for people, livestock and wildlife. When the monsoon fills it, the shoreline turns green and the water attracts birds from far away.</p>
<h2>Birds you may see</h2>
<p>Over three hundred bird species have been recorded around Jawai. In the cooler months look for flamingos, demoiselle cranes, bar-headed geese, painted storks, spoonbills and a range of ducks and waders, with raptors circling over the hills. See our <a href="{{blog/birdwatching-in-jawai}}">birdwatching guide</a> for more.</p>
<h2>Other wildlife</h2>
<p>Mugger crocodiles bask on the banks, and the dam’s edges are good places to spot jackal, mongoose and monitor lizards. Leopards range across the hills nearby, so the shoreline is part of the same landscape you will explore on a safari.</p>
<h2>Visiting from Bera</h2>
<p>The dam is about 15 km from Bera, roughly half an hour by road. A slow morning drive with binoculars is rewarding; our <a href="{{safari}}">Jawai Dam wetland and birding drive</a> is built around it. Dress in muted colours, keep noise low and never approach the water’s edge on foot near crocodiles.</p>
HTML];

    $P[] = ['rabari-community-jawai-culture-and-leopards', 'The Rabari of Jawai: Living Beside Leopards', 'Culture', '', '2026-09-16 09:00:00',
        'Meet the herding community whose tolerance for big cats shapes the story of Jawai.',
        'Rabari Community of Jawai: Culture & Coexistence with Leopards',
        'Learn about the Rabari herders of Jawai, their red turbans and silver jewellery, hill temples and the unusual coexistence between people and leopards.',
        'Rabari community Jawai, Rabari culture Rajasthan, Jawai villages, Jawai leopards and people, Rabari village visit, Raika herders',
        <<<'HTML'
<p>The Rabari are a pastoral community found across western India, traditionally herders of sheep, goats and camels. Around Jawai their villages sit right beside the hills where leopards live, and that closeness is the heart of the region’s story.</p>
<h2>Distinctive dress</h2>
<p>Rabari men are recognisable by bright red or white turbans, white dhotis and kurtas and heavy silver jewellery. Women wear embroidered shawls, bangles and layered ornaments. Colour and ornament often signal clan and marital status.</p>
<h2>Sharing the hills</h2>
<p>Granite hills here are not just wildlife habitat; they are grazing land and sacred ground, with small shrines tucked among the boulders. Leopards sometimes take livestock, yet in many places the community’s attitude has been one of tolerance rather than retaliation. That patience – and the habitat that remains – is a major reason leopards can be seen so often in Jawai.</p>
<h2>What a village visit is like</h2>
<ul>
<li>A walk through lanes with a local guide.</li>
<li>Masala chai in a courtyard and conversation about herding life.</li>
<li>A look at traditional crafts and household life.</li>
</ul>
<p>Visits are most welcoming when guests are respectful: ask before taking photographs, dress modestly and treat it as a conversation, not a photo stop. Our <a href="{{safari}}">Rabari village and culture visit</a> is led by a local guide and takes about two hours.</p>
<p>Pair it with a dawn leopard drive for a fuller picture of the landscape; the <a href="{{blog/jawai-leopard-safari-complete-guide}}">safari guide</a> explains how.</p>
HTML];

    $P[] = ['jawai-3-day-itinerary-safari-kumbhalgarh-ranakpur', 'A 3-Day Jawai Itinerary: Safari, Villages and Kumbhalgarh', 'Itinerary', 'assets/images/leopard-log.jpg', '2026-09-19 09:00:00',
        'A practical three-day plan combining leopard safaris, the Jawai Dam, village culture and a heritage day trip.',
        '3-Day Jawai Itinerary: Leopards, Dam, Villages & Heritage',
        'A relaxed 3-day Jawai itinerary with two leopard safaris, Jawai Dam birdwatching, a Rabari village visit and a day trip to Ranakpur and Kumbhalgarh.',
        'Jawai itinerary, 3 day Jawai trip, Jawai weekend, Jawai Ranakpur Kumbhalgarh, Jawai travel plan, Jawai safari itinerary',
        <<<'HTML'
<p>Three days is enough to see Jawai properly without rushing. This plan assumes you arrive the evening before Day 1 and are based in Bera.</p>
<h2>Day 1: Leopard hills</h2>
<ul>
<li><strong>Dawn:</strong> a leopard tracking safari with tea on the rocks.</li>
<li><strong>Late morning:</strong> breakfast, rest and a lazy lunch of Rajasthani vegetarian food.</li>
<li><strong>Afternoon:</strong> a second drive at around 4 PM for golden-hour sightings and sunset over the hills.</li>
</ul>
<h2>Day 2: Dam and village</h2>
<ul>
<li><strong>Morning:</strong> a birding drive at the Jawai Dam, about 15 km away.</li>
<li><strong>Midday:</strong> a Rabari village walk with chai.</li>
<li><strong>Evening:</strong> a campfire and stargazing at the hotel.</li>
</ul>
<h2>Day 3: Heritage day trip</h2>
<ul>
<li><strong>Ranakpur Jain Temples</strong> (about 48 km, an hour away): a marble complex with 1,444 carved pillars in a forested valley.</li>
<li><strong>Kumbhalgarh Fort</strong> (about 81 km, roughly 1 hr 40 min): a 15th-century hill fort with one of the longest continuous walls in the world.</li>
</ul>
<p>If you prefer to drive on to Udaipur or Jodhpur afterwards, both are on the way out. Read about <a href="{{blog/day-trips-from-jawai-ranakpur-kumbhalgarh-mount-abu}}">day trips from Jawai</a> to decide what to include.</p>
<p>Want it arranged? Message us on <a href="{{contact}}">WhatsApp</a> and we will plan jeeps, meals and transport around your dates.</p>
HTML];

    $P[] = ['jawai-vs-ranthambore-which-safari', 'Jawai vs Ranthambore: Which Safari Is Right for You?', 'Wildlife', 'assets/images/leopard-log.jpg', '2026-09-22 09:00:00',
        'Leopards on open rocks or tigers in forest: how the two famous Rajasthan safaris differ.',
        'Jawai vs Ranthambore Safari: Differences & Which to Choose',
        'Comparing Jawai and Ranthambore for wildlife: leopards vs tigers, landscape, crowds, safari style, cost and who should choose which.',
        'Jawai vs Ranthambore, Jawai or Ranthambore, leopard vs tiger safari Rajasthan, best safari in Rajasthan, Jawai safari experience',
        <<<'HTML'
<p>Both are in Rajasthan and both deliver memorable wildlife, but they are very different experiences. The right choice depends on what you most want to see.</p>
<h2>The animal</h2>
<p>Ranthambore is famous for tigers. Jawai is leopard country, where the cats are often seen resting on granite boulders. If a tiger is your one goal, Ranthambore is the better bet; if you want to see a big cat in open, rocky terrain with fewer vehicles, Jawai is hard to beat.</p>
<h2>The landscape</h2>
<p>Ranthambore is a national park with forest, lakes and a fort. Jawai is a landscape of granite domes, caves, farmland, a dam and villages. In Jawai you drive through living, working countryside rather than a fenced park.</p>
<h2>Crowds and style</h2>
<p>Ranthambore safaris run in zones with a fixed number of vehicles and usually require permits booked in advance. Jawai safaris are community-linked and run in small open jeeps with local trackers, which tends to feel more relaxed and personal.</p>
<h2>Who should choose which</h2>
<ul>
<li><strong>Choose Jawai</strong> for leopards, photography on open rocks, culture and a calm pace.</li>
<li><strong>Choose Ranthambore</strong> for tigers and a classic national-park experience.</li>
<li><strong>Do both</strong> if you have a week – they combine well in one Rajasthan itinerary.</li>
</ul>
<p>Either way, nothing in the wild is guaranteed. Read how a drive works in our <a href="{{blog/jawai-leopard-safari-complete-guide}}">complete Jawai safari guide</a>.</p>
HTML];

    $P[] = ['wildlife-of-jawai-beyond-leopards', 'Wildlife of Jawai: More Than Leopards', 'Wildlife', '', '2026-09-25 09:00:00',
        'Hyenas, crocodiles, foxes and more: what else lives in the Jawai hills and around the dam.',
        'Wildlife of Jawai: Animals Besides Leopards',
        'Beyond leopards: striped hyena, desert fox, jackal, mongoose, mugger crocodile and other wildlife you may see around Jawai, with tips for spotting them.',
        'Jawai wildlife, animals in Jawai, striped hyena Jawai, Jawai crocodile, Jawai desert fox, Jawai biodiversity',
        <<<'HTML'
<p>Leopards are the headline act, but Jawai’s hills and wetlands support a wider cast. Spending time looking for the “smaller” species often makes a safari richer.</p>
<h2>Mammals</h2>
<ul>
<li><strong>Striped hyena:</strong> a shy night animal that shares the same rocky caves as leopards.</li>
<li><strong>Indian fox and desert fox:</strong> small, quick and often seen at dusk.</li>
<li><strong>Jackal and mongoose:</strong> common along tracks and field edges.</li>
<li><strong>Langur and nilgai:</strong> langurs sound alarm calls when a leopard moves, which trackers listen for.</li>
</ul>
<h2>Reptiles</h2>
<p>Mugger crocodiles bask on the Jawai Dam shoreline. Monitor lizards, agamas and several snakes live among the rocks. Keep your distance and follow your guide’s instructions.</p>
<h2>Birds</h2>
<p>More than three hundred species have been recorded. Raptors, owls and peafowl are common all year; winter brings waterbirds. We cover this in detail in <a href="{{blog/birdwatching-in-jawai}}">Birdwatching in Jawai</a>.</p>
<h2>Spotting tips</h2>
<ol>
<li>Listen: alarm calls reveal where a predator is moving.</li>
<li>Scan rock ledges, cave mouths and shaded boulders rather than open ground.</li>
<li>Go early and late, when animals are active and the light is soft.</li>
<li>Stay quiet and still; patience wins at Jawai.</li>
</ol>
<p>Our <a href="{{safari}}">safari packages</a> are led by local trackers who know these hills by sight and sound.</p>
HTML];

    $P[] = ['birdwatching-in-jawai', 'Birdwatching in Jawai: Species, Seasons and Hotspots', 'Wildlife', '', '2026-09-28 09:00:00',
        'When to go, where to look and what you might see on a birding morning at Jawai.',
        'Birdwatching in Jawai: Best Season, Species & Hotspots',
        'Birdwatching in Jawai, Rajasthan: the best season, species around the dam and granite hills, tips and how to arrange a birding drive from Bera.',
        'Jawai birdwatching, birds of Jawai, Jawai dam flamingos, Jawai birding tour, Rajasthan birding, demoiselle crane Jawai',
        <<<'HTML'
<p>With the Jawai Dam, scrubby farmland and rocky hills in one landscape, the region attracts an impressive variety of birds. More than three hundred species have been recorded around Jawai.</p>
<h2>Best season</h2>
<p>November to February is the peak, when migratory waterbirds arrive. March to June is good for raptors and dry-country species; the monsoon brings breeding activity and green scenery.</p>
<h2>Species you may find</h2>
<ul>
<li><strong>Waterbirds:</strong> flamingos, painted stork, spoonbill, bar-headed goose, ducks and waders.</li>
<li><strong>Cranes:</strong> demoiselle cranes in some winters.</li>
<li><strong>Raptors:</strong> eagles, kites, harriers and shikra circling over the hills.</li>
<li><strong>Hill and scrub birds:</strong> owls, peafowl, bee-eaters, rock-loving species and many more.</li>
</ul>
<h2>How to bird at Jawai</h2>
<ol>
<li>Start at sunrise when birds are most active.</li>
<li>Drive slowly along the dam edge and stop often.</li>
<li>Use a spotting scope or binoculars; do not walk toward water because of crocodiles.</li>
<li>Keep a checklist; you will be surprised how many you tally.</li>
</ol>
<p>Our <a href="{{safari}}">Jawai Dam wetland and birding drive</a> runs in the morning with an experienced guide. Learn more about the reservoir itself in <a href="{{blog/jawai-dam-jawai-bandh-history-birds-crocodiles}}">our Jawai Dam guide</a>.</p>
HTML];

    $P[] = ['jawai-safari-cost-booking-what-is-included', 'Jawai Safari Cost and Booking: What Is Included?', 'Safari', 'assets/images/leopard-log.jpg', '2026-10-01 09:00:00',
        'How Jawai safaris are priced, what shared vs private jeeps mean and how to book without surprises.',
        'Jawai Safari Cost & Booking Guide: Shared vs Private Jeep',
        'How much does a Jawai leopard safari cost? Shared vs private jeep, what is included, how to book from Bera and tips to avoid surprises.',
        'Jawai safari cost, Jawai safari price, Jawai safari booking, private jeep safari Jawai, Jawai safari charges',
        <<<'HTML'
<p>Safari rates depend on the season, the group size and the kind of drive, so we share them on enquiry rather than print numbers that go out of date. The structure, however, stays the same and is worth understanding before you book.</p>
<h2>Shared vs private</h2>
<ul>
<li><strong>Per-person (shared):</strong> you join other guests in an open jeep, usually up to six. The most affordable option.</li>
<li><strong>Private jeep:</strong> the whole vehicle is yours. Best for families, couples, photographers and anyone who wants to set the pace.</li>
</ul>
<h2>What is usually included</h2>
<ul>
<li>An open 4x4 jeep, driver and a local tracker.</li>
<li>About three hours on the road, depending on sightings.</li>
<li>A tea or snack stop where safe.</li>
</ul>
<p>Park-style entry or forest charges, if any, and optional extras like a sundowner or breakfast picnic are confirmed when you book.</p>
<h2>Packages</h2>
<p>We run a classic leopard tracking safari, a Jawai Dam birding drive, a private 4x4 expedition, a small-group photography safari and a Rabari village visit. See the timings and what each drive covers on our <a href="{{safari}}">safari page</a>, then ask for the current rate.</p>
<h2>How to book</h2>
<ol>
<li>Pick your dates and number of guests.</li>
<li>Send the request on the <a href="{{book?type=safari}}">booking page</a> – it opens a ready WhatsApp message.</li>
<li>We confirm availability and share the rate on WhatsApp. No online payment is needed to request.</li>
</ol>
<p>Weekends, school holidays and December–January fill up quickly, so ask early.</p>
HTML];

    $P[] = ['jawai-wildlife-photography-tips', 'Jawai Wildlife Photography Tips for Leopards and Landscapes', 'Photography', 'assets/images/leopard-log.jpg', '2026-10-04 09:00:00',
        'Light, lenses, camera settings and etiquette for photographing leopards on granite hills.',
        'Jawai Wildlife Photography Tips: Leopards & Landscapes',
        'Practical photography tips for Jawai leopard safaris: best light, lenses, camera settings, composition and ethical behaviour on the rocks.',
        'Jawai photography, leopard photography tips, wildlife photography Rajasthan, Jawai photo safari, Jawai golden hour',
        <<<'HTML'
<p>Jawai’s granite domes, golden light and relaxed leopards make it a favourite with photographers. A little preparation helps you come home with more than record shots.</p>
<h2>Light</h2>
<p>The first and last hour of daylight give warm, low-angle light and long shadows on the rocks. Midday light is harsh; use it to rest. Overcast mornings after rain are excellent for soft, even tones.</p>
<h2>Gear</h2>
<ul>
<li>A telephoto lens in the 200–500 mm range; a 70–200 mm is also useful for landscapes and scenes with context.</li>
<li>A beanbag for steadying the lens on the jeep rail (a monopod is awkward in a jeep).</li>
<li>Spare batteries and cards; dawn cold drains batteries quickly.</li>
<li>A lens cloth, because dust is constant.</li>
</ul>
<h2>Settings that work</h2>
<ol>
<li>Aperture priority, wide open for subject separation.</li>
<li>Shutter speed above 1/1000 s for moving animals, 1/500 s for resting ones.</li>
<li>Auto-ISO with a sensible ceiling.</li>
<li>Continuous autofocus and burst mode.</li>
</ol>
<h2>Composition</h2>
<p>Include the rock, the cave and the sky; Jawai is about habitat as much as the animal. Shoot at the animal’s eye level when you can and leave space in front of the face.</p>
<h2>Etiquette</h2>
<p>No flash, no loud noises, no leaving the vehicle and no pressing for a closer approach. The animal’s comfort comes first. Our <a href="{{safari}}">wildlife photography safari</a> limits each jeep to three photographers so everyone has a clear view.</p>
HTML];

    $P[] = ['rajasthani-vegetarian-food-to-eat-in-jawai', 'Rajasthani Vegetarian Food to Eat in Jawai', 'Food', 'assets/images/food-thali.jpg', '2026-10-07 09:00:00',
        'Dal baati churma, ker sangri, bajra roti and more: a vegetarian guide to the flavours of Marwar.',
        'Rajasthani Vegetarian Food in Jawai: Dishes You Must Try',
        'A guide to Rajasthani vegetarian food around Jawai: dal baati churma, ker sangri, gatte ki sabzi, bajra roti, kadhi and sweets, and where to eat in Bera.',
        'Rajasthani food Jawai, dal baati churma, ker sangri, gatte ki sabzi, vegetarian restaurant Bera, Marwari food, Rajasthani thali',
        <<<'HTML'
<p>Rajasthani village food was designed for a dry land where fresh greens are scarce and meals must last. Lentils, millets, dried desert beans and plenty of ghee make a cuisine that is generous, earthy and fully vegetarian.</p>
<h2>Dishes to try</h2>
<ul>
<li><strong>Dal baati churma:</strong> baked wheat balls dunked in ghee, served with spiced dal and sweet crumbled churma.</li>
<li><strong>Ker sangri:</strong> a desert speciality made from wild berries and beans cooked with spices.</li>
<li><strong>Gatte ki sabzi:</strong> gram-flour dumplings simmered in a yoghurt gravy.</li>
<li><strong>Bajra roti with ghee and garlic chutney:</strong> a millet flatbread that tastes best hot.</li>
<li><strong>Rajasthani kadhi:</strong> a tangy yoghurt curry with gram-flour pakoras.</li>
<li><strong>Pyaaz kachori and mirchi vada:</strong> classic snacks for tea time.</li>
<li><strong>Sweets:</strong> churma ladoo, moong dal halwa, malpua and ghevar in season.</li>
<li><strong>Chai and buttermilk:</strong> kulhad chai after a dawn safari, chhaas in the heat.</li>
</ul>
<h2>Eating in Bera</h2>
<p>Our restaurant is pure vegetarian and cooks Rajasthani village food alongside tandoor and everyday favourites. Children and guests with mild-spice preferences are easy to cater for; just tell us when you order. See the <a href="{{restaurant}}">restaurant page</a> for the current menu.</p>
<p>Want a picnic breakfast or a village-style meal during your safari? Mention it when you <a href="{{book?type=safari}}">request a booking</a> and we will arrange it.</p>
HTML];

    $P[] = ['jawai-with-kids-family-travel-tips', 'Jawai with Kids: Family Travel Tips', 'Family', 'assets/images/hotel-day.jpg', '2026-10-10 09:00:00',
        'How to plan a comfortable, safe and fun Jawai trip for families with children.',
        'Jawai with Kids: Family Safari & Stay Tips',
        'Travelling to Jawai with children: safari age and comfort tips, what to pack, family-friendly activities and how to plan a relaxed family trip.',
        'Jawai with kids, family trip Jawai, Jawai family safari, Rajasthan wildlife with children, Bera family hotel',
        <<<'HTML'
<p>Jawai is a good wildlife destination for families because the pace is gentle: short distances, open vehicles and long pauses to watch rather than chase. A few choices make it far easier with children.</p>
<h2>Choose a private jeep</h2>
<p>A private jeep lets you stop for breaks, adjust timings and keep the noise level to what suits your family. Most children manage a three-hour drive with snacks, water and something to look through, such as binoculars.</p>
<h2>Timing</h2>
<p>Dawn drives are cold in winter, so pack jackets, caps and a blanket. For younger children the late-afternoon drive, ending at sunset, is often easier than a 6 AM start.</p>
<h2>Keep them engaged</h2>
<ul>
<li>Give children a simple checklist of animals and birds to tick off.</li>
<li>Bring child-size binoculars.</li>
<li>Combine the safari with easy activities: a Rabari village walk, a campfire and stargazing.</li>
</ul>
<h2>Safety</h2>
<p>Everyone stays seated in the vehicle, and children should follow the guide’s instructions without exception. Sun protection and water are essential in warm months.</p>
<h2>Food and comfort</h2>
<p>A vegetarian kitchen that can adjust spice levels, a comfortable air-conditioned room and a hotel only minutes from the hills makes the logistics easy. Explore our <a href="{{rooms}}">family-friendly rooms</a> or tell us your needs on <a href="{{contact}}">WhatsApp</a>.</p>
HTML];

    $P[] = ['romantic-getaway-jawai-couples-honeymoon', 'A Romantic Getaway in Jawai: Couples and Honeymoon Ideas', 'Couples', 'assets/images/hotel-front.jpg', '2026-10-13 09:00:00',
        'Quiet hills, starry skies and good food: how to plan a memorable couples’ trip.',
        'Romantic Jawai Getaway: Honeymoon & Couples Ideas',
        'Plan a romantic Jawai trip for couples and honeymooners: private leopard safaris, sunset points, stargazing, village dinners and where to stay in Bera.',
        'Jawai honeymoon, romantic getaway Jawai, couples trip Rajasthan, Jawai for couples, private safari Jawai, Bera hotel couples',
        <<<'HTML'
<p>Jawai is not a party destination; it is a place to slow down. For couples, the draw is open skies, big landscapes and shared moments such as watching a leopard stretch on a warm rock at sunrise.</p>
<h2>Ideas for two</h2>
<ul>
<li><strong>Private dawn safari:</strong> your own jeep and tracker, with flasks of tea on a hilltop.</li>
<li><strong>Sundowner on the granite hills:</strong> ask for a quiet viewpoint and a drink as the light turns gold.</li>
<li><strong>Campfire and stargazing:</strong> clear desert skies away from city lights.</li>
<li><strong>A village dinner:</strong> a simple home-style Rajasthani meal.</li>
<li><strong>A day trip:</strong> Ranakpur’s marble temples or Kumbhalgarh Fort for a change of scene.</li>
</ul>
<h2>When to go</h2>
<p>Winter evenings are the most pleasant. Spring and early summer offer excellent sightings if you do not mind the heat. Our <a href="{{blog/best-time-to-visit-jawai}}">best time to visit guide</a> has details.</p>
<h2>Celebrating something?</h2>
<p>If it is an anniversary or birthday, tell us in advance. We can arrange a flower-decorated room, a cake or a private dinner. Browse our <a href="{{rooms}}">rooms and suites</a> and request dates on <a href="{{book?type=stay}}">the booking page</a>.</p>
HTML];

    $P[] = ['day-trips-from-jawai-ranakpur-kumbhalgarh-mount-abu', 'Day Trips from Jawai: Ranakpur, Kumbhalgarh, Mount Abu and Udaipur', 'Itinerary', '', '2026-10-16 09:00:00',
        'Heritage and nature trips within easy driving distance of Bera, with real distances.',
        'Day Trips from Jawai: Ranakpur, Kumbhalgarh, Mount Abu & More',
        'Best day trips from Jawai and Bera: Ranakpur Jain temples, Kumbhalgarh Fort, Mount Abu, Udaipur and Jodhpur, with driving distances and tips.',
        'day trips from Jawai, Ranakpur from Jawai, Kumbhalgarh from Jawai, Mount Abu from Jawai, Udaipur Jawai, places near Jawai',
        <<<'HTML'
<p>Jawai sits roughly midway between Udaipur and Jodhpur, so it makes a natural stop for a trip that mixes wildlife with heritage. These are the most rewarding day trips from Bera.</p>
<h2>Ranakpur Jain Temples (about 48 km, 1 hour)</h2>
<p>A marble temple complex in a forested valley, known for 1,444 intricately carved pillars, no two exactly alike. Visit in the morning when it is quiet; dress modestly and check temple timings for non-Jain visitors.</p>
<h2>Kumbhalgarh Fort (about 81 km, 1 hr 40 min)</h2>
<p>A 15th-century fort in the Aravalli hills with a wall that is among the longest continuous walls in the world. Plan two to three hours and wear comfortable shoes.</p>
<h2>Mount Abu (about 99 km, 2 hours)</h2>
<p>Rajasthan’s only hill station, with Nakki Lake, the Dilwara temples and cooler air. A long day, so start early.</p>
<h2>Udaipur (about 113 km, 1 hr 45 min)</h2>
<p>City Palace, Lake Pichola and old havelis. Many guests combine Jawai with two nights in Udaipur.</p>
<h2>Jodhpur (about 175 km, 3 hours)</h2>
<p>Mehrangarh Fort and the old Blue City. Better as an onward leg than a return day trip.</p>
<h2>Planning tips</h2>
<ul>
<li>Do the safari at dawn and a day trip after breakfast, or the other way round on a separate day.</li>
<li>Ask us to arrange a car and driver.</li>
<li>See all distances on our <a href="{{jawai}}">Explore Jawai</a> map.</li>
</ul>
<p>Looking for a full plan? Try our <a href="{{blog/jawai-3-day-itinerary-safari-kumbhalgarh-ranakpur}}">3-day Jawai itinerary</a>.</p>
HTML];

    $P[] = ['jawai-safari-rules-etiquette-responsible-travel', 'Jawai Safari Rules and Etiquette: Travelling Responsibly', 'Safari', 'assets/images/leopard-log.jpg', '2026-10-19 09:00:00',
        'How to behave on safari so wildlife and the local community stay undisturbed.',
        'Jawai Safari Rules & Etiquette: Responsible Wildlife Travel',
        'Do’s and don’ts for a Jawai leopard safari: staying in the vehicle, noise, flash, photography, respect for villagers and leaving no trace.',
        'Jawai safari rules, safari etiquette, responsible wildlife tourism, Jawai leopard conservation, safari do and dont',
        <<<'HTML'
<p>Jawai works because people, leopards and visitors have found a balance. Following a few simple rules keeps it that way.</p>
<h2>In the vehicle</h2>
<ul>
<li>Stay seated and inside the jeep at all times.</li>
<li>Follow the guide’s instructions immediately – they read animal behaviour.</li>
<li>Keep voices low and phones on silent.</li>
</ul>
<h2>Around animals</h2>
<ul>
<li>Never ask the driver to approach or follow an animal closely.</li>
<li>No flash photography, drones or loud noises.</li>
<li>Do not feed or call to wildlife.</li>
<li>If an animal changes its behaviour because of us, we back off.</li>
</ul>
<h2>Respecting the land and people</h2>
<ul>
<li>Carry your rubbish out; plastic bottles and wrappers harm livestock and wildlife.</li>
<li>Ask permission before photographing people, especially women and children.</li>
<li>Dress modestly in villages and near shrines.</li>
<li>Buy local where you can; it keeps value in the community that protects the animals.</li>
</ul>
<h2>Why it matters</h2>
<p>Leopards at Jawai live beside farms and herds. Respectful tourism gives communities a reason to keep tolerating them. Read more about that relationship in <a href="{{blog/rabari-community-jawai-culture-and-leopards}}">The Rabari of Jawai</a>, and our <a href="{{policies}}">policies page</a> for our safari conduct terms.</p>
HTML];

    $P[] = ['private-parties-events-celebrations-in-jawai-bera', 'Private Parties and Celebrations in Jawai and Bera', 'Events', 'assets/images/hotel-front.jpg', '2026-10-22 09:00:00',
        'Birthdays, anniversaries, family get-togethers and small corporate groups at a hotel near the leopard hills.',
        'Private Parties & Events in Jawai & Bera | Jawai Mumal',
        'Plan a birthday, anniversary, family reunion or small group event in Jawai: venue, vegetarian catering, stay, safari add-ons and how to enquire.',
        'party venue Jawai, events in Bera, birthday celebration Jawai, anniversary Jawai, group booking Jawai, corporate retreat Rajasthan',
        <<<'HTML'
<p>A celebration in Jawai is different from one in a city banquet hall. The setting, the food and the possibility of a leopard safari the next morning give the occasion a story guests remember.</p>
<h2>What we can arrange</h2>
<ul>
<li><strong>Birthdays and anniversaries:</strong> decorated rooms or an evening dinner with a cake.</li>
<li><strong>Family reunions:</strong> rooms for the whole group and a shared meal at the restaurant.</li>
<li><strong>Small corporate groups:</strong> a quiet retreat with a safari as the team activity.</li>
<li><strong>Photography groups:</strong> a stay with dawn and dusk drives.</li>
</ul>
<h2>Food</h2>
<p>All catering is pure vegetarian, from Rajasthani thali and live tandoor to snacks and sweets. We can plan a menu around the number of guests and the time of year.</p>
<h2>Safari add-ons</h2>
<p>Many groups add a <a href="{{safari}}">leopard safari</a> or a <a href="{{blog/rabari-community-jawai-culture-and-leopards}}">village visit</a> to the program, usually in the morning after a night’s stay.</p>
<h2>How to plan</h2>
<ol>
<li>Tell us your date, number of guests and kind of event.</li>
<li>Send the request on the <a href="{{book?type=event}}">party and events form</a>; it opens WhatsApp with the details filled in.</li>
<li>We reply with availability, a suggested plan and a quote.</li>
</ol>
<p>For larger groups, book well ahead; weekends and winter dates go first.</p>
HTML];

    return $P;
}

function jm_seed_blog(): void {
    try {
        if ((int)val('SELECT COUNT(*) FROM jm_blog') === 0) {
            $st = db()->prepare('INSERT INTO jm_blog (slug,title,category,image,published_at,excerpt,meta_title,meta_description,keywords,body,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
            foreach (jm_blog_posts() as $i => $p) {
                $st->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], 'published', $i]);
            }
        }
        set_setting('blog_seeded', '1');
    } catch (Throwable $e) { /* table not ready yet; try again on the next request */ }
}
