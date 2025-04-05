<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

trait Product
{
    public static function getProductsBySectionId(string $section_id)
    {
        return DB::table('products')
        ->join('sections', 'products.section_id', '=', 'sections.id')
        ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
        ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
        ->where('products.section_id', $section_id)
        ->select(
            'products.id',
            'products.color',
            'products.size',
            'products.model',
            'products.quantity',
            'products.title',
            'products.created_at',
            // DB::raw('ANY_VALUE(invoices.status) as status'),
            DB::raw('COUNT(invoices.status) as invoice_count'),
            DB::raw('GROUP_CONCAT(invoices.id) as invoice_ids')
        )
        ->groupBy('products.id')
        ->orderBy('products.size', 'asc')
        ->paginate(PAGINATE);
    }

    public static function getFreeRentProduct($section_id , $start , $end)
    {
        return DB::table('products')
        ->join('sections', 'products.section_id', '=', 'sections.id')
        ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
        ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
        ->where('products.section_id', $section_id)
        ->where(function(Builder $query) use($start , $end)
        {
            $query->where(DB::Raw('COUNT(`invoices.id`)') , '>' , 0)
            ->OrWhere('invoices.date_of_receipt' , '<=', $start)
            ->OrWhere('invoice.return_date', '>=', $end);
        })
        ->select(
            'products.id',
            'products.color',
            'products.size',
            'products.model',
            'products.quantity',
            'products.title',
            'products.created_at'
        )
        ->groupBy('products.id')
        ->orderBy('products.size', 'asc')
        ->get();
    }
}

