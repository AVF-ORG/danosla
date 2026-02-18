<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [

            ['Dairy sector', 'Secteur laitier', 'قطاع الألبان'],
            ['Agriculture', 'Agriculture', 'الزراعة'],
            ['Manufacturing Industry', 'Industrie manufacturière', 'الصناعة التحويلية'],
            ['Chemicals and Pharmaceuticals', 'Chimie et Pharmaceutique', 'الصناعات الكيميائية والدوائية'],
            ['Oil and Gas Industry', 'Industrie pétrolière et gazière', 'صناعة النفط والغاز'],
            ['Mining and Extraction', 'Exploitation minière', 'التعدين والاستخراج'],
            ['Construction and Civil Engineering', 'Construction et génie civil', 'البناء والهندسة المدنية'],
            ['Food and Beverage Sector', 'Secteur agroalimentaire', 'قطاع الأغذية والمشروبات'],
            ['Health and Medical Products', 'Produits de santé et médicaux', 'المنتجات الصحية والطبية'],
            ['Technology and Electronics', 'Technologie et électronique', 'التكنولوجيا والإلكترونيات'],
            ['Automotive and Spare Parts', 'Automobile et pièces de rechange', 'السيارات وقطع الغيار'],
            ['Textile and Apparel', 'Textile et habillement', 'النسيج والملابس'],
            ['Wood and Paper Industry', 'Industrie du bois et du papier', 'صناعة الخشب والورق'],
            ['Metallurgical and Steel Products', 'Produits métallurgiques et sidérurgiques', 'المنتجات المعدنية والحديدية'],
            ['Consumer Goods and Durable Goods', 'Biens de consommation et durables', 'السلع الاستهلاكية والمعمرة'],
            ['Renewable Energy Sector', 'Secteur des énergies renouvelables', 'قطاع الطاقة المتجددة'],
            ['Logistics and Distribution Sector', 'Secteur logistique et distribution', 'قطاع اللوجستيات والتوزيع'],
            ['Agricultural Chemicals and Fertilizers', 'Produits chimiques agricoles et engrais', 'الكيماويات الزراعية والأسمدة'],
            ['Financial and Banking Sector', 'Secteur financier et bancaire', 'القطاع المالي والمصرفي'],
            ['Other', 'Autre', 'أخرى'],
        ];

        foreach ($sectors as $sector) {

            [$en, $fr, $ar] = $sector;

            Sector::updateOrCreate(
                ['slug->en' => Str::slug($en)],
                [
                    'name' => [
                        'en' => $en,
                        'fr' => $fr,
                        'ar' => $ar,
                    ],
                    'slug' => [
                        'en' => Str::slug($en),
                        'fr' => Str::slug($fr),
                        'ar' => Str::slug($en), // keep English slug for routing consistency
                    ],
                    'is_active' => true,
                ]
            );
        }
    }
}
