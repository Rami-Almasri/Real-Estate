<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\District;
use App\Models\House;
use App\Models\Office;
use App\Models\Preference;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private array $covers = [
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=900&q=80',
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=900&q=80',
        'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=900&q=80',
        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=900&q=80',
        'https://images.unsplash.com/photo-1430285561322-7808604715df?w=900&q=80',
        'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900&q=80',
        'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=900&q=80',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&q=80',
        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=900&q=80',
        'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?w=900&q=80',
        'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=900&q=80',
    ];

    private array $directions = ['شمالي', 'جنوبي', 'شرقي', 'غربي', 'شمالي شرقي', 'جنوبي غربي'];

    public function run(): void
    {
        $this->call([CitySeeder::class, DistrictSeeder::class]);

        $districts = District::all();

        // ---- Real-estate offices (with provider users + subscriptions) ----
        $officesData = [
            ['name' => 'مكتب الشام العقاري', 'email' => 'office@gmail.com', 'address' => 'شارع 29 أيار، أبو رمانة', 'district' => 2, 'plan' => 'elite',  'days_left' => 25],
            ['name' => 'دار القصر للعقارات',  'email' => 'qasr@aqar.sy',     'address' => 'أوتوستراد المزة', 'district' => 1, 'plan' => 'pro',    'days_left' => 18],
            ['name' => 'مكتب حلب الذهبي',     'email' => 'aleppo@aqar.sy',   'address' => 'شارع الفرقان الرئيسي', 'district' => 6, 'plan' => 'pro',    'days_left' => 5],
            ['name' => 'عقارات الساحل',        'email' => 'coast@aqar.sy',    'address' => 'كورنيش اللاذقية', 'district' => 11, 'plan' => 'basic',  'days_left' => 12],
            ['name' => 'مكتب الوسام',          'email' => 'wisam@aqar.sy',    'address' => 'حي الإنشاءات، حمص', 'district' => 9, 'plan' => null,     'days_left' => 0],
        ];

        $offices = collect();
        foreach ($officesData as $od) {
            $district = $districts->firstWhere('id', $od['district']) ?? $districts->first();
            $office = Office::create([
                'address'     => $od['address'],
                'district_id' => $district->id,
                'latitude'    => $district->latitude,
                'longitude'   => $district->longitude,
            ]);
            $office->provider()->create([
                'name'     => $od['name'],
                'email'    => $od['email'],
                'password' => Hash::make('111111'),
            ]);
            if ($od['plan']) {
                $plan = config("plans.{$od['plan']}");
                $office->subscriptions()->create([
                    'plan'          => $od['plan'],
                    'price'         => $plan['price'],
                    'listing_limit' => $plan['listing_limit'],
                    'start_date'    => now()->subDays(30 - $od['days_left'])->toDateString(),
                    'end_date'      => now()->addDays($od['days_left'])->toDateString(),
                    'is_active'     => true,
                ]);
            }
            $offices->push($office);
        }

        // ---- Buyer accounts ----
        $buyerNames = ['أحمد العلي', 'ليلى حسن', 'محمد خير', 'رنا سعيد', 'عمر الديب', 'سارة نور', 'يوسف حمدان', 'هبة كنعان'];
        $buyers = collect();
        foreach ($buyerNames as $i => $name) {
            $buyers->push(User::create([
                'name'          => $name,
                'email'         => 'buyer' . ($i + 1) . '@aqar.sy',
                'password'      => Hash::make('111111'),
                'userable_type' => User::class,
                'userable_id'   => 0,
            ]));
        }
        // Keep the original demo buyer login working.
        $buyers->push(User::create([
            'name' => 'مستخدم تجريبي', 'email' => 'test@gmail.com',
            'password' => Hash::make('111111'), 'userable_type' => User::class, 'userable_id' => 0,
        ]));

        // ---- Houses ----
        $titlesSale = ['شقة فاخرة بإطلالة مفتوحة', 'منزل عائلي واسع', 'شقة حديثة التشطيب', 'بنتهاوس بتراس كبير', 'شقة استثمارية مميزة', 'دوبلكس راقٍ', 'شقة بتشطيب سوبر ديلوكس'];
        $titlesRent = ['شقة مفروشة للإيجار', 'شقة عائلية للإيجار', 'استوديو أنيق', 'شقة قرب الخدمات', 'شقة مشمسة للإيجار', 'شقة مجددة بالكامل'];

        $houseIds = [];
        foreach (range(1, 64) as $n) {
            $office = $offices[($n - 1) % $offices->count()];
            // Only offices with an active plan list properties.
            if (! $office->activeSubscription()) {
                $office = $offices->first(fn ($o) => $o->activeSubscription());
            }
            $district = $districts->random();
            $isSale = mt_rand(1, 100) <= 68;
            $area = $isSale ? mt_rand(90, 320) : mt_rand(60, 180);
            $rooms = max(1, (int) round($area / mt_rand(35, 55)));
            $ppm = $isSale ? mt_rand(900, 2600) : mt_rand(4, 9);
            $price = $isSale ? $ppm * $area : $ppm * $area; // rent ~ small $/m² → monthly
            $occupied = mt_rand(1, 100) <= 22;

            $created = now()->subDays(mt_rand(0, 175));

            $house = House::create([
                'office_id'   => $office->id,
                'district_id' => $district->id,
                'title'       => ($isSale ? $titlesSale[array_rand($titlesSale)] : $titlesRent[array_rand($titlesRent)]) . ' - ' . $district->name,
                'description' => 'عقار متميز في ' . $district->name . ' يتألف من ' . $rooms . ' غرف بمساحة ' . $area . ' م²، تشطيب ممتاز وموقع استراتيجي قريب من جميع الخدمات والمواصلات. فرصة لا تعوّض.',
                'cover_image' => $this->covers[array_rand($this->covers)],
                'featured'    => mt_rand(1, 100) <= 22,
                'status'      => $occupied ? 'occupied' : 'empty',
                'closed_at'   => $occupied ? $created->copy()->addDays(mt_rand(1, 20)) : null,
                'type'        => $isSale ? 'sale' : 'rent',
                'rooms'       => $rooms,
                'floor'       => mt_rand(0, 9),
                'area'        => $area,
                'direction'   => $this->directions[array_rand($this->directions)],
                'price'       => $price,
                'latitude'    => $district->latitude + (mt_rand(-40, 40) / 10000),
                'longitude'   => $district->longitude + (mt_rand(-40, 40) / 10000),
            ]);
            // Backdate for the 6-month price-trend chart.
            $house->timestamps = false;
            $house->created_at = $created;
            $house->updated_at = $created;
            $house->save();
            $house->timestamps = true;

            $houseIds[] = $house->id;
        }

        // ---- Views & ratings (bulk for performance) ----
        $views = [];
        $rates = [];
        foreach ($houseIds as $hid) {
            $vCount = mt_rand(8, 120);
            for ($i = 0; $i < $vCount; $i++) {
                $views[] = ['house_id' => $hid, 'user_id' => $buyers->random()->id, 'created_at' => now(), 'updated_at' => now()];
            }
            foreach (range(1, mt_rand(0, 5)) as $r) {
                $rates[] = ['house_id' => $hid, 'user_id' => $buyers->random()->id, 'rating' => mt_rand(3, 5), 'created_at' => now(), 'updated_at' => now()];
            }
        }
        foreach (array_chunk($views, 500) as $chunk) {
            DB::table('views')->insert($chunk);
        }
        foreach (array_chunk($rates, 500) as $chunk) {
            DB::table('rates')->insert($chunk);
        }

        // ---- Buyer preferences (drives matching) ----
        $prefs = [
            ['type' => 'sale', 'city_id' => 1, 'district_id' => 1,  'max_price' => 250000, 'min_rooms' => 3, 'label' => 'شقة تمليك بالمزة'],
            ['type' => 'rent', 'city_id' => 1, 'district_id' => null,'max_price' => 700,    'min_rooms' => 2, 'label' => 'إيجار في دمشق'],
            ['type' => 'sale', 'city_id' => 2, 'district_id' => 6,  'max_price' => 180000, 'min_rooms' => 2, 'label' => 'تمليك بحلب'],
            ['type' => 'sale', 'city_id' => 1, 'district_id' => null,'max_price' => 400000, 'min_rooms' => 4, 'min_area' => 180, 'label' => 'فيلا/دوبلكس'],
            ['type' => 'rent', 'city_id' => 4, 'district_id' => 11, 'max_price' => 500,    'min_rooms' => 1, 'label' => 'إيجار باللاذقية'],
            ['type' => null,   'city_id' => 1, 'district_id' => 3,  'max_price' => 300000, 'min_rooms' => 3, 'label' => 'أي عقار بالمالكي'],
            ['type' => 'sale', 'city_id' => 3, 'district_id' => null,'max_price' => 150000, 'min_rooms' => 2, 'label' => 'تمليك بحمص'],
        ];
        $matching = app(MatchingService::class);
        foreach ($prefs as $i => $p) {
            $preference = Preference::create(array_merge($p, [
                'user_id'   => $buyers[$i % $buyers->count()]->id,
                'is_active' => true,
            ]));
            $matching->runForPreference($preference);
        }

        // ---- Sample contracts (with due-date variety) for the demo office ----
        $demoOffice = $offices->first();
        $occupiedHouses = House::where('office_id', $demoOffice->id)->get();
        if ($occupiedHouses->isEmpty()) {
            $occupiedHouses = House::take(4)->get();
        }
        $contractSpecs = [
            ['cycle' => 'monthly',   'type' => 'rent', 'due' => -3,  'name' => 'خالد إبراهيم'],   // overdue
            ['cycle' => 'monthly',   'type' => 'rent', 'due' => 6,   'name' => 'فاطمة الحلبي'],   // due soon
            ['cycle' => 'quarterly', 'type' => 'rent', 'due' => 20,  'name' => 'سامر يوسف'],
            ['cycle' => 'once',      'type' => 'sale', 'due' => null, 'name' => 'نور الدين عباس'],
        ];
        foreach ($contractSpecs as $i => $cs) {
            $house = $occupiedHouses[$i % max(1, $occupiedHouses->count())];
            $start = now()->subMonths(mt_rand(1, 6));
            Contract::create([
                'office_id'     => $demoOffice->id,
                'house_id'      => $house->id,
                'type'          => $cs['type'],
                'party_name'    => $cs['name'],
                'party_phone'   => '09' . mt_rand(10000000, 99999999),
                'amount'        => $cs['type'] === 'sale' ? $house->price : max(150, round($house->price)),
                'payment_cycle' => $cs['cycle'],
                'start_date'    => $start->toDateString(),
                'end_date'      => $cs['type'] === 'sale' ? null : $start->copy()->addYear()->toDateString(),
                'due_date'      => $cs['due'] === null ? null : now()->addDays($cs['due'])->toDateString(),
                'status'        => 'active',
                'reference'     => 'SY-' . now()->format('Ymd') . '-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
            ]);
        }

        $this->command->info('✅ تم إنشاء بيانات تجريبية غنية: ' . count($houseIds) . ' عقار، ' . $offices->count() . ' مكاتب، ' . $buyers->count() . ' مشترٍ.');
    }
}
