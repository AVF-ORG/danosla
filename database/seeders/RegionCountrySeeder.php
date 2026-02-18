<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionCountrySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Regions
        |--------------------------------------------------------------------------
        */

        $eu = Region::updateOrCreate(
            ['code' => 'EU'],
            [
                'name' => [
                    'en' => 'European Union',
                    'fr' => 'Union européenne',
                    'ar' => 'الاتحاد الأوروبي',
                ],
            ]
        );

        $eea = Region::updateOrCreate(
            ['code' => 'EEA'],
            [
                'name' => [
                    'en' => 'European Economic Area',
                    'fr' => 'Espace économique européen',
                    'ar' => 'المنطقة الاقتصادية الأوروبية',
                ],
            ]
        );

        $non = Region::updateOrCreate(
            ['code' => 'NON_EU_EEA'],
            [
                'name' => [
                    'en' => 'European countries outside EU & EEA',
                    'fr' => 'Pays européens hors UE et EEE',
                    'ar' => 'دول أوروبية خارج الاتحاد الأوروبي والمنطقة الاقتصادية الأوروبية',
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Countries (from your select list)
        |--------------------------------------------------------------------------
        */

        $countries = [

            // EU
            [['en' => 'Austria', 'fr' => 'Autriche', 'ar' => 'النمسا'], 'AT', '+43', 'EU'],
            [['en' => 'Belgium', 'fr' => 'Belgique', 'ar' => 'بلجيكا'], 'BE', '+32', 'EU'],
            [['en' => 'Bulgaria', 'fr' => 'Bulgarie', 'ar' => 'بلغاريا'], 'BG', '+359', 'EU'],
            [['en' => 'Croatia', 'fr' => 'Croatie', 'ar' => 'كرواتيا'], 'HR', '+385', 'EU'],
            [['en' => 'Cyprus', 'fr' => 'Chypre', 'ar' => 'قبرص'], 'CY', '+357', 'EU'],
            [['en' => 'Czech Republic', 'fr' => 'République tchèque', 'ar' => 'التشيك'], 'CZ', '+420', 'EU'],
            [['en' => 'Denmark', 'fr' => 'Danemark', 'ar' => 'الدنمارك'], 'DK', '+45', 'EU'],
            [['en' => 'Estonia', 'fr' => 'Estonie', 'ar' => 'إستونيا'], 'EE', '+372', 'EU'],
            [['en' => 'Finland', 'fr' => 'Finlande', 'ar' => 'فنلندا'], 'FI', '+358', 'EU'],
            [['en' => 'France', 'fr' => 'France', 'ar' => 'فرنسا'], 'FR', '+33', 'EU'],
            [['en' => 'Germany', 'fr' => 'Allemagne', 'ar' => 'ألمانيا'], 'DE', '+49', 'EU'],
            [['en' => 'Greece', 'fr' => 'Grèce', 'ar' => 'اليونان'], 'GR', '+30', 'EU'],
            [['en' => 'Hungary', 'fr' => 'Hongrie', 'ar' => 'المجر'], 'HU', '+36', 'EU'],
            [['en' => 'Ireland', 'fr' => 'Irlande', 'ar' => 'أيرلندا'], 'IE', '+353', 'EU'],
            [['en' => 'Italy', 'fr' => 'Italie', 'ar' => 'إيطاليا'], 'IT', '+39', 'EU'],
            [['en' => 'Latvia', 'fr' => 'Lettonie', 'ar' => 'لاتفيا'], 'LV', '+371', 'EU'],
            [['en' => 'Lithuania', 'fr' => 'Lituanie', 'ar' => 'ليتوانيا'], 'LT', '+370', 'EU'],
            [['en' => 'Luxembourg', 'fr' => 'Luxembourg', 'ar' => 'لوكسمبورغ'], 'LU', '+352', 'EU'],
            [['en' => 'Malta', 'fr' => 'Malte', 'ar' => 'مالطا'], 'MT', '+356', 'EU'],
            [['en' => 'Netherlands', 'fr' => 'Pays-Bas', 'ar' => 'هولندا'], 'NL', '+31', 'EU'],
            [['en' => 'Poland', 'fr' => 'Pologne', 'ar' => 'بولندا'], 'PL', '+48', 'EU'],
            [['en' => 'Portugal', 'fr' => 'Portugal', 'ar' => 'البرتغال'], 'PT', '+351', 'EU'],
            [['en' => 'Romania', 'fr' => 'Roumanie', 'ar' => 'رومانيا'], 'RO', '+40', 'EU'],
            [['en' => 'Slovakia', 'fr' => 'Slovaquie', 'ar' => 'سلوفاكيا'], 'SK', '+421', 'EU'],
            [['en' => 'Slovenia', 'fr' => 'Slovénie', 'ar' => 'سلوفينيا'], 'SI', '+386', 'EU'],
            [['en' => 'Spain', 'fr' => 'Espagne', 'ar' => 'إسبانيا'], 'ES', '+34', 'EU'],
            [['en' => 'Sweden', 'fr' => 'Suède', 'ar' => 'السويد'], 'SE', '+46', 'EU'],

            // EEA only
            [['en' => 'Iceland', 'fr' => 'Islande', 'ar' => 'آيسلندا'], 'IS', '+354', 'EEA'],
            [['en' => 'Liechtenstein', 'fr' => 'Liechtenstein', 'ar' => 'ليختنشتاين'], 'LI', '+423', 'EEA'],
            [['en' => 'Norway', 'fr' => 'Norvège', 'ar' => 'النرويج'], 'NO', '+47', 'EEA'],

            // NON EU / EEA
            [['en' => 'United Kingdom', 'fr' => 'Royaume-Uni', 'ar' => 'المملكة المتحدة'], 'GB', '+44', 'NON'],
            [['en' => 'Switzerland', 'fr' => 'Suisse', 'ar' => 'سويسرا'], 'CH', '+41', 'NON'],
            [['en' => 'Ukraine', 'fr' => 'Ukraine', 'ar' => 'أوكرانيا'], 'UA', '+380', 'NON'],
            [['en' => 'Moldova', 'fr' => 'Moldavie', 'ar' => 'مولدوفا'], 'MD', '+373', 'NON'],
            [['en' => 'Serbia', 'fr' => 'Serbie', 'ar' => 'صربيا'], 'RS', '+381', 'NON'],
            [['en' => 'North Macedonia', 'fr' => 'Macédoine du Nord', 'ar' => 'مقدونيا الشمالية'], 'MK', '+389', 'NON'],
            [['en' => 'Bosnia and Herzegovina', 'fr' => 'Bosnie-Herzégovine', 'ar' => 'البوسنة والهرسك'], 'BA', '+387', 'NON'],
            [['en' => 'Montenegro', 'fr' => 'Monténégro', 'ar' => 'الجبل الأسود'], 'ME', '+382', 'NON'],
            [['en' => 'Kosovo', 'fr' => 'Kosovo', 'ar' => 'كوسوفو'], 'XK', '+383', 'NON'],
        ];

        foreach ($countries as $item) {

            [$name, $iso, $code, $regionType] = $item;

            $country = Country::updateOrCreate(
                ['iso2' => $iso],
                [
                    'name' => $name,
                    'international_code' => $code,
                ]
            );

            if ($regionType === 'EU') {
                $country->regions()->syncWithoutDetaching([$eu->id]);
            }

            if ($regionType === 'EEA') {
                $country->regions()->syncWithoutDetaching([$eea->id]);
            }

            if ($regionType === 'NON') {
                $country->regions()->syncWithoutDetaching([$non->id]);
            }
        }
    }
}
