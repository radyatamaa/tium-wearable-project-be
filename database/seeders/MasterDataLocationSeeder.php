<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert cities
        $cities = [
            ['city_name' => '서울', 'city_name_en' => 'Seoul'],
            ['city_name' => '부산', 'city_name_en' => 'Busan'],
            ['city_name' => '대구', 'city_name_en' => 'Daegu'],
            ['city_name' => '인천', 'city_name_en' => 'Incheon'],
            ['city_name' => '광주', 'city_name_en' => 'Gwangju'],
            ['city_name' => '대전', 'city_name_en' => 'Daejeon'],
            ['city_name' => '울산', 'city_name_en' => 'Ulsan'],
            ['city_name' => '세종', 'city_name_en' => 'Sejong'],
            ['city_name' => '경기', 'city_name_en' => 'Gyeonggi'],
            ['city_name' => '강원', 'city_name_en' => 'Gangwon'],
            ['city_name' => '충북', 'city_name_en' => 'Chungbuk'],
            ['city_name' => '충남', 'city_name_en' => 'Chungnam'],
            ['city_name' => '전북', 'city_name_en' => 'Jeonbuk'],
            ['city_name' => '전남', 'city_name_en' => 'Jeonnam'],
            ['city_name' => '경북', 'city_name_en' => 'Gyeongbuk'],
            ['city_name' => '경남', 'city_name_en' => 'Gyeongnam'],
            ['city_name' => '제주', 'city_name_en' => 'Jeju']
        ];

        DB::table('master_data_cities')->insert($cities);

        // Retrieve city IDs
        $cityIds = DB::table('master_data_cities')->pluck('id', 'city_name');

        // Insert districts with postal codes
        $districts = [
            // Seoul
            ['district_name' => '강남구', 'district_name_en' => 'Gangnam-gu', 'postal_code' => '06000', 'city_id' => $cityIds['서울']],
            ['district_name' => '강동구', 'district_name_en' => 'Gangdong-gu', 'postal_code' => '05300', 'city_id' => $cityIds['서울']],
            ['district_name' => '강북구', 'district_name_en' => 'Gangbuk-gu', 'postal_code' => '01000', 'city_id' => $cityIds['서울']],
            ['district_name' => '강서구', 'district_name_en' => 'Gangseo-gu', 'postal_code' => '07500', 'city_id' => $cityIds['서울']],
            ['district_name' => '관악구', 'district_name_en' => 'Gwanak-gu', 'postal_code' => '08700', 'city_id' => $cityIds['서울']],
            ['district_name' => '광진구', 'district_name_en' => 'Gwangjin-gu', 'postal_code' => '05000', 'city_id' => $cityIds['서울']],
            ['district_name' => '구로구', 'district_name_en' => 'Guro-gu', 'postal_code' => '08300', 'city_id' => $cityIds['서울']],
            ['district_name' => '금천구', 'district_name_en' => 'Geumcheon-gu', 'postal_code' => '08500', 'city_id' => $cityIds['서울']],
            ['district_name' => '노원구', 'district_name_en' => 'Nowon-gu', 'postal_code' => '01600', 'city_id' => $cityIds['서울']],
            ['district_name' => '도봉구', 'district_name_en' => 'Dobong-gu', 'postal_code' => '01300', 'city_id' => $cityIds['서울']],
            ['district_name' => '동대문구', 'district_name_en' => 'Dongdaemun-gu', 'postal_code' => '02500', 'city_id' => $cityIds['서울']],
            ['district_name' => '동작구', 'district_name_en' => 'Dongjak-gu', 'postal_code' => '07000', 'city_id' => $cityIds['서울']],
            ['district_name' => '마포구', 'district_name_en' => 'Mapo-gu', 'postal_code' => '04100', 'city_id' => $cityIds['서울']],
            ['district_name' => '서대문구', 'district_name_en' => 'Seodaemun-gu', 'postal_code' => '03700', 'city_id' => $cityIds['서울']],
            ['district_name' => '서초구', 'district_name_en' => 'Seocho-gu', 'postal_code' => '06500', 'city_id' => $cityIds['서울']],
            ['district_name' => '성동구', 'district_name_en' => 'Seongdong-gu', 'postal_code' => '04700', 'city_id' => $cityIds['서울']],
            ['district_name' => '성북구', 'district_name_en' => 'Seongbuk-gu', 'postal_code' => '02800', 'city_id' => $cityIds['서울']],
            ['district_name' => '송파구', 'district_name_en' => 'Songpa-gu', 'postal_code' => '05500', 'city_id' => $cityIds['서울']],
            ['district_name' => '양천구', 'district_name_en' => 'Yangcheon-gu', 'postal_code' => '08000', 'city_id' => $cityIds['서울']],
            ['district_name' => '영등포구', 'district_name_en' => 'Yeongdeungpo-gu', 'postal_code' => '07200', 'city_id' => $cityIds['서울']],
            ['district_name' => '용산구', 'district_name_en' => 'Yongsan-gu', 'postal_code' => '04300', 'city_id' => $cityIds['서울']],
            ['district_name' => '은평구', 'district_name_en' => 'Eunpyeong-gu', 'postal_code' => '03300', 'city_id' => $cityIds['서울']],
            ['district_name' => '종로구', 'district_name_en' => 'Jongno-gu', 'postal_code' => '03100', 'city_id' => $cityIds['서울']],
            ['district_name' => '중구', 'district_name_en' => 'Jung-gu', 'postal_code' => '04500', 'city_id' => $cityIds['서울']],
            ['district_name' => '중랑구', 'district_name_en' => 'Jungnang-gu', 'postal_code' => '02100', 'city_id' => $cityIds['서울']],

            // Busan
            ['district_name' => '강서구', 'district_name_en' => 'Gangseo-gu', 'postal_code' => '46700', 'city_id' => $cityIds['부산']],
            ['district_name' => '금정구', 'district_name_en' => 'Geumjeong-gu', 'postal_code' => '46200', 'city_id' => $cityIds['부산']],
            ['district_name' => '기장군', 'district_name_en' => 'Gijang-gun', 'postal_code' => '46000', 'city_id' => $cityIds['부산']],
            ['district_name' => '남구', 'district_name_en' => 'Nam-gu', 'postal_code' => '48400', 'city_id' => $cityIds['부산']],
            ['district_name' => '동구', 'district_name_en' => 'Dong-gu', 'postal_code' => '48700', 'city_id' => $cityIds['부산']],
            ['district_name' => '동래구', 'district_name_en' => 'Dongnae-gu', 'postal_code' => '47800', 'city_id' => $cityIds['부산']],
            ['district_name' => '부산진구', 'district_name_en' => 'Busanjin-gu', 'postal_code' => '47200', 'city_id' => $cityIds['부산']],
            ['district_name' => '북구', 'district_name_en' => 'Buk-gu', 'postal_code' => '46500', 'city_id' => $cityIds['부산']],
            ['district_name' => '사상구', 'district_name_en' => 'Sasang-gu', 'postal_code' => '47000', 'city_id' => $cityIds['부산']],
            ['district_name' => '사하구', 'district_name_en' => 'Saha-gu', 'postal_code' => '49300', 'city_id' => $cityIds['부산']],
            ['district_name' => '서구', 'district_name_en' => 'Seo-gu', 'postal_code' => '49200', 'city_id' => $cityIds['부산']],
            ['district_name' => '수영구', 'district_name_en' => 'Suyeong-gu', 'postal_code' => '48200', 'city_id' => $cityIds['부산']],
            ['district_name' => '연제구', 'district_name_en' => 'Yeonje-gu', 'postal_code' => '47500', 'city_id' => $cityIds['부산']],
            ['district_name' => '영도구', 'district_name_en' => 'Yeongdo-gu', 'postal_code' => '49000', 'city_id' => $cityIds['부산']],
            ['district_name' => '중구', 'district_name_en' => 'Jung-gu', 'postal_code' => '48900', 'city_id' => $cityIds['부산']],
            ['district_name' => '해운대구', 'district_name_en' => 'Haeundae-gu', 'postal_code' => '48000', 'city_id' => $cityIds['부산']],

            // Add more districts for other cities as needed...

            // Daegu
            ['district_name' => '중구', 'district_name_en' => 'Jung-gu', 'postal_code' => '41900', 'city_id' => $cityIds['대구']],
            ['district_name' => '동구', 'district_name_en' => 'Dong-gu', 'postal_code' => '41100', 'city_id' => $cityIds['대구']],
            ['district_name' => '서구', 'district_name_en' => 'Seo-gu', 'postal_code' => '41700', 'city_id' => $cityIds['대구']],
            ['district_name' => '남구', 'district_name_en' => 'Nam-gu', 'postal_code' => '42400', 'city_id' => $cityIds['대구']],
            ['district_name' => '북구', 'district_name_en' => 'Buk-gu', 'postal_code' => '41400', 'city_id' => $cityIds['대구']],
            ['district_name' => '수성구', 'district_name_en' => 'Suseong-gu', 'postal_code' => '42100', 'city_id' => $cityIds['대구']],
            ['district_name' => '달서구', 'district_name_en' => 'Dalseo-gu', 'postal_code' => '42600', 'city_id' => $cityIds['대구']],
            ['district_name' => '달성군', 'district_name_en' => 'Dalseong-gun', 'postal_code' => '42900', 'city_id' => $cityIds['대구']],

            // Incheon
            ['district_name' => '중구', 'district_name_en' => 'Jung-gu', 'postal_code' => '22300', 'city_id' => $cityIds['인천']],
            ['district_name' => '동구', 'district_name_en' => 'Dong-gu', 'postal_code' => '22500', 'city_id' => $cityIds['인천']],
            ['district_name' => '미추홀구', 'district_name_en' => 'Michuhol-gu', 'postal_code' => '22100', 'city_id' => $cityIds['인천']],
            ['district_name' => '연수구', 'district_name_en' => 'Yeonsu-gu', 'postal_code' => '22000', 'city_id' => $cityIds['인천']],
            ['district_name' => '남동구', 'district_name_en' => 'Namdong-gu', 'postal_code' => '21500', 'city_id' => $cityIds['인천']],
            ['district_name' => '부평구', 'district_name_en' => 'Bupyeong-gu', 'postal_code' => '21300', 'city_id' => $cityIds['인천']],
            ['district_name' => '계양구', 'district_name_en' => 'Gyeyang-gu', 'postal_code' => '21000', 'city_id' => $cityIds['인천']],
            ['district_name' => '서구', 'district_name_en' => 'Seo-gu', 'postal_code' => '22600', 'city_id' => $cityIds['인천']],
            ['district_name' => '강화군', 'district_name_en' => 'Ganghwa-gun', 'postal_code' => '23000', 'city_id' => $cityIds['인천']],
            ['district_name' => '옹진군', 'district_name_en' => 'Ongjin-gun', 'postal_code' => '23100', 'city_id' => $cityIds['인천']],

            // Add more districts for other cities as needed...

        ];

        DB::table('master_data_districts')->insert($districts);
    }
}