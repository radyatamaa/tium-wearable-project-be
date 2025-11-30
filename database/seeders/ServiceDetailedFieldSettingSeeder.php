<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceDetailedFieldSetting;

class ServiceDetailedFieldSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceDetailedFieldSetting::create([
            'service_classification' => 'medical_institution',
            'service_classification_desc' => '의료기관', // Medical institution
            'medical_subject' => '소아과,산부인과,신경외과', // Pediatrics, Obstetrics and Gynecology, Neurosurgery
            'detailed_field_by_service' => '3/3'
        ]);

        ServiceDetailedFieldSetting::create([
            'service_classification' => 'industrial_scene',
            'service_classification_desc' => '산업현장', // Industrial scene
            'medical_subject' => '토목,설비,용접', // Civil engineering, Equipment, Welding
            'detailed_field_by_service' => '3/3'
        ]);

        ServiceDetailedFieldSetting::create([
            'service_classification' => 'public_welfare',
            'service_classification_desc' => '공공복지', //Public welfare
            'medical_subject' => '지원,복지,기타', //Support, Welfare
            'detailed_field_by_service' => '3/3'
        ]);
    }
}