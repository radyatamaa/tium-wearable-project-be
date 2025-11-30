<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceJobSetting;

class ServiceJobSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceJobSetting::create([
            'service_classification' => 'medical_institution',
            'service_classification_desc' => '의료기관', // Medical institution
            'job_title' => '최고 관리자,중간 관리자,일반 관리자', // Chief Administrator, Middle Administrator, General Administrator
            'position' => '직원,의사,간호사', // Staff, Doctor, Nurses
            'status_by_service' => '20/12',
            'color_dashboard' => 'custom-bg-blue',
            'icon' => '/icons/dashboard/medical-institution.svg',
        ]);

        ServiceJobSetting::create([
            'service_classification' => 'industrial_scene',
            'service_classification_desc' => '산업현장', // Industrial scene
            'job_title' => '최고 관리자,중간 관리자,일반 관리자', // Chief Administrator, Middle Administrator, General Administrator
            'position' => '팀장,책임,부서장,본부장', // Team leader, person in charge, department head, division head
            'status_by_service' => '20/12',
            'color_dashboard' => 'custom-bg-purple',
            'icon' => '/icons/dashboard/industrial-scene.svg',
        ]);

        ServiceJobSetting::create([
            'service_classification' => 'public_welfare',
            'service_classification_desc' => '공공복지', //Public welfare
            'job_title' => '최고 관리자,중간 관리자,일반 관리자', // Chief Administrator, Middle Administrator, General Administrator
            'position' => '팀장,책임,부서장,본부장', // Team leader, person in charge, department head, division head
            'status_by_service' => '20/12',
            'color_dashboard' => 'custom-bg-light-blue',
            'icon' => '/icons/dashboard/public-welfare.svg',
        ]);
    }
}