<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product As ProductModel;

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

    public static function getFreeProduct(Request $request)
    {
        $start = Carbon::parse($request->date_of_receipt ?? Carbon::now())->format('Y-m-d');
        $end = Carbon::parse($request->return_date ?? Carbon::now())->format('Y-m-d');

        $productQuery = ProductModel::select('products.id', 'products.quantity')
        ->where('products.section_id', $request->section_id)
        ->leftJoin('orders', 'orders.product_id', '=', 'products.id')
        ->leftJoin('invoices', 'invoices.id', '=', 'orders.invoice_id')
        ->whereNull('invoices.restored_at');

        // Apply filtering based on status
        $productQuery->where(function ($query) use ($start, $end, $request) {
            $query->whereBetween('date_of_receipt', [$start, $end])
                ->orWhereBetween('return_date', [$start, $end]);

            if ($request->status === 'pending') {
                $query->orWhere(function ($q) use ($start, $end) {
                    $q->where('date_of_receipt', '<', $start)
                    ->where('return_date', '>', $end);
                });
            } else {
                $query->orWhere(function ($q) use ($start, $end) {
                    $q->where('date_of_receipt', '>', $start)
                    ->orWhere('return_date', '>', $end);
                });
            }
        });

        // Get product IDs that are fully booked

        if ($request->status === 'inactive') {
        $excludedIds = ProductModel::select('products.id')
            ->where('products.section_id', $request->section_id)
            ->leftJoin('orders', 'orders.product_id', '=', 'products.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'orders.invoice_id')
            ->where('invoices.status', 'inactive')
            ->groupBy('products.id', 'products.quantity')
            ->havingRaw('products.quantity <= COUNT(orders.id)')
            ->pluck('products.id');
        } else {
            $excludedIds = $productQuery
                ->groupBy('products.id', 'products.quantity')
                ->havingRaw('products.quantity <= COUNT(orders.id)')
                ->pluck('products.id');
        }

        // Get available products
        $products = ProductModel::whereNotIn('id', $excludedIds)
            ->where('quantity', '>=', 1)
            ->where('section_id', $request->section_id)
            ->orderBy('products.size')
            ->get();

        return $products;
    }
}

