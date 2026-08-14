<?php

namespace Database\Seeders;

use App\Models\AccommodationType;
use App\Models\ConservationArea;
use App\Models\Package;
use Illuminate\Database\Seeder;

class ConservationAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'code' => 'DVCA',
                'name' => 'Danum Valley Conservation Area',
                'short_name' => 'Danum Valley',
                'slug' => 'danum-valley',
                'description' => 'One of the oldest and most biodiverse rainforests in the world, Danum Valley is a pristine 438 km² lowland dipterocarp forest in the heart of Sabah.',
                'about' => 'Danum Valley Conservation Area (DVCA) represents one of the last remaining areas of undisturbed lowland dipterocarp forest in South-East Asia. Managed by Yayasan Sabah, this extraordinary rainforest has been designated a UNESCO World Heritage Site candidate. Home to diverse mega-fauna including Bornean pygmy elephants, orangutans, clouded leopards, and over 340 bird species, Danum Valley offers unparalleled wildlife experiences. The Danum Valley Field Centre (DVFC) supports world-class scientific research, while the Borneo Rainforest Lodge provides an exclusive nature retreat.',
                'location' => 'Lahad Datu, Sabah',
                'area_hectares' => 43800,
                'cover_image' => 'areas/dvca.jpg',
                'highlights' => ['Wildlife spotting', 'Night safaris', 'Canopy walkway', 'River swimming', 'Research programs', 'Guided forest treks'],
                'wildlife' => ['Bornean Pygmy Elephant', 'Orangutan', 'Clouded Leopard', 'Sun Bear', 'Proboscis Monkey', 'Hornbills', 'Saltwater Crocodile'],
                'best_time_to_visit' => 'March to October',
                'difficulty_level' => 'moderate',
                'sort_order' => 1,
            ],
            [
                'code' => 'MBCA',
                'name' => 'Maliau Basin Conservation Area',
                'short_name' => 'Maliau Basin',
                'slug' => 'maliau-basin',
                'description' => 'Known as Sabah\'s "Lost World", the Maliau Basin is a stunning sunken plateau surrounded by steep escarpments, hiding pristine rainforests and spectacular waterfalls.',
                'about' => 'Maliau Basin Conservation Area (MBCA) is one of Sabah\'s last great wilderness areas, covering approximately 58,840 hectares. Enclosed by a natural rim of mountains rising 1,600–1,900m, this "Lost World" remained virtually unknown until the 1980s. The basin\'s isolation has preserved extraordinary biodiversity, including rare pitcher plants, ancient forests, and diverse wildlife. Seven impressive tiered waterfalls cascade through the basin, offering adventurous trekking experiences. The area is managed by Yayasan Sabah for conservation and sustainable ecotourism.',
                'location' => 'Tawau, Sabah',
                'area_hectares' => 58840,
                'cover_image' => 'areas/mbca.jpg',
                'highlights' => ['Maliau Falls', 'Multi-day treks', 'Pitcher plants', 'Canopy observation', 'Rare bird watching', 'Research station'],
                'wildlife' => ['Clouded Leopard', 'Bornean Orangutan', 'Banteng', 'Slow Loris', 'Giant Squirrel', 'Banded Langur'],
                'best_time_to_visit' => 'April to September',
                'difficulty_level' => 'challenging',
                'sort_order' => 2,
            ],
            [
                'code' => 'ICCA',
                'name' => 'Imbak Canyon Conservation Area',
                'short_name' => 'Imbak Canyon',
                'slug' => 'imbak-canyon',
                'description' => 'A spectacular canyon carved by the Imbak River through pristine rainforest, offering breathtaking scenery and rich biodiversity in the heart of Sabah.',
                'about' => 'Imbak Canyon Conservation Area (ICCA) covers 30,000 hectares of virtually undisturbed tropical rainforest in the central part of Sabah. The canyon, carved by the Imbak River, creates a stunning landscape of towering cliffs, waterfalls, and dense forest. As one of Sabah\'s last true wilderness areas, ICCA protects exceptional biodiversity including many endemic and rare species. Scientific research programmes have uncovered numerous new species here. Managed by Yayasan Sabah, the area offers challenging expeditions for adventurous naturalists.',
                'location' => 'Tongod, Sabah',
                'area_hectares' => 30000,
                'cover_image' => 'areas/icca.jpg',
                'highlights' => ['Canyon trekking', 'River exploration', 'Waterfall visits', 'Wildlife surveys', 'Photography', 'Camping expeditions'],
                'wildlife' => ['Bornean Pygmy Elephant', 'Orangutan', 'Helmeted Hornbill', 'Flat-headed Cat', 'Otter Civet', 'Giant Bornean Earthworm'],
                'best_time_to_visit' => 'May to September',
                'difficulty_level' => 'challenging',
                'sort_order' => 3,
            ],
            [
                'code' => 'SCCA',
                'name' => 'Silam Coast Conservation Area',
                'short_name' => 'Silam Coast',
                'slug' => 'silam-coast',
                'description' => 'A beautiful coastal conservation area along the Darvel Bay, featuring mangroves, coastal forests, and rich marine biodiversity with stunning sea views.',
                'about' => 'Silam Coast Conservation Area (SCCA) protects a unique coastal and marine ecosystem along Darvel Bay in eastern Sabah. The area encompasses mangrove forests, coastal dipterocarp forest, and marine habitats that are vital for numerous species. Silam is renowned for its proboscis monkey population inhabiting the mangroves, as well as diverse marine life including dugongs, sea turtles, and reef fish. The Silam Forest Reserve forms an important buffer zone and provides spectacular views of Darvel Bay and the surrounding islands.',
                'location' => 'Lahad Datu, Sabah',
                'area_hectares' => 8000,
                'cover_image' => 'areas/scca.jpg',
                'highlights' => ['Mangrove boat tours', 'Proboscis monkey watching', 'Marine snorkeling', 'Coastal bird watching', 'Sunset views', 'Traditional fishing community'],
                'wildlife' => ['Proboscis Monkey', 'Irrawaddy Dolphin', 'Sea Turtle', 'Dugong', 'Kingfishers', 'Mangrove Snake'],
                'best_time_to_visit' => 'March to October',
                'difficulty_level' => 'easy',
                'sort_order' => 4,
            ],
            [
                'code' => 'TRCA',
                'name' => 'Taliwas River Conservation Area',
                'short_name' => 'Taliwas River',
                'slug' => 'taliwas-river',
                'description' => 'A pristine riverine forest conservation area along the Taliwas River, offering peaceful river experiences and rich biodiversity within an intact Bornean ecosystem.',
                'about' => 'Taliwas River Conservation Area (TRCA) protects the unique riparian ecosystems along the Taliwas River corridor in Sabah. The area features extensive riverine forests, oxbow lakes, and wetland habitats that support high biodiversity. River-based activities including boat safaris offer excellent opportunities to spot wildlife along the riverbanks. The conservation area plays a crucial role in maintaining water catchment and protecting freshwater biodiversity. It is managed by Yayasan Sabah as part of the broader Heart of Borneo initiative.',
                'location' => 'Kinabatangan, Sabah',
                'area_hectares' => 12000,
                'cover_image' => 'areas/trca.jpg',
                'highlights' => ['River boat safaris', 'Oxbow lake visits', 'Night spotting', 'Firefly watching', 'Fishing experiences', 'Birding tours'],
                'wildlife' => ['Pygmy Elephant', 'Proboscis Monkey', 'Estuarine Crocodile', 'Oriental Darter', 'Storm\'s Stork', 'Rhinoceros Hornbill'],
                'best_time_to_visit' => 'February to October',
                'difficulty_level' => 'easy',
                'sort_order' => 5,
            ],
            [
                'code' => 'INFAPRO',
                'name' => 'Innoprise-FACE Foundation Rainforest Rehabilitation Project',
                'short_name' => 'INFAPRO',
                'slug' => 'infapro',
                'description' => 'A pioneering rainforest rehabilitation project restoring logged-over forest with native tree species, offering a unique insight into tropical forest restoration and conservation.',
                'about' => 'The Innoprise-FACE Foundation Rainforest Rehabilitation Project (INFAPRO) is a landmark conservation initiative in Sabah covering 25,000 hectares of logged-over forest in the Ulu Segama-Malua Forest Reserve. Established in 1992, the project aims to restore degraded dipterocarp forest through planting of native tree species. INFAPRO has planted millions of trees and created wildlife corridors connecting intact forest patches. The project demonstrates the viability of large-scale tropical forest restoration and serves as a model for similar programmes worldwide. Visitors can participate in tree-planting activities and learn about forest restoration ecology.',
                'location' => 'Lahad Datu, Sabah',
                'area_hectares' => 25000,
                'cover_image' => 'areas/infapro.jpg',
                'highlights' => ['Tree planting participation', 'Nursery visits', 'Forest restoration trails', 'Educational programs', 'Wildlife corridors', 'Research opportunities'],
                'wildlife' => ['Orangutan', 'Sun Bear', 'Bearded Pig', 'Various Hornbills', 'Forest Birds', 'Reptiles'],
                'best_time_to_visit' => 'April to October',
                'difficulty_level' => 'easy',
                'sort_order' => 6,
            ],
            [
                'code' => 'INIKEA',
                'name' => 'Innoprise-IKEA Tropical Forest Rehabilitation Project',
                'short_name' => 'INIKEA',
                'slug' => 'inikea',
                'description' => 'A corporate conservation partnership between Yayasan Sabah and IKEA to restore and protect tropical rainforest in Sabah, contributing to global sustainability goals.',
                'about' => 'The Innoprise-IKEA Tropical Forest Rehabilitation Project (INIKEA) is a partnership between Yayasan Sabah\'s Innoprise Corporation and IKEA to restore degraded forest land in Sabah. This initiative covers approximately 25,000 hectares and focuses on rehabilitating logged-over forest with native dipterocarp species. The project not only restores biodiversity and ecosystem services but also sequesters significant amounts of carbon. INIKEA represents a model for corporate sustainability partnerships that deliver measurable conservation outcomes. Educational tours showcase how business partnerships can contribute to forest conservation.',
                'location' => 'Sabah',
                'area_hectares' => 25000,
                'cover_image' => 'areas/inikea.jpg',
                'highlights' => ['Sustainability education', 'Tree planting', 'Forest monitoring', 'Carbon offset programs', 'Corporate tours', 'School programs'],
                'wildlife' => ['Forest Birds', 'Small Mammals', 'Butterflies', 'Reptiles', 'Reintroduced Species'],
                'best_time_to_visit' => 'Year-round',
                'difficulty_level' => 'easy',
                'sort_order' => 7,
            ],
        ];

        foreach ($areas as $areaData) {
            $area = ConservationArea::create($areaData);
            $this->seedAccommodations($area);
            $this->seedPackages($area);
        }
    }

    private function seedAccommodations(ConservationArea $area): void
    {
        $accommodations = [
            'DVCA' => [
                ['name' => 'Borneo Rainforest Lodge Chalet', 'type' => 'chalet', 'capacity' => 2, 'price_per_night' => 650, 'price_per_night_foreigner' => 850, 'amenities' => ['En-suite bathroom', 'Air conditioning', 'Mini bar', 'Verandah', 'Forest view']],
                ['name' => 'Field Centre Hostel', 'type' => 'hostel', 'capacity' => 4, 'price_per_night' => 120, 'price_per_night_foreigner' => 180, 'amenities' => ['Shared bathroom', 'Fan', 'Locker', 'Meals available']],
                ['name' => 'Canopy Suite', 'type' => 'suite', 'capacity' => 2, 'price_per_night' => 950, 'price_per_night_foreigner' => 1200, 'amenities' => ['En-suite bathroom', 'Air conditioning', 'Elevated deck', 'Premium forest view', 'Private guide']],
            ],
            'MBCA' => [
                ['name' => 'Agathis Camp Dormitory', 'type' => 'dormitory', 'capacity' => 8, 'price_per_night' => 80, 'price_per_night_foreigner' => 120, 'amenities' => ['Shared facilities', 'Basic meals', 'Trekking gear rental']],
                ['name' => 'Camel Trophy Camp', 'type' => 'camp', 'capacity' => 4, 'price_per_night' => 180, 'price_per_night_foreigner' => 260, 'amenities' => ['Tent accommodation', 'Meals', 'Guide included', 'Trekking equipment']],
            ],
            'ICCA' => [
                ['name' => 'Base Camp Tent', 'type' => 'camping', 'capacity' => 2, 'price_per_night' => 90, 'price_per_night_foreigner' => 140, 'amenities' => ['Tent', 'Sleeping bag', 'Meals', 'Guide']],
                ['name' => 'Research Lodge Room', 'type' => 'lodge', 'capacity' => 2, 'price_per_night' => 200, 'price_per_night_foreigner' => 300, 'amenities' => ['Private room', 'Shared bathroom', 'Meals', 'Research access']],
            ],
            'SCCA' => [
                ['name' => 'Coastal Chalet', 'type' => 'chalet', 'capacity' => 3, 'price_per_night' => 280, 'price_per_night_foreigner' => 380, 'amenities' => ['Sea view', 'Air conditioning', 'En-suite', 'Balcony']],
                ['name' => 'Mangrove Eco-Lodge', 'type' => 'lodge', 'capacity' => 2, 'price_per_night' => 220, 'price_per_night_foreigner' => 300, 'amenities' => ['Mangrove view', 'Fan', 'Private bathroom', 'Boat access']],
            ],
            'TRCA' => [
                ['name' => 'Riverfront Chalet', 'type' => 'chalet', 'capacity' => 2, 'price_per_night' => 320, 'price_per_night_foreigner' => 420, 'amenities' => ['River view', 'Air conditioning', 'En-suite bathroom', 'Boat safari included']],
                ['name' => 'Longhouse Room', 'type' => 'guesthouse', 'capacity' => 2, 'price_per_night' => 150, 'price_per_night_foreigner' => 220, 'amenities' => ['Shared bathroom', 'Fan', 'Local experience', 'Meals']],
            ],
            'INFAPRO' => [
                ['name' => 'Rehabilitation Centre Lodge', 'type' => 'lodge', 'capacity' => 4, 'price_per_night' => 160, 'price_per_night_foreigner' => 240, 'amenities' => ['Basic rooms', 'Meals', 'Tree planting kit', 'Guide']],
            ],
            'INIKEA' => [
                ['name' => 'Forest Education Centre Room', 'type' => 'guesthouse', 'capacity' => 2, 'price_per_night' => 160, 'price_per_night_foreigner' => 240, 'amenities' => ['Basic room', 'Meals', 'Educational materials', 'Guided tour']],
            ],
        ];

        foreach ($accommodations[$area->code] ?? [] as $acc) {
            AccommodationType::create(array_merge($acc, [
                'conservation_area_id' => $area->id,
                'description' => 'Comfortable accommodation at ' . $area->short_name . '.',
            ]));
        }
    }

    private function seedPackages(ConservationArea $area): void
    {
        $packages = [
            'DVCA' => [
                [
                    'name' => '3D2N Danum Valley Explorer',
                    'slug' => 'dvca-3d2n-explorer',
                    'description' => 'An immersive 3-day experience in the ancient Danum Valley rainforest with guided night walks, canopy walks, and wildlife spotting.',
                    'duration_days' => 3,
                    'min_pax' => 2,
                    'max_pax' => 8,
                    'price_per_person' => 1200,
                    'price_per_person_foreigner' => 1600,
                    'inclusions' => ['2 nights accommodation', 'All meals', 'Professional nature guide', 'Night safari', 'Canopy walkway access', 'Airport transfer (Lahad Datu)'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal expenses', 'Tips'],
                    'sort_order' => 1,
                ],
                [
                    'name' => '5D4N Danum Valley Deep Forest',
                    'slug' => 'dvca-5d4n-deep-forest',
                    'description' => 'A comprehensive deep forest experience with extended wildlife monitoring, research station visits, and multiple trail explorations.',
                    'duration_days' => 5,
                    'min_pax' => 2,
                    'max_pax' => 6,
                    'price_per_person' => 2800,
                    'price_per_person_foreigner' => 3500,
                    'inclusions' => ['4 nights accommodation', 'All meals', 'Expert naturalist guide', '2 night safaris', 'Research station visit', 'Canopy walkway', 'All transfers'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal expenses'],
                    'sort_order' => 2,
                ],
            ],
            'MBCA' => [
                [
                    'name' => '4D3N Maliau Basin Adventure Trek',
                    'slug' => 'mbca-4d3n-adventure-trek',
                    'description' => 'Trek through the mysterious Lost World of Maliau Basin, discovering its legendary waterfalls and extraordinary biodiversity.',
                    'duration_days' => 4,
                    'min_pax' => 4,
                    'max_pax' => 10,
                    'price_per_person' => 1800,
                    'price_per_person_foreigner' => 2400,
                    'inclusions' => ['3 nights camp accommodation', 'All meals', 'Certified guide', 'Trekking equipment', 'Waterfall visits', 'Safety briefing'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal trekking gear', 'Tips'],
                    'sort_order' => 1,
                ],
                [
                    'name' => '7D6N Maliau Basin Full Circuit',
                    'slug' => 'mbca-7d6n-full-circuit',
                    'description' => 'Complete the iconic Maliau Basin circular trek, visiting all major waterfalls and experiencing the full range of ecosystems.',
                    'duration_days' => 7,
                    'min_pax' => 4,
                    'max_pax' => 8,
                    'price_per_person' => 3500,
                    'price_per_person_foreigner' => 4500,
                    'inclusions' => ['6 nights camp/lodge', 'All meals', 'Expert guides', 'Full trekking kit', 'All park fees', 'Emergency support'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal gear', 'Tips'],
                    'sort_order' => 2,
                ],
            ],
            'ICCA' => [
                [
                    'name' => '3D2N Imbak Canyon Explorer',
                    'slug' => 'icca-3d2n-explorer',
                    'description' => 'Discover the hidden wonders of Imbak Canyon with guided treks along the canyon rim and river valley.',
                    'duration_days' => 3,
                    'min_pax' => 2,
                    'max_pax' => 8,
                    'price_per_person' => 1100,
                    'price_per_person_foreigner' => 1500,
                    'inclusions' => ['2 nights accommodation', 'All meals', 'Guide', 'Canyon trek', 'Waterfall visit'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal gear'],
                    'sort_order' => 1,
                ],
            ],
            'SCCA' => [
                [
                    'name' => '2D1N Silam Coast Nature Escape',
                    'slug' => 'scca-2d1n-nature-escape',
                    'description' => 'A relaxing coastal conservation experience with mangrove boat tours, proboscis monkey spotting, and marine exploration.',
                    'duration_days' => 2,
                    'min_pax' => 2,
                    'max_pax' => 10,
                    'price_per_person' => 580,
                    'price_per_person_foreigner' => 780,
                    'inclusions' => ['1 night accommodation', 'All meals', 'Mangrove boat tour', 'Snorkeling equipment', 'Nature guide'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal expenses'],
                    'sort_order' => 1,
                ],
            ],
            'TRCA' => [
                [
                    'name' => '2D1N Taliwas River Safari',
                    'slug' => 'trca-2d1n-river-safari',
                    'description' => 'Experience the magic of a Bornean river ecosystem with boat safaris, wildlife spotting, and evening firefly watching.',
                    'duration_days' => 2,
                    'min_pax' => 2,
                    'max_pax' => 8,
                    'price_per_person' => 620,
                    'price_per_person_foreigner' => 820,
                    'inclusions' => ['1 night accommodation', 'All meals', 'Boat safari (AM & PM)', 'Night spotting', 'Firefly watching', 'Nature guide'],
                    'exclusions' => ['Flights', 'Travel insurance', 'Personal expenses'],
                    'sort_order' => 1,
                ],
            ],
            'INFAPRO' => [
                [
                    'name' => '1-Day INFAPRO Conservation Experience',
                    'slug' => 'infapro-1day-conservation',
                    'description' => 'Participate in active forest restoration by planting native trees, visiting the nursery, and learning about rainforest rehabilitation.',
                    'duration_days' => 1,
                    'min_pax' => 5,
                    'max_pax' => 30,
                    'price_per_person' => 180,
                    'price_per_person_foreigner' => 280,
                    'inclusions' => ['Tree planting activity', 'Nursery tour', 'Lunch', 'Educational materials', 'Certificate of participation'],
                    'exclusions' => ['Transport', 'Travel insurance'],
                    'sort_order' => 1,
                ],
            ],
            'INIKEA' => [
                [
                    'name' => '1-Day INIKEA Forest Sustainability Tour',
                    'slug' => 'inikea-1day-sustainability',
                    'description' => 'Learn about corporate conservation partnerships and sustainable forestry through guided tours of the IKEA-Yayasan Sabah rehabilitation project.',
                    'duration_days' => 1,
                    'min_pax' => 5,
                    'max_pax' => 30,
                    'price_per_person' => 160,
                    'price_per_person_foreigner' => 260,
                    'inclusions' => ['Guided forest tour', 'Tree planting', 'Sustainability talk', 'Lunch', 'Planting certificate'],
                    'exclusions' => ['Transport', 'Travel insurance'],
                    'sort_order' => 1,
                ],
            ],
        ];

        foreach ($packages[$area->code] ?? [] as $pkg) {
            Package::create(array_merge($pkg, [
                'conservation_area_id' => $area->id,
                'is_active' => true,
            ]));
        }
    }
}
