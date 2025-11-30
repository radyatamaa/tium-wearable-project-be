<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BodyMeasurementExport implements FromArray, WithHeadings
{
    protected $data;
    protected $excludeFields;

    public function __construct(array $data, array $excludeFields = [])
    {
        $this->data = $data;
        $this->excludeFields = $excludeFields;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return array_map(function ($item) {
            return array_diff_key($item, array_flip($this->excludeFields));
        }, $this->data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            config('app.lang') != 'en' ? __('측정시간') : __('Measurement time'),
            config('app.lang') != 'en' ? __('심박수') : __('Heart rate'),
            config('app.lang') != 'en' ? __('체온') : __('Body temperature'),
            config('app.lang') != 'en' ? __('혈압') : __('Blood pressure'),
            config('app.lang') != 'en' ? __('산소') : __('Oxygen'),
            config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate'),
        ];
    }
}