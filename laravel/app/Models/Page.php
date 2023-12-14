<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    const PAGE_ABOUT_US = 1;
    const PAGE_TERMS = 2;
    const PAGE_PRIVACY = 3;
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 0;


    protected $fillable = [
        'title',
        'description',
        'page_type',
        'created_at'
    ];

    //  public function getDescriptionAttribute($value)
    // {
    //     return html_entity_decode(strip_tags($value));
    // }

    public function getAllPages()
    {
        return self::orderBy('id', 'DESC')->get();

    }

    public function getPageType()
    {

        $list = [
            self::PAGE_ABOUT_US => "About Us",
            self::PAGE_TERMS => "Terms & Conditions",
            self::PAGE_PRIVACY => "Privacy & policy"

        ];

        return isset($list[$this->page_type]) ? $list[$this->page_type] : "Not Defined";
    }

    public function getStatus()
    {

        $list = [
            self::STATE_ACTIVE => "Active",
            self::STATE_INACTIVE => "Inactive"

        ];

        return isset($list[$this->state_id]) ? $list[$this->state_id] : "Not Defined";
    }

    public static function getPageTypeOptions()
    {

        $list = [
            self::PAGE_ABOUT_US => "About Us",
            self::PAGE_TERMS => "Terms & Conditions",
            self::PAGE_PRIVACY => "Privacy & policy"

        ];

        return $list;

    }

}
