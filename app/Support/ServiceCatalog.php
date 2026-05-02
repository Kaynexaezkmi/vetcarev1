<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ServiceCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function items(): array
    {
        return [
            [
                'key' => 'grooming',
                'title' => 'Grooming',
                'description' => 'Bath, nail trim, ear cleaning, and fur grooming.',
                'intro' => 'Our grooming service keeps your pet clean, healthy, and looking their best. We use gentle, pet-safe products and professional techniques to ensure your furry friend feels comfortable and cared for.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'grooming',
                'features' => [
                    ['title' => 'Professional Care', 'text' => 'Handled by experienced groomers.'],
                    ['title' => 'Safe Products', 'text' => 'We use pet-safe and gentle products.'],
                    ['title' => 'Stress-Free', 'text' => 'A calm and comfortable environment.'],
                ],
                'catPricing' => [
                    ['name' => 'Full Groom', 'details' => 'Bath, haircut, nail trim, ear cleaning, anal gland expression, paw pad trim.', 'price' => '₱700', 'amount' => 700],
                    ['name' => 'Basic Groom', 'details' => 'Bath, blow dry, nail trim, ear cleaning.', 'price' => '₱550', 'amount' => 550],
                ],
                'pricing' => [
                    ['size' => 'Small', 'weight' => 'Up to 10 kg', 'breeds' => 'Shih Tzu, Pomeranian, Chihuahua, etc.', 'basic' => '₱600', 'basicAmount' => 600, 'full' => '₱850', 'fullAmount' => 850],
                    ['size' => 'Medium', 'weight' => '10 - 20 kg', 'breeds' => 'Beagle, Cocker Spaniel, French Bulldog, etc.', 'basic' => '₱800', 'basicAmount' => 800, 'full' => '₱1,100', 'fullAmount' => 1100],
                    ['size' => 'Large', 'weight' => '20 kg & above', 'breeds' => 'Golden Retriever, Husky, Labrador, etc.', 'basic' => '₱1,100', 'basicAmount' => 1100, 'full' => '₱1,500', 'fullAmount' => 1500],
                ],
            ],
            [
                'key' => 'deworming',
                'title' => 'Deworming',
                'description' => 'Regular deworming helps protect your pet from internal parasites.',
                'intro' => 'Regular deworming helps protect your pet from internal parasites and supports their overall health and wellbeing.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'price-list',
                'sectionTitle' => 'Deworming',
                'sectionSubtitle' => 'Price is based on your pet\'s weight.',
                'features' => [
                    ['title' => 'Prevents Parasites', 'text' => 'Helps eliminate and prevent worms effectively.'],
                    ['title' => 'Safe & Effective', 'text' => 'Vet-recommended dewormers for your pet\'s safety.'],
                    ['title' => 'Better Health', 'text' => 'Supports digestion, immunity, and overall health.'],
                ],
                'pricing' => [
                    ['label' => '1 - 2 kg', 'price' => '₱250', 'amount' => 250],
                    ['label' => '2 - 5 kg', 'price' => '₱300', 'amount' => 300],
                    ['label' => '+ 5 kg', 'price' => '+ ₱50 every 5 kg', 'amount' => 350],
                ],
                'note' => 'Add ₱50 for every additional 5 kg of body weight.',
            ],
            [
                'key' => 'surgery',
                'title' => 'Surgery',
                'description' => 'Major and minor surgical procedures.',
                'intro' => 'Our surgical services are performed with expert care and advanced techniques to ensure your pet\'s safety and fast recovery.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'option-list',
                'sectionTitle' => 'Surgical Services',
                'sectionSubtitle' => 'Choose the type of surgery to learn more and book.',
                'features' => [
                    ['title' => 'Safe & Sterile Environment', 'text' => 'All surgeries are performed in a fully equipped and sterile facility.'],
                    ['title' => 'Experienced Veterinarians', 'text' => 'Our skilled vets ensure the best care before, during, and after surgery.'],
                    ['title' => 'Comfort & Recovery', 'text' => 'We prioritize your pet\'s comfort and a smooth, stress-free recovery.'],
                ],
                'options' => [
                    ['name' => 'Major Surgery', 'details' => 'Complex surgical procedures that require general anesthesia and extended recovery time. Performed by experienced veterinarians with advanced monitoring.', 'price' => '₱850', 'amount' => 850],
                    ['name' => 'Minor Surgery', 'details' => 'Routine surgical procedures that are less complex and have shorter recovery time. Safe and effective treatments for common health issues.', 'price' => '₱850', 'amount' => 850],
                ],
            ],
            [
                'key' => 'health-wellness',
                'title' => 'Health & Wellness',
                'aliases' => ['Wellness'],
                'description' => 'Preventive care to keep your pet healthy and protected.',
                'intro' => 'Our wellness service helps monitor your pet\'s health, prevent illness, and keep them protected with routine care.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'single-price',
                'sectionTitle' => 'Health & Wellness',
                'sectionSubtitle' => 'Includes preventive health assessment and care guidance.',
                'price' => '₱500',
                'amount' => 500,
                'features' => [
                    ['title' => 'Preventive Care', 'text' => 'Routine checks that help catch health concerns early.'],
                    ['title' => 'Personal Guidance', 'text' => 'Care recommendations based on your pet\'s needs.'],
                    ['title' => 'Ongoing Protection', 'text' => 'Keeps your pet healthier between clinic visits.'],
                ],
                'includedTitle' => 'What\'s Included',
                'included' => ['Wellness assessment', 'Health monitoring', 'Care recommendations', 'Preventive guidance'],
            ],
            [
                'key' => 'consultation',
                'title' => 'Consultation',
                'aliases' => ['General Checkup'],
                'description' => 'General health check and veterinarian consultation.',
                'intro' => 'Get professional advice and guidance from our veterinarians for your pet\'s health and concerns.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'single-price',
                'sectionTitle' => 'Consultation Fee',
                'sectionSubtitle' => 'Includes physical exam and professional advice.',
                'price' => '₱500',
                'amount' => 500,
                'features' => [
                    ['title' => 'Expert Advice', 'text' => 'Accurate diagnosis and health recommendations.'],
                    ['title' => 'Health Monitoring', 'text' => 'Regular check-ups for a healthy, happy pet.'],
                    ['title' => 'Personalized Care', 'text' => 'Tailored treatment and care plans for your pet.'],
                ],
                'includedTitle' => 'What\'s Included',
                'included' => ['Physical examination', 'Health assessment', 'Diet & care recommendations', 'Treatment plan if needed'],
            ],
            [
                'key' => 'x-ray',
                'title' => 'X-Ray',
                'description' => 'Digital x-ray and veterinarian interpretation.',
                'intro' => 'Digital X-ray helps us see what\'s inside your pet\'s body to accurately diagnose and treat conditions.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'single-price',
                'sectionTitle' => 'X-Ray Service',
                'sectionSubtitle' => 'Includes digital x-ray and veterinarian interpretation.',
                'price' => '₱1,500',
                'amount' => 1500,
                'features' => [
                    ['title' => 'Accurate Diagnosis', 'text' => 'Helps detect fractures, foreign objects, and internal issues.'],
                    ['title' => 'Fast & Safe', 'text' => 'Quick procedure with minimal stress for your pet.'],
                    ['title' => 'Advanced Equipment', 'text' => 'Digital X-ray for clear and precise results.'],
                ],
                'includedTitle' => 'Common Uses',
                'included' => ['Fractures & bone issues', 'Chest & lung problems', 'Abdominal concerns', 'Foreign object detection'],
            ],
            [
                'key' => 'cbc',
                'title' => 'CBC',
                'aliases' => ['Blood Test'],
                'description' => 'Blood extraction and laboratory analysis.',
                'intro' => 'CBC helps assess your pet\'s overall health by checking the different components of their blood.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'single-price',
                'sectionTitle' => 'CBC Test',
                'sectionSubtitle' => 'Includes blood extraction and laboratory analysis.',
                'price' => '₱1,000',
                'amount' => 1000,
                'features' => [
                    ['title' => 'Health Screening', 'text' => 'Detects infections, anemia, and other conditions.'],
                    ['title' => 'Early Detection', 'text' => 'Helps catch health issues early for better treatment.'],
                    ['title' => 'Reliable Results', 'text' => 'Accurate and quick laboratory analysis.'],
                ],
                'includedTitle' => 'Tests Include',
                'included' => ['Red Blood Cells (RBC)', 'White Blood Cells (WBC)', 'Hemoglobin / Hematocrit', 'Platelets'],
            ],
            [
                'key' => 'bloodchem',
                'title' => 'Blood Chem',
                'aliases' => ['Bloodchem', 'Blood Chemistry'],
                'description' => 'Blood chemistry testing for organ function and health screening.',
                'intro' => 'Blood chemistry testing helps evaluate organ function and gives our veterinarians a clearer picture of your pet\'s internal health.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'single-price',
                'sectionTitle' => 'Blood Chemistry',
                'sectionSubtitle' => 'Includes blood extraction and chemistry panel analysis.',
                'price' => '₱2,000',
                'amount' => 2000,
                'features' => [
                    ['title' => 'Organ Screening', 'text' => 'Helps assess liver, kidney, and metabolic health.'],
                    ['title' => 'Clearer Diagnosis', 'text' => 'Provides useful data for treatment planning.'],
                    ['title' => 'Laboratory Analysis', 'text' => 'Processed for reliable veterinary interpretation.'],
                ],
                'includedTitle' => 'Tests Include',
                'included' => ['Blood extraction', 'Chemistry panel', 'Veterinarian review', 'Health recommendations'],
            ],
            [
                'key' => 'confinement',
                'title' => 'Confinement',
                'description' => 'Supervised care during treatment or recovery.',
                'intro' => 'Safe, clean, and comfortable care for pets that need close monitoring or recovery support.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'option-list',
                'sectionTitle' => 'Confinement Service',
                'sectionSubtitle' => 'For pets that need supervised care during treatment or recovery.',
                'features' => [
                    ['title' => 'Safe & Secure Environment', 'text' => 'Our facilities are secure, clean, and monitored for your pet\'s safety.'],
                    ['title' => 'Comfort & Care', 'text' => 'We provide cozy spaces, clean bedding, fresh food, and attentive care.'],
                    ['title' => 'Supervised by Professionals', 'text' => 'Our trained staff and veterinarians closely monitor your pet\'s wellbeing.'],
                ],
                'options' => [
                    ['name' => 'Confinement', 'details' => 'Special care and restricted activity for pets that need close monitoring, treatment support, or recovery time.', 'price' => '₱2,000', 'amount' => 2000],
                ],
                'note' => 'Bring your pet\'s food, bedding, and essentials for a more comfortable stay.',
            ],
            [
                'key' => 'vaccination',
                'title' => 'Vaccination',
                'description' => 'Protect your pet with essential vaccines.',
                'intro' => 'Vaccination helps protect your pet from common diseases and supports long-term health.',
                'image' => 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                'layout' => 'option-list',
                'sectionTitle' => 'Vaccination Services',
                'sectionSubtitle' => 'Choose your vaccination type.',
                'features' => [
                    ['title' => 'Disease Protection', 'text' => 'Helps prevent serious and contagious diseases.'],
                    ['title' => 'Safe & Effective', 'text' => 'We only use high-quality and vet-approved vaccines.'],
                    ['title' => 'Healthy & Active', 'text' => 'Keeps your pet strong, energetic, and full of life.'],
                ],
                'options' => [
                    ['name' => '5in1 Vaccine', 'details' => 'Protects against Feline Panleukopenia, Calicivirus, Herpesvirus, Rhinotracheitis, and Chlamydophila.', 'price' => '₱500', 'amount' => 500],
                    ['name' => 'Anti Rabies Vaccine', 'details' => 'Protects your pet from rabies virus and other related risks.', 'price' => '₱350', 'amount' => 350],
                    ['name' => 'Kennel Cough Vaccine', 'details' => 'Helps protect against Bordetella bronchiseptica, a common cause of respiratory infection.', 'price' => '₱750', 'amount' => 750],
                ],
            ],
        ];
    }

    public static function forServices(Collection $services): Collection
    {
        $catalog = collect(self::items())
            ->map(function (array $item) use ($services): array {
                $record = $services->first(function ($service) use ($item): bool {
                    $serviceName = self::normalizeName($service->name);
                    $catalogNames = collect([$item['title'], $item['key'], ...($item['aliases'] ?? [])])
                        ->map(fn (string $name): string => self::normalizeName($name));

                    return $catalogNames->contains(function (string $catalogName) use ($serviceName): bool {
                        return $serviceName === $catalogName
                            || str_contains($serviceName, $catalogName)
                            || str_contains($catalogName, $serviceName);
                    });
                });

                if ($record?->image && file_exists(public_path($record->image))) {
                    $item['image'] = $record->image;
                }

                $item['service_id'] = $record?->id;
                $item['database_price'] = (float) ($record?->price ?? 0);
                $item['booking_amount'] = self::bookingAmount($item);
                $item['booking_price'] = self::formatPeso($item['booking_amount']);

                return $item;
            })
            ->values();

        $matchedServiceIds = $catalog
            ->pluck('service_id')
            ->filter()
            ->all();

        $services
            ->reject(fn ($service): bool => in_array($service->id, $matchedServiceIds, true))
            ->each(function ($service) use ($catalog): void {
                $catalog->push([
                    'key' => self::normalizeName($service->name),
                    'title' => $service->name,
                    'description' => $service->description ?: 'Professional veterinary service for your pet.',
                    'intro' => $service->description ?: 'Professional veterinary service for your pet.',
                    'image' => $service->image ?: 'images/services/1774290704_istockphoto-1421577226-612x612.jpg',
                    'layout' => 'single-price',
                    'sectionTitle' => $service->name,
                    'sectionSubtitle' => $service->description ?: 'Professional veterinary service for your pet.',
                    'price' => self::formatPeso((float) $service->price),
                    'amount' => (float) $service->price,
                    'features' => [],
                    'includedTitle' => 'Service Details',
                    'included' => [$service->duration ? $service->duration.' minutes' : 'Duration to be confirmed by the clinic'],
                    'service_id' => $service->id,
                    'database_price' => (float) $service->price,
                    'booking_amount' => (float) $service->price,
                    'booking_price' => self::formatPeso((float) $service->price),
                ]);
            });

        return $catalog->values();
    }

    public static function normalizeName(string $name): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower($name));
    }

    protected static function bookingAmount(array $item): float
    {
        if (isset($item['amount'])) {
            return (float) $item['amount'];
        }

        if (isset($item['pricing'][0]['amount'])) {
            return (float) $item['pricing'][0]['amount'];
        }

        if (isset($item['pricing'][0]['basicAmount'])) {
            return (float) $item['pricing'][0]['basicAmount'];
        }

        if (isset($item['options'][0]['amount'])) {
            return (float) $item['options'][0]['amount'];
        }

        if (isset($item['catPricing'][0]['amount'])) {
            return (float) $item['catPricing'][0]['amount'];
        }

        if (($item['database_price'] ?? 0) > 0) {
            return (float) $item['database_price'];
        }

        return 0.0;
    }

    protected static function formatPeso(float $amount): string
    {
        return '₱'.number_format($amount, 2);
    }
}
