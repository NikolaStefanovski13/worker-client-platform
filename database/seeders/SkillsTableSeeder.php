<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillsTableSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            'Plumbing' => ['Residential Plumbing', 'Commercial Plumbing', 'Drainage', 'Pipe Fitting'],
            'Electrical' => ['Residential Wiring', 'Commercial Electrical', 'Lighting', 'Electrical Repairs'],
            'Carpentry' => ['Framing', 'Cabinetry', 'Woodworking', 'Furniture Making'],
            'Roofing' => ['Shingle Roofing', 'Metal Roofing', 'Roof Repair', 'Gutter Installation'],
            'HVAC' => ['AC Installation', 'Heating Systems', 'Ventilation', 'HVAC Repair'],
            'Painting' => ['Interior Painting', 'Exterior Painting', 'Staining', 'Wall Texturing'],
            'Landscaping' => ['Lawn Care', 'Garden Design', 'Irrigation', 'Tree Service'],
            'Cleaning' => ['House Cleaning', 'Commercial Cleaning', 'Deep Cleaning', 'Move-out Cleaning'],
        ];

        foreach ($skills as $category => $skillNames) {
            foreach ($skillNames as $name) {
                Skill::create([
                    'name' => $name,
                    'category' => $category,
                ]);
            }
        }
    }
}
