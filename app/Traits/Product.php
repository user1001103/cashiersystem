<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product As ProductModel;

trait Product
{
    public static function getProductsBySectionId(string $section_id , $status = 0)
    {
        $query = DB::table('products')
        ->join('sections', 'products.section_id', '=', 'sections.id')
        ->leftJoin('borrows' , 'products.id' , '=', 'borrows.product_id')
        ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
        ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
        ->where('products.section_id', $section_id)
        // ->whereNull('invoices.restored_at')
        ->groupBy('products.id')
        ->orderBy('products.size', 'asc');

        if($status){
            $query->select(
                'products.id',
                'products.color',
                'products.type',
                'products.size',
                'products.model',
                'products.quantity',
                'products.title',
                'products.created_at',
                // DB::raw('ANY_VALUE(invoices.status) as status'),
                DB::raw("COUNT(DISTINCT orders.id) as orders_count"),
                DB::raw('GROUP_CONCAT(DISTINCT invoices.id) as invoice_ids'),
                DB::raw('sum(DISTINCT borrows.quantity) as sum_borrow')
            );
        }else{
            $query->select(
                'products.id',
                'products.color',
                'products.type',
                'products.size',
                'products.model',
                'products.quantity',
                'products.title',
                'products.created_at',
                DB::raw("COUNT(DISTINCT CASE WHEN invoices.restored_at IS NULL THEN orders.id END) as orders_count"),
                DB::raw('GROUP_CONCAT(DISTINCT invoices.id) as invoice_ids'),
                DB::raw('sum(DISTINCT borrows.quantity) as sum_borrow')
            );
        }
        return  $query->paginate(PAGINATE);
    }

    public static function getFreeProduct(Request $request)
    {
        $start = Carbon::parse($request->date_of_receipt ?? Carbon::now())->format('Y-m-d');
        $end = Carbon::parse($request->return_date ?? Carbon::now())->format('Y-m-d');

        $productQuery = ProductModel::select('products.id', 'products.quantity')
        ->where('products.section_id', $request->section_id)
        ->leftJoin('orders', 'orders.product_id', '=', 'products.id')
        ->leftJoin('invoices', 'invoices.id', '=', 'orders.invoice_id')
        ->leftJoin('borrows', 'products.id', '=', 'borrows.product_id');
        // ->whereNull('invoices.restored_at');

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
            ->leftJoin('borrows', 'products.id', '=', 'borrows.product_id')
            ->where('invoices.status', 'inactive')
            ->whereNull('invoices.restored_at')
            ->groupBy('products.id', 'products.quantity')
            ->havingRaw('products.quantity <= COUNT(orders.id) + SUM(borrows.quantity)')
            ->pluck('products.id');
        } else {
            $excludedIds = $productQuery
                ->whereNull('invoices.restored_at')
                ->groupBy('products.id', 'products.quantity')
                // ->havingRaw('products.quantity <= COUNT(orders.id)')
                ->havingRaw('products.quantity <= COUNT(orders.id) + SUM(borrows.quantity)')
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


    protected static function getCountOrdersProductBySectionId($section_id , $product_id , $status = 'sale' ,$start = null , $end = null)
    {

        $query =  DB::table('products')
        ->rightJoin('orders', 'products.id', '=', 'orders.product_id')
        ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
        ->where('section_id', $section_id)
        ->where('products.id', $product_id)
        ->whereNull('invoices.restored_at')
        ->select(DB::raw('COUNT(orders.id) AS count'));

        if($status !== 'sale')
        {
            $start = isset($start) ? Carbon::parse($start) : Carbon::now();
            $end = isset($end) ? Carbon::parse($end)->subDay() : Carbon::now();

            // Collect base dates
                $baseDates = collect();
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $baseDates->push($date->copy());
                }

                // Add +1 day and +2 days for each
                $extendedDates = $baseDates->flatMap(function ($date) {
                    return [
                        $date->copy(),
                        $date->copy()->addDay(),
                    ];
                });

                // Remove duplicates and format as 'Y-m-d'
                $dates = $extendedDates->unique()
                    ->sort()
                    ->values()
                    ->map(fn($d) => $d->format('Y-m-d'))
                    ->toArray();
                // dd($dates);
                // Result
            $query->where(function($q) use ($dates){
                $q->whereIn('date_of_receipt',$dates)
                ->OrWhereIn('return_date',$dates);
            });

        }
       return $query->first();
    }

    protected static function getCountProductBorrowByProductId($product_id)
    {
       return DB::table('products')
        ->leftJoin('borrows', 'products.id', '=', 'borrows.product_id')
        ->where('products.id', $product_id)
        ->select(DB::raw('Sum(borrows.quantity) AS count'))
        ->value('count');
    }
}

