<?php
/*
 * Per-destination content for the five destination pages.
 *
 * These were five near-identical ~275-line files with the same markup copied
 * five times: any layout change meant editing five files. Shared markup now
 * lives in includes/dest_page.php; only what differs lives here.
 *
 * Values were EXTRACTED from the originals, not retyped, and every page was
 * diffed against a pre-refactor render before this landed.
 *
 * Some fields intentionally hold HTML (<span> emphasis in headings, star
 * markup in season ratings) and are echoed raw - do not pass them to h().
 *
 * routes entries are [from, to, km, duration, badge, href, buttonLabel].
 * Per-route rather than derived, because spiti is not uniform: its third
 * card reads "Full Spiti Circuit" and links to #contact. Its CTA button
 * and hero warning are likewise its own.
 *
 * Shape is deliberately flat so it can move to a DB table + admin screen
 * without reworking the template.
 */
return array (
  'manali' => 
  array (
    'title' => 'Manali Trips & Cab Booking | Himachal Safar',
    'desc' => 'Plan your Manali trip with Himachal Safar. Delhi to Manali. Rohtang Pass, Solang Valley, Hadimba Temple — expert mountain drivers.',
    'metaDesc' => 'Plan your Manali trip with Himachal Safar. Delhi to Manali. Rohtang Pass, Solang Valley, Hadimba Temple — expert mountain drivers.',
    'docTitle' => 'Manali Trips &amp; Cab Booking | Himachal Safar',
    'crumb' => NULL,
    'heroAlt' => 'Manali snow mountains Himachal Pradesh',
    'name' => 'Manali',
    'tagline' => 'The Valley of the Gods — Himalayan peaks, alpine meadows and the spirit of adventure',
    'heroTag' => 
    array (
      0 => 'fa-solid fa-mountain-sun',
      1 => 'Himachal Pradesh',
    ),
    'facts' => 
    array (
      0 => 
      array (
        0 => 'fa-solid fa-mountain',
        1 => '2,050 m Altitude',
      ),
      1 => 
      array (
        0 => 'fa-regular fa-calendar',
        1 => 'Oct – Jun Best Time',
      ),
      2 => 
      array (
        0 => 'fa-solid fa-route',
        1 => '550 km from Delhi',
      ),
      3 => 
      array (
        0 => 'fa-solid fa-temperature-half',
        1 => '−2°C to 25°C',
      ),
    ),
    'heroBtn' => 'Plan Manali Trip',
    'aboutTag' => 
    array (
      0 => 'fa-solid fa-info-circle',
      1 => 'About Manali',
    ),
    'aboutTitle' => 'The <span>Crown Jewel</span> of Himachal Pradesh',
    'aboutParas' => 
    array (
      0 => 'Manali is a high-altitude Himalayan resort town nestled in the Beas River Valley, at the northern end of the Kullu Valley. Sitting at an elevation of 2,050 metres, it is one of the most visited hill stations in India — and for good reason.',
      1 => 'From the thundering Rohtang Pass at 3,978 metres to the serene apple orchards of Old Manali, from the glaciers of Solang Valley to the sacred Hadimba Devi Temple — Manali offers every kind of Himalayan experience. Whether you are a honeymooner, adventurer, family or solo traveller, Manali delivers.',
    ),
    'aboutTags' => 
    array (
      0 => 'Honeymoon Destination',
      1 => 'Adventure Sports',
      2 => 'Trekking &amp; Camping',
      3 => 'Snow Activities',
    ),
    'aboutAlt' => 'Manali valley Himalaya',
    'highlights' => 
    array (
      0 => 
      array (
        0 => 'fa-road',
        1 => 'Rohtang Pass',
        2 => '3,978 m high mountain pass with snow year-round. Gateway to Lahaul–Spiti. Open June to November.',
      ),
      1 => 
      array (
        0 => 'fa-skiing',
        1 => 'Solang Valley',
        2 => 'Adventure hub — skiing, zorbing, paragliding, rope crossing. 14 km from Manali town.',
      ),
      2 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Hadimba Temple',
        2 => '16th-century wooden temple in a cedar forest. Dedicated to Goddess Hadimba — serene and sacred.',
      ),
      3 => 
      array (
        0 => 'fa-city',
        1 => 'Old Manali',
        2 => 'Hippie cafés, budget stays, art shops and the charming Manu Temple above the Beas River.',
      ),
      4 => 
      array (
        0 => 'fa-landmark',
        1 => 'Naggar Castle',
        2 => '500-year-old stone castle, now a heritage hotel. Stunning views over the Kullu Valley.',
      ),
      5 => 
      array (
        0 => 'fa-water',
        1 => 'Beas River',
        2 => 'White-water rafting stretches from Pirdi to Jhiri. River walks and riverside camping available.',
      ),
      6 => 
      array (
        0 => 'fa-moon',
        1 => 'Chandratal Lake',
        2 => '14,100 ft crescent-shaped lake. One of the most beautiful high-altitude lakes in the world.',
      ),
      7 => 
      array (
        0 => 'fa-motorcycle',
        1 => 'Leh via Manali',
        2 => 'The legendary Manali–Leh highway — one of the highest motorable roads on earth at 5,328 m.',
      ),
    ),
    'hlTag' => 
    array (
      0 => 'fa-solid fa-star',
      1 => 'Must Visit',
    ),
    'hlTitle' => 'Top Highlights of <span>Manali</span>',
    'hlBg' => 'var(--green-light)',
    'galTag' => 
    array (
      0 => 'fa-solid fa-images',
      1 => 'Photo Gallery',
    ),
    'galTitle' => 'Manali in <span>Pictures</span>',
    'galFallback' => 
    array (
      0 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&q=85',
        2 => 'Manali snow mountains',
      ),
      1 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85',
        2 => 'Rohtang mountain road',
      ),
      2 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85',
        2 => 'Snow peaks Manali',
      ),
      3 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1457530378978-8bac673b8062?auto=format&fit=crop&w=700&q=85',
        2 => 'Solang Valley Manali',
      ),
      4 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?auto=format&fit=crop&w=1200&q=85',
        2 => 'Himalayan valley Manali',
      ),
      5 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=700&q=85',
        2 => 'Mountain lake Manali',
      ),
    ),
    'rtTag' => 
    array (
      0 => 'fa-solid fa-route',
      1 => 'Book a Cab',
    ),
    'rtTitle' => 'Routes to <span>Manali</span>',
    'rtDesc' => 'Choose your departure city — we cover all major routes to Manali with experienced mountain drivers.',
    'rtBg' => 'var(--cream)',
    'routes' => 
    array (
      0 => 
      array (
        0 => 'Delhi',
        1 => 'Manali',
        2 => '550 km',
        3 => '12–14 hrs',
        4 => 'Family Itinerary',
        5 => 'index.php?calc=manali',
        6 => 'Plan My Trip',
      ),
      1 => 
      array (
        0 => 'Chandigarh',
        1 => 'Manali',
        2 => '300 km',
        3 => '8–9 hrs',
        4 => 'Combo Itinerary',
        5 => 'index.php?calc=manali',
        6 => 'Plan My Trip',
      ),
      2 => 
      array (
        0 => 'Pathankot',
        1 => 'Manali',
        2 => '380 km',
        3 => '9–11 hrs',
        4 => 'Custom Itinerary',
        5 => 'index.php?calc=manali',
        6 => 'Plan My Trip',
      ),
    ),
    'heroWarning' => NULL,
    'ctaH' => 'Plan a Private <span>Manali</span> Journey',
    'ctaP' => 'Share your dates, guest count and hotel preferences. Our travel desk will respond with a clear route plan and quote.',
    'ctaHref' => 'index.php?calc=manali',
    'ctaIcon' => 'fa-solid fa-calculator',
    'ctaBtn' => 'Plan My Trip',
    'ctaWa' => 'Hi, I want a private Manali trip quote. Please help me plan the route.',
    'btTag' => 
    array (
      0 => 'fa-regular fa-calendar',
      1 => 'Travel Tips',
    ),
    'btTitle' => 'Best Time to Visit <span>Manali</span>',
    'seasons' => 
    array (
      0 => 
      array (
        0 => 'season-summer',
        1 => 'fa-solid fa-sun',
        2 => 'Summer (Mar–Jun)',
        3 => 'Perfect weather, 10°C–25°C. Roads to Rohtang and Solang open. Most popular season — book early.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      1 => 
      array (
        0 => 'season-monsoon',
        1 => 'fa-solid fa-cloud-rain',
        2 => 'Monsoon (Jul–Sep)',
        3 => 'Heavy rains, landslide risk on highway. Some parts of Rohtang closed. Not recommended for first-time visitors.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
      2 => 
      array (
        0 => 'season-autumn',
        1 => 'fa-solid fa-leaf',
        2 => 'Autumn (Oct–Nov)',
        3 => 'Clear skies, golden foliage, fewer crowds. Snow starts from late October. Ideal for scenic drives.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
      3 => 
      array (
        0 => 'season-winter',
        1 => 'fa-solid fa-snowflake',
        2 => 'Winter (Dec–Feb)',
        3 => 'Heavy snowfall, −2°C to 10°C. Rohtang closed but Manali town looks magical. Perfect for snow lovers.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
    ),
  ),
  'shimla' => 
  array (
    'title' => 'Shimla Trips & Cab Booking | Himachal Safar',
    'desc' => 'Book Shimla cab from Delhi. Mall Road, Jakhu Temple, Kufri, Chail — experience the Queen of Hills with Himachal Safar.',
    'metaDesc' => 'Book Shimla cab from Delhi. Mall Road, Jakhu Temple, Kufri, Chail — experience the Queen of Hills with Himachal Safar.',
    'docTitle' => 'Shimla Trips &amp; Cab Booking | Himachal Safar',
    'crumb' => NULL,
    'heroAlt' => 'Shimla Queen of Hills Himachal Pradesh',
    'name' => 'Shimla',
    'tagline' => 'The Queen of Hills — colonial charm, pine forests and the iconic Mall Road',
    'heroTag' => 
    array (
      0 => 'fa-solid fa-mountain-sun',
      1 => 'Himachal Pradesh',
    ),
    'facts' => 
    array (
      0 => 
      array (
        0 => 'fa-solid fa-mountain',
        1 => '2,205 m Altitude',
      ),
      1 => 
      array (
        0 => 'fa-regular fa-calendar',
        1 => 'Year-round Destination',
      ),
      2 => 
      array (
        0 => 'fa-solid fa-route',
        1 => '350 km from Delhi',
      ),
      3 => 
      array (
        0 => 'fa-solid fa-temperature-half',
        1 => '2°C to 30°C',
      ),
    ),
    'heroBtn' => 'Plan Shimla Trip',
    'aboutTag' => 
    array (
      0 => 'fa-solid fa-info-circle',
      1 => 'About Shimla',
    ),
    'aboutTitle' => 'The <span>Queen of Hills</span>',
    'aboutParas' => 
    array (
      0 => 'Shimla, the capital of Himachal Pradesh, is one of India\'s most beloved hill stations. Perched at 2,205 metres in the Shivalik range, it was once the summer capital of British India — and its Victorian-era architecture, wide promenades and cedar forests still carry that old-world elegance.',
      1 => 'From the buzzing Mall Road and Ridge to the quiet apple orchards of Kufri, from the adventure at Narkanda to the royal heritage of Chail — Shimla offers something for every traveller. It is the perfect year-round destination, with snow in winters and cool summer escapes.',
    ),
    'aboutTags' => 
    array (
      0 => 'Heritage Architecture',
      1 => 'Apple Orchards',
      2 => 'Toy Train',
      3 => 'Skiing at Kufri',
    ),
    'aboutAlt' => 'Shimla hills Himachal Pradesh',
    'highlights' => 
    array (
      0 => 
      array (
        0 => 'fa-person-walking',
        1 => 'Mall Road',
        2 => 'The heartbeat of Shimla. Shops, restaurants, colonial buildings and mountain views all in one iconic promenade.',
      ),
      1 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Jakhu Temple',
        2 => '2,455 m high temple dedicated to Lord Hanuman. Magnificent views of Shimla — and mischievous monkeys!',
      ),
      2 => 
      array (
        0 => 'fa-skiing',
        1 => 'Kufri',
        2 => '16 km from Shimla. Skiing in winter, horse-riding and Himalayan Nature Park in summer.',
      ),
      3 => 
      array (
        0 => 'fa-chess-rook',
        1 => 'Chail',
        2 => 'Highest cricket ground in the world at 2,250 m. Royal Chail Palace and dense forest — a serene retreat.',
      ),
      4 => 
      array (
        0 => 'fa-train',
        1 => 'Kalka–Shimla Railway',
        2 => 'UNESCO heritage toy train through 102 tunnels and 18 viaducts. One of the most scenic rail journeys in India.',
      ),
      5 => 
      array (
        0 => 'fa-hot-tub',
        1 => 'Tattapani',
        2 => 'Hot sulphur springs on the banks of Sutlej River, 51 km from Shimla. Perfect for relaxation.',
      ),
      6 => 
      array (
        0 => 'fa-apple-whole',
        1 => 'Narkanda',
        2 => 'Apple orchard country at 2,700 m. Skiing in winter, Hatu Peak and breathtaking Himalayan panoramas.',
      ),
      7 => 
      array (
        0 => 'fa-archway',
        1 => 'The Ridge',
        2 => 'Open space in heart of Shimla. Gaiety Cultural Complex, Christ Church and stunning views of snow-capped mountains.',
      ),
    ),
    'hlTag' => 
    array (
      0 => 'fa-solid fa-star',
      1 => 'Must Visit',
    ),
    'hlTitle' => 'Top Highlights of <span>Shimla</span>',
    'hlBg' => 'var(--green-light)',
    'galTag' => 
    array (
      0 => 'fa-solid fa-images',
      1 => 'Photo Gallery',
    ),
    'galTitle' => 'Shimla in <span>Pictures</span>',
    'galFallback' => 
    array (
      0 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1200&q=85',
        2 => 'Shimla hill station',
      ),
      1 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85',
        2 => 'Snow covered Shimla',
      ),
      2 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=700&q=85',
        2 => 'Pine forests Shimla',
      ),
      3 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85',
        2 => 'Shimla hills landscape',
      ),
      4 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85',
        2 => 'Shimla mountains green',
      ),
      5 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=700&q=85',
        2 => 'Forest trail Shimla',
      ),
    ),
    'rtTag' => 
    array (
      0 => 'fa-solid fa-route',
      1 => 'Book a Cab',
    ),
    'rtTitle' => 'Routes to <span>Shimla</span>',
    'rtDesc' => 'Easy road access from Delhi, Chandigarh and Pathankot with our expert mountain drivers.',
    'rtBg' => 'var(--cream)',
    'routes' => 
    array (
      0 => 
      array (
        0 => 'Delhi',
        1 => 'Shimla',
        2 => '350 km',
        3 => '8–9 hrs',
        4 => 'Weekend Itinerary',
        5 => 'index.php?calc=shimla',
        6 => 'Plan My Trip',
      ),
      1 => 
      array (
        0 => 'Chandigarh',
        1 => 'Shimla',
        2 => '115 km',
        3 => '3–4 hrs',
        4 => 'Express Itinerary',
        5 => 'index.php?calc=shimla',
        6 => 'Plan My Trip',
      ),
      2 => 
      array (
        0 => 'Ambala',
        1 => 'Shimla',
        2 => '200 km',
        3 => '5–6 hrs',
        4 => 'Custom Itinerary',
        5 => 'index.php?calc=shimla',
        6 => 'Plan My Trip',
      ),
    ),
    'heroWarning' => NULL,
    'ctaH' => 'Ready to Visit <span>Shimla?</span>',
    'ctaP' => 'Share your dates and pickup city. We will suggest the right vehicle, timing and quote for a private Shimla journey.',
    'ctaHref' => 'index.php?calc=shimla',
    'ctaIcon' => 'fa-solid fa-calculator',
    'ctaBtn' => 'Plan My Trip',
    'ctaWa' => 'Hi, I want a private Shimla trip quote. Please help me plan the route.',
    'btTag' => 
    array (
      0 => 'fa-regular fa-calendar',
      1 => 'Travel Tips',
    ),
    'btTitle' => 'Best Time to Visit <span>Shimla</span>',
    'seasons' => 
    array (
      0 => 
      array (
        0 => 'season-summer',
        1 => 'fa-solid fa-sun',
        2 => 'Summer (Mar–Jun)',
        3 => 'Most popular — 15°C to 30°C. Escape the Delhi heat. Apple blossoms in spring. Book months in advance.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      1 => 
      array (
        0 => 'season-monsoon',
        1 => 'fa-solid fa-cloud-rain',
        2 => 'Monsoon (Jul–Sep)',
        3 => 'Green, lush landscapes but slippery roads. Landslide risk on some routes. Fewer crowds, lower prices.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
      2 => 
      array (
        0 => 'season-autumn',
        1 => 'fa-solid fa-leaf',
        2 => 'Autumn (Oct–Nov)',
        3 => 'Apple harvest season. Clear skies and crisp air. Best for scenic drives — Kufri, Narkanda and Chail look stunning.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      3 => 
      array (
        0 => 'season-winter',
        1 => 'fa-solid fa-snowflake',
        2 => 'Winter (Dec–Feb)',
        3 => 'Heavy snowfall, 2°C to 10°C. Kufri skiing season. Shimla under snow is magical. Perfect for snow holiday.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
    ),
  ),
  'dharamshala' => 
  array (
    'title' => 'Dharamshala & McLeodganj Tour | Himachal Safar',
    'desc' => 'Book Delhi to Dharamshala. McLeodganj, Triund Trek, Bhagsu Waterfall, Dalai Lama Temple — expert Kangra Valley travel with Himachal Safar.',
    'metaDesc' => 'Book Delhi to Dharamshala. McLeodganj, Triund Trek, Bhagsu Waterfall, Dalai Lama Temple — expert Kangra Valley travel with Himachal Safar.',
    'docTitle' => 'Dharamshala &amp; McLeodganj Tour | Himachal Safar',
    'crumb' => NULL,
    'heroAlt' => 'Dharamshala McLeodganj Himachal Pradesh',
    'name' => 'Dharamshala',
    'tagline' => 'Little Lhasa of India — Tibetan culture, misty mountains and the Triund trail',
    'heroTag' => 
    array (
      0 => 'fa-solid fa-mountain-sun',
      1 => 'Himachal Pradesh',
    ),
    'facts' => 
    array (
      0 => 
      array (
        0 => 'fa-solid fa-mountain',
        1 => '1,457 m Altitude',
      ),
      1 => 
      array (
        0 => 'fa-regular fa-calendar',
        1 => 'Mar – Nov Best Time',
      ),
      2 => 
      array (
        0 => 'fa-solid fa-route',
        1 => '480 km from Delhi',
      ),
      3 => 
      array (
        0 => 'fa-solid fa-temperature-half',
        1 => '5°C to 28°C',
      ),
    ),
    'heroBtn' => 'Plan Dharamshala Trip',
    'aboutTag' => 
    array (
      0 => 'fa-solid fa-info-circle',
      1 => 'About Dharamshala',
    ),
    'aboutTitle' => 'Home of the <span>Dalai Lama</span>',
    'aboutParas' => 
    array (
      0 => 'Dharamshala is a unique blend of Indian and Tibetan culture, nestled in the Kangra Valley with the Dhauladhar range as a dramatic backdrop. The upper part, McLeodganj, is known as "Little Lhasa" — the seat of the Tibetan government-in-exile and residence of His Holiness the 14th Dalai Lama.',
      1 => 'McLeodganj\'s cobblestone streets are lined with Tibetan cafés, monasteries, handcraft shops and trekking agencies. The Triund trek above McLeodganj is one of the most accessible and stunning high-altitude treks in Himachal Pradesh — a must-do for any visitor.',
    ),
    'aboutTags' => 
    array (
      0 => 'Tibetan Culture',
      1 => 'Triund Trek',
      2 => 'Cricket Stadium',
      3 => 'Waterfall Walks',
    ),
    'aboutAlt' => 'Dharamshala Kangra valley',
    'highlights' => 
    array (
      0 => 
      array (
        0 => 'fa-om',
        1 => 'Namgyal Monastery',
        2 => 'Residence monastery of the Dalai Lama. Visitors can attend prayer sessions and learn about Tibetan Buddhism.',
      ),
      1 => 
      array (
        0 => 'fa-person-hiking',
        1 => 'Triund Trek',
        2 => '9 km trek from McLeodganj to 2,875 m. Stunning 360° Dhauladhar views. Camping available at the top.',
      ),
      2 => 
      array (
        0 => 'fa-water',
        1 => 'Bhagsu Waterfall',
        2 => '20-minute walk from McLeodganj. Beautiful two-tiered waterfall in a rocky gorge — perfect day walk.',
      ),
      3 => 
      array (
        0 => 'fa-baseball',
        1 => 'HPCA Cricket Stadium',
        2 => 'One of the most scenic cricket grounds in the world, surrounded by snow-capped mountains. Iconic venue.',
      ),
      4 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Dalai Lama Temple',
        2 => 'The Tsuglagkhang complex — monks debating, prayer wheels and the magnificent central temple.',
      ),
      5 => 
      array (
        0 => 'fa-water',
        1 => 'Dal Lake Dharamshala',
        2 => '5 km from McLeodganj. Small mountain lake surrounded by cedar trees — peaceful picnic spot.',
      ),
      6 => 
      array (
        0 => 'fa-mountain',
        1 => 'Dharamkot',
        2 => 'Quiet village above McLeodganj. Yoga retreats, rock climbing and trekking base for Triund and Indrahar Pass.',
      ),
      7 => 
      array (
        0 => 'fa-church',
        1 => 'Church of St. John',
        2 => 'Gothic church built in 1852, in loving memory of Lord Elgin. Set in a quiet forest — serene heritage site.',
      ),
    ),
    'hlTag' => 
    array (
      0 => 'fa-solid fa-star',
      1 => 'Must Visit',
    ),
    'hlTitle' => 'Top Highlights of <span>Dharamshala</span>',
    'hlBg' => 'var(--green-light)',
    'galTag' => 
    array (
      0 => 'fa-solid fa-images',
      1 => 'Photo Gallery',
    ),
    'galTitle' => 'Dharamshala in <span>Pictures</span>',
    'galFallback' => 
    array (
      0 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1200&q=85',
        2 => 'Dharamshala mountain view',
      ),
      1 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85',
        2 => 'Mountain trek Triund',
      ),
      2 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=700&q=85',
        2 => 'Kangra valley landscape',
      ),
      3 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=700&q=85',
        2 => 'Forest hills Dharamshala',
      ),
      4 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1200&q=85',
        2 => 'Pine forest McLeodganj',
      ),
      5 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85',
        2 => 'Nature landscape Himachal',
      ),
    ),
    'rtTag' => 
    array (
      0 => 'fa-solid fa-route',
      1 => 'Book a Cab',
    ),
    'rtTitle' => 'Routes to <span>Dharamshala</span>',
    'rtDesc' => 'Book your cab from Delhi, Chandigarh or Pathankot — our drivers know the Kangra Valley roads perfectly.',
    'rtBg' => 'var(--cream)',
    'routes' => 
    array (
      0 => 
      array (
        0 => 'Delhi',
        1 => 'Dharamshala',
        2 => '480 km',
        3 => '10–12 hrs',
        4 => 'McLeodganj Special',
        5 => 'index.php?calc=dharamshala',
        6 => 'Plan My Trip',
      ),
      1 => 
      array (
        0 => 'Chandigarh',
        1 => 'Dharamshala',
        2 => '245 km',
        3 => '6–7 hrs',
        4 => 'Kangra Itinerary',
        5 => 'index.php?calc=dharamshala',
        6 => 'Plan My Trip',
      ),
      2 => 
      array (
        0 => 'Pathankot',
        1 => 'Dharamshala',
        2 => '100 km',
        3 => '2–3 hrs',
        4 => 'Airport Transfer',
        5 => 'index.php?calc=dharamshala',
        6 => 'Plan My Trip',
      ),
    ),
    'heroWarning' => NULL,
    'ctaH' => 'Plan Your <span>Dharamshala Trip?</span>',
    'ctaP' => 'Share your travel dates and pickup city. We will plan a clear Kangra Valley route with the right vehicle and driver.',
    'ctaHref' => 'index.php?calc=dharamshala',
    'ctaIcon' => 'fa-solid fa-calculator',
    'ctaBtn' => 'Plan My Trip',
    'ctaWa' => 'Hi, I want a private Dharamshala/McLeodganj trip quote. Please help me plan the route.',
    'btTag' => 
    array (
      0 => 'fa-regular fa-calendar',
      1 => 'Travel Tips',
    ),
    'btTitle' => 'Best Time to Visit <span>Dharamshala</span>',
    'seasons' => 
    array (
      0 => 
      array (
        0 => 'season-summer',
        1 => 'fa-solid fa-sun',
        2 => 'Spring/Summer (Mar–Jun)',
        3 => 'Best weather for Triund trek, 10°C–25°C. Rhododendrons in bloom. Perfect for outdoor activities.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      1 => 
      array (
        0 => 'season-monsoon',
        1 => 'fa-solid fa-cloud-rain',
        2 => 'Monsoon (Jul–Sep)',
        3 => 'Heavy rainfall. Landslide risk. Triund trek not advisable. However, valleys are lush and beautiful.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
      2 => 
      array (
        0 => 'season-autumn',
        1 => 'fa-solid fa-leaf',
        2 => 'Autumn (Oct–Nov)',
        3 => 'Clear mountain views, crisp air. Triund covered in snow by November. Best for photography.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      3 => 
      array (
        0 => 'season-winter',
        1 => 'fa-solid fa-snowflake',
        2 => 'Winter (Dec–Feb)',
        3 => 'Heavy snow above McLeodganj. Town itself is pleasant. Snowfall on Dhauladhar range is spectacular.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
    ),
  ),
  'dalhousie' => 
  array (
    'title' => 'Dalhousie Trips & Cab Booking | Himachal Safar',
    'desc' => 'Book Delhi to Dalhousie. Khajjiar (mini Switzerland), Dainkund Peak, Chamera Lake — peaceful hill station with Himachal Safar.',
    'metaDesc' => 'Book Delhi to Dalhousie. Khajjiar (mini Switzerland), Dainkund Peak, Chamera Lake — peaceful hill station with Himachal Safar.',
    'docTitle' => 'Dalhousie Trips &amp; Cab Booking | Himachal Safar',
    'crumb' => NULL,
    'heroAlt' => 'Dalhousie Khajjiar Himachal Pradesh',
    'name' => 'Dalhousie',
    'tagline' => 'Scotland of India — colonial bungalows, misty meadows and the magical Khajjiar',
    'heroTag' => 
    array (
      0 => 'fa-solid fa-mountain-sun',
      1 => 'Himachal Pradesh',
    ),
    'facts' => 
    array (
      0 => 
      array (
        0 => 'fa-solid fa-mountain',
        1 => '2,036 m Altitude',
      ),
      1 => 
      array (
        0 => 'fa-regular fa-calendar',
        1 => 'Mar – Jun, Oct – Nov',
      ),
      2 => 
      array (
        0 => 'fa-solid fa-route',
        1 => '560 km from Delhi',
      ),
      3 => 
      array (
        0 => 'fa-solid fa-temperature-half',
        1 => '−1°C to 24°C',
      ),
    ),
    'heroBtn' => 'Plan Dalhousie Trip',
    'aboutTag' => 
    array (
      0 => 'fa-solid fa-info-circle',
      1 => 'About Dalhousie',
    ),
    'aboutTitle' => 'Himachal\'s <span>Serene</span> Retreat',
    'aboutParas' => 
    array (
      0 => 'Dalhousie is a quaint colonial hill station spread over five hills — Kathalagh, Potreyn, Terah, Bakrota and Bhangora — in the Chamba district of Himachal Pradesh. Named after Lord Dalhousie, who established it as a British summer retreat in 1854, it retains a quiet, unhurried charm unlike the more touristy Shimla or Manali.',
      1 => 'Just 22 km from Dalhousie lies Khajjiar — famously called the "Mini Switzerland of India" — a saucer-shaped meadow with a small lake at its center, ringed by dense Deodar forests and backed by snow-capped Dhauladhar peaks. It is one of the most photographed spots in Himachal Pradesh.',
    ),
    'aboutTags' => 
    array (
      0 => 'Khajjiar Meadows',
      1 => 'Colonial Heritage',
      2 => 'Dense Deodar Forests',
      3 => 'Chamera Lake',
    ),
    'aboutAlt' => 'Dalhousie green hills Himachal',
    'highlights' => 
    array (
      0 => 
      array (
        0 => 'fa-leaf',
        1 => 'Khajjiar',
        2 => '"Mini Switzerland of India" — a magical circular meadow with a lake, Deodar forest and Dhauladhar backdrop.',
      ),
      1 => 
      array (
        0 => 'fa-mountain',
        1 => 'Dainkund Peak',
        2 => 'Highest point near Dalhousie at 2,755 m. A 2-km trek with panoramic 360° views of the Chamba Valley.',
      ),
      2 => 
      array (
        0 => 'fa-water',
        1 => 'Chamera Lake',
        2 => 'A beautiful reservoir lake on the Ravi River. Boating, picnicking and scenic views — perfect family outing.',
      ),
      3 => 
      array (
        0 => 'fa-person-hiking',
        1 => 'Bakrota Hills Walk',
        2 => 'A 5-km circular walk through oak and rhododendron forests. Best walk in Dalhousie for sunrise views.',
      ),
      4 => 
      array (
        0 => 'fa-paw',
        1 => 'Kalatop Wildlife',
        2 => 'Kalatop-Khajjiar Wildlife Sanctuary — Himalayan black bear, barking deer and incredible birdlife in Deodar forest.',
      ),
      5 => 
      array (
        0 => 'fa-church',
        1 => 'St. John\'s Church',
        2 => 'Beautiful 1863 Gothic church in the heart of Dalhousie. A peaceful colonial-era landmark.',
      ),
      6 => 
      array (
        0 => 'fa-person-walking',
        1 => 'Gandhi Chowk',
        2 => 'Lively central square with shops, restaurants and Subhash Baoli — the historic spring visited by Subhas Chandra Bose.',
      ),
      7 => 
      array (
        0 => 'fa-monument',
        1 => 'Chamba Town',
        2 => '56 km from Dalhousie. Ancient Chamba Kingdom palaces, Lakshmi Narayan temples and the famous Manimahesh Lake.',
      ),
    ),
    'hlTag' => 
    array (
      0 => 'fa-solid fa-star',
      1 => 'Must Visit',
    ),
    'hlTitle' => 'Top Highlights of <span>Dalhousie</span>',
    'hlBg' => 'var(--green-light)',
    'galTag' => 
    array (
      0 => 'fa-solid fa-images',
      1 => 'Photo Gallery',
    ),
    'galTitle' => 'Dalhousie in <span>Pictures</span>',
    'galFallback' => 
    array (
      0 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=85',
        2 => 'Dalhousie mountain meadow',
      ),
      1 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=700&q=85',
        2 => 'Green hills Dalhousie',
      ),
      2 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=700&q=85',
        2 => 'Forest trail Khajjiar',
      ),
      3 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=700&q=85',
        2 => 'Deodar forest Dalhousie',
      ),
      4 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=85',
        2 => 'Mountain road Dalhousie',
      ),
      5 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85',
        2 => 'Snow peaks near Dalhousie',
      ),
    ),
    'rtTag' => 
    array (
      0 => 'fa-solid fa-route',
      1 => 'Book a Cab',
    ),
    'rtTitle' => 'Routes to <span>Dalhousie</span>',
    'rtDesc' => 'We cover all major routes to Dalhousie — comfortable cabs, experienced drivers, transparent fares.',
    'rtBg' => 'var(--cream)',
    'routes' => 
    array (
      0 => 
      array (
        0 => 'Delhi',
        1 => 'Dalhousie',
        2 => '560 km',
        3 => '11–13 hrs',
        4 => 'Hill Escape Itinerary',
        5 => 'index.php?calc=dalhousie',
        6 => 'Plan My Trip',
      ),
      1 => 
      array (
        0 => 'Pathankot',
        1 => 'Dalhousie',
        2 => '80 km',
        3 => '2–3 hrs',
        4 => 'Airport Transfer',
        5 => 'index.php?calc=dalhousie',
        6 => 'Plan My Trip',
      ),
      2 => 
      array (
        0 => 'Amritsar',
        1 => 'Dalhousie',
        2 => '200 km',
        3 => '4–5 hrs',
        4 => 'Punjab Combo',
        5 => 'index.php?calc=dalhousie',
        6 => 'Plan My Trip',
      ),
    ),
    'heroWarning' => NULL,
    'ctaH' => 'Escape to <span>Dalhousie?</span>',
    'ctaP' => 'Share your dates and route preferences. We will shape a relaxed Dalhousie and Khajjiar plan with a clear quote.',
    'ctaHref' => 'index.php?calc=dalhousie',
    'ctaIcon' => 'fa-solid fa-calculator',
    'ctaBtn' => 'Plan My Trip',
    'ctaWa' => 'Hi, I want a private Dalhousie/Khajjiar trip quote. Please help me plan the route.',
    'btTag' => 
    array (
      0 => 'fa-regular fa-calendar',
      1 => 'Travel Tips',
    ),
    'btTitle' => 'Best Time to Visit <span>Dalhousie</span>',
    'seasons' => 
    array (
      0 => 
      array (
        0 => 'season-summer',
        1 => 'fa-solid fa-sun',
        2 => 'Summer (Mar–Jun)',
        3 => 'Perfect escape from North Indian heat. 10°C–24°C. Khajjiar at its greenest. Most popular time to visit.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      1 => 
      array (
        0 => 'season-monsoon',
        1 => 'fa-solid fa-cloud-rain',
        2 => 'Monsoon (Jul–Sep)',
        3 => 'Lush green Khajjiar meadows at their most beautiful. Rainfall can be heavy. Roads generally safe.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
      2 => 
      array (
        0 => 'season-autumn',
        1 => 'fa-solid fa-leaf',
        2 => 'Autumn (Oct–Nov)',
        3 => 'Clear skies, golden forests. First snow on Dhauladhar peaks. Excellent for photography and nature walks.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>',
      ),
      3 => 
      array (
        0 => 'season-winter',
        1 => 'fa-solid fa-snowflake',
        2 => 'Winter (Dec–Feb)',
        3 => 'Heavy snowfall, −1°C to 10°C. Khajjiar under snow is breathtaking. Ideal for snow lovers and honeymooners.',
        4 => '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>',
      ),
    ),
  ),
  'spiti' => 
  array (
    'title' => 'Spiti Valley Tour & Cab Booking | Himachal Safar',
    'desc' => 'Explore Spiti Valley with Himachal Safar. Key Monastery, Chandratal Lake, Kaza, Pin Valley — custom 8N/9D adventure itineraries with expert high-altitude drivers.',
    'metaDesc' => 'Explore Spiti Valley with Himachal Safar. Key Monastery, Chandratal Lake, Kaza, Pin Valley — custom 8N/9D adventure itineraries with expert high-altitude drivers.',
    'docTitle' => 'Spiti Valley Tour &amp; Cab Booking | Himachal Safar',
    'crumb' => 'Spiti Valley',
    'heroAlt' => 'Spiti Valley high altitude Himachal Pradesh',
    'name' => 'Spiti Valley',
    'tagline' => 'The Middle Land — ancient monasteries, barren moonscapes and some of the world\'s highest motorable roads',
    'heroTag' => 
    array (
      0 => 'fa-solid fa-person-hiking',
      1 => 'Adventure Destination',
    ),
    'facts' => 
    array (
      0 => 
      array (
        0 => 'fa-solid fa-mountain',
        1 => '3,800 m+ Altitude',
      ),
      1 => 
      array (
        0 => 'fa-regular fa-calendar',
        1 => 'Jun – Sep Only',
      ),
      2 => 
      array (
        0 => 'fa-solid fa-route',
        1 => '~700 km from Delhi',
      ),
      3 => 
      array (
        0 => 'fa-solid fa-temperature-half',
        1 => '−30°C to 15°C',
      ),
    ),
    'heroBtn' => 'Plan Spiti Trip',
    'aboutTag' => 
    array (
      0 => 'fa-solid fa-info-circle',
      1 => 'About Spiti Valley',
    ),
    'aboutTitle' => 'The Last <span>Forbidden Land</span>',
    'aboutParas' => 
    array (
      0 => 'Spiti Valley is a cold desert mountain valley nestled high in the Himalayas in north-eastern Himachal Pradesh. Bordered by Tibet to the east, it is one of the most remote and sparsely populated regions of India — and one of the most spectacular.',
      1 => 'At an average altitude of 3,800 metres, Spiti\'s landscape is dramatic and barren — rolling barren mountains, ancient Buddhist monasteries perched on cliffsides, high-altitude lakes and river valleys that look like they belong on another planet. Getting here requires expert drivers, acclimatization and planning. Our team specialises exclusively in Spiti expeditions.',
    ),
    'aboutTags' => 
    array (
      0 => 'Expert Drivers Required',
      1 => '9-Day Itineraries Available',
      2 => 'Jun–Sep Road Access',
      3 => '4WD Vehicles',
    ),
    'aboutAlt' => 'Spiti Valley high altitude landscape',
    'highlights' => 
    array (
      0 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Key Monastery',
        2 => '4,166 m high monastery, one of the largest in the area. Spectacular location on a rocky hilltop. Must-visit in Spiti.',
      ),
      1 => 
      array (
        0 => 'fa-water',
        1 => 'Chandratal Lake',
        2 => '"Moon Lake" at 14,100 ft. Crescent-shaped gem surrounded by snow-capped peaks. Camping at the lakeside.',
      ),
      2 => 
      array (
        0 => 'fa-city',
        1 => 'Kaza',
        2 => 'District headquarters of Spiti at 3,800 m. Base camp for Spiti explorations. Markets, guesthouses and warm locals.',
      ),
      3 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Dhankar Monastery',
        2 => 'Built on a cliff 1,000 m above the confluence of Spiti and Pin rivers. One of the most dramatic monastery locations.',
      ),
      4 => 
      array (
        0 => 'fa-mountain',
        1 => 'Pin Valley',
        2 => 'National Park with some of Spiti\'s rarest wildlife — Snow Leopard, Ibex, Bharal. Mud village with oldest gompa.',
      ),
      5 => 
      array (
        0 => 'fa-house',
        1 => 'Kibber Village',
        2 => 'One of the highest villages in the world with a road. At 4,270 m — a true edge-of-the-world experience.',
      ),
      6 => 
      array (
        0 => 'fa-place-of-worship',
        1 => 'Tabo Monastery',
        2 => '1,000-year-old monastery in Tabo, called the "Ajanta of the Himalayas". Ancient frescoes and clay sculptures.',
      ),
      7 => 
      array (
        0 => 'fa-road',
        1 => 'Kunzum Pass',
        2 => '4,590 m high pass — dramatic gateway to Spiti from Manali via Lahaul. The highest motorable pass in Himachal.',
      ),
    ),
    'hlTag' => 
    array (
      0 => 'fa-solid fa-star',
      1 => 'Must Visit',
    ),
    'hlTitle' => 'Top Highlights of <span>Spiti Valley</span>',
    'hlBg' => 'var(--green-light)',
    'galTag' => 
    array (
      0 => 'fa-solid fa-images',
      1 => 'Photo Gallery',
    ),
    'galTitle' => 'Spiti Valley in <span>Pictures</span>',
    'galFallback' => 
    array (
      0 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=1200&q=85',
        2 => 'Spiti Valley high altitude',
      ),
      1 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=700&q=85',
        2 => 'Mountain pass Spiti',
      ),
      2 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=700&q=85',
        2 => 'Snow peaks Spiti Valley',
      ),
      3 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1548013584-24e97daa89c6?auto=format&fit=crop&w=700&q=85',
        2 => 'Mountain landscape Spiti',
      ),
      4 => 
      array (
        0 => 'gi-wide',
        1 => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&q=85',
        2 => 'Himalayan peaks Spiti area',
      ),
      5 => 
      array (
        0 => '',
        1 => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?auto=format&fit=crop&w=700&q=85',
        2 => 'Remote valley Himachal',
      ),
    ),
    'rtTag' => 
    array (
      0 => 'fa-solid fa-route',
      1 => 'Plan Your Spiti Trip',
    ),
    'rtTitle' => 'Routes to <span>Spiti Valley</span>',
    'rtDesc' => 'Two ways to enter Spiti — via Manali (Kunzum Pass) or via Shimla (Kinnaur). We cover both. Both require expert high-altitude drivers.',
    'rtBg' => 'var(--cream)',
    'routes' => 
    array (
      0 => 
      array (
        0 => 'Delhi via Manali',
        1 => 'Spiti',
        2 => '~700 km',
        3 => '2 days',
        4 => 'Adventure Route',
        5 => 'index.php?calc=spiti',
        6 => 'Plan My Trip',
      ),
      1 => 
      array (
        0 => 'Delhi via Shimla',
        1 => 'Spiti',
        2 => '~650 km',
        3 => '2 days',
        4 => 'Kinnaur Circuit',
        5 => 'index.php?calc=spiti',
        6 => 'Plan My Trip',
      ),
      2 => 
      array (
        0 => '8N/9D Itinerary',
        1 => 'Full Spiti Circuit',
        2 => 'Full Circuit',
        3 => '9 Days',
        4 => 'All-Inclusive',
        5 => 'index.php#contact',
        6 => 'Plan This Trip',
      ),
    ),
    'heroWarning' => 
    array (
      'icon' => 'fa-solid fa-triangle-exclamation',
      'text' => 'High altitude destination — expert drivers mandatory. We specialise in Spiti routes.',
    ),
    'ctaH' => 'Plan a Serious <span>Spiti Valley</span> Route',
    'ctaP' => 'This is not an ordinary hill trip. Our Spiti specialists plan the permits, acclimatization, vehicle choice and driver assignment carefully.',
    'ctaHref' => 'index.php#contact',
    'ctaIcon' => 'fa-solid fa-calendar-check',
    'ctaBtn' => 'Plan My Spiti Trip',
    'ctaWa' => 'Hi, I want to plan a private Spiti Valley trip. Please share route options and availability.',
    'advisory' => 
    array (
      'bg' => 'var(--green-light)',
      'icon' => 'fa-solid fa-triangle-exclamation',
      'title' => 'Important: Spiti Valley Travel Advisory',
      'items' => 
      array (
        0 => 
        array (
          0 => 'Road Open Season:',
          1 => 'June to September only. Roads close due to heavy snowfall in winter.',
        ),
        1 => 
        array (
          0 => 'Inner Line Permit:',
          1 => 'Required for some areas near the China border. We assist with permit documentation.',
        ),
        2 => 
        array (
          0 => 'Altitude Sickness:',
          1 => 'Acclimatize in Manali or Kaza for 1-2 days before ascending further. Do not rush.',
        ),
        3 => 
        array (
          0 => '4WD Vehicles:',
          1 => 'We use only suitable SUVs (Innova Crysta / Fortuner) for Spiti. No sedans.',
        ),
        4 => 
        array (
          0 => 'Fuel:',
          1 => 'Fill up at Kaza — the last reliable petrol pump. Our drivers know every stop.',
        ),
      ),
    ),
  ),
);
