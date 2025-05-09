<?php

namespace App\Http\Controllers\Sales;

use Carbon\Carbon;
use App\Models\OrderPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalesWeeklyController extends Controller
{
    protected Carbon $now;
    public function __construct()
    {
        $this->now = Carbon::now('Africa/Cairo');
    }
    public function deposits()
    {
        $deposits = OrderPricing::GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '>', $this->now)->paginate(PAGINATE);
        return view('salesWeekly.deposits' , ['deposits' => $deposits]);
    }
    public function index()
    {
        $sales = OrderPricing::GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->paginate(PAGINATE);
        // $sales = OrderPricing::GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->paginate(PAGINATE);
        return view('salesWeekly.index' , ['sales' => $sales]);
    }
    public function pending()
    {
        $sales = OrderPricing::GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->having('invoices.status' , 'pending')->paginate(PAGINATE);
        // $sales = OrderPricing::GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->having('invoices.status' , 'pending')->paginate(PAGINATE);
        return view('salesWeekly.pending' , ['sales' => $sales]);
    }
    public function inactive()
    {
        $sales = OrderPricing::GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        // $sales = OrderPricing::GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        return view('salesWeekly.inactive' , ['sales' => $sales]);
    }

    public function archive(string $ids)
    {
        DB::table('order_pricing')
        ->whereIn('id', explode(',', $ids))
        ->update(['deleted_at' => now()]);
        return back();
    }
    public function archiveAll()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->paginate(PAGINATE);
        // $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->paginate(PAGINATE);
        return view('salesWeekly.index' , ['sales' => $sales]);
    }
    public function archivePending()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->having('invoices.status' , 'pending')->paginate(PAGINATE);
        // $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->having('invoices.status' , 'pending')->paginate(PAGINATE);
        return view('salesWeekly.pending' , ['sales' => $sales]);
    }
    public function archiveInactive()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now)->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        // $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->where('orders.price' , '=',DB::Raw('orders.payment'))->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        return view('salesWeekly.inactive' , ['sales' => $sales]);
    }


    public function showOrders($start_week , $end_week , $status)
    {

        $orders = OrderPricing::GetWeeklyWhere($start_week , $end_week , $status);

        if(request('deposit')){
            $orders->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '>', $this->now);
            // $orders->where('orders.price' , '<>',DB::Raw('orders.payment'));
        }else{
            $orders->whereDate(DB::raw('COALESCE(invoices.date_of_receipt, invoices.created_at)') , '<=', $this->now);
            // $orders->where('orders.price' , '=',DB::Raw('orders.payment'));
        }

        if(request('archive') == "true"){
            $orders->onlyTrashed();
        }
        $orders = $orders->paginate(PAGINATE);

        return view('salesWeekly.show' , ['orders' => $orders]);
    }
}
