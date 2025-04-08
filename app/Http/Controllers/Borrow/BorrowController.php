<?php

namespace App\Http\Controllers\Borrow;


use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowRequest;
use App\Traits\Product As ProductTrait;

class BorrowController extends Controller
{
    use ProductTrait;

    public function index()
    {
        $borrows = DB::table('borrows')
        ->leftJoin('products' , 'borrows.product_id' , 'products.id')
        ->where('borrows.type', '=' , 'personal')
        ->select('borrows.id','products.color' , 'products.model' , 'products.size' , 'borrows.quantity')
        ->paginate(PAGINATE);
        // return $borrows;
        return view('borrows.index' , ['borrows' => $borrows]);
    }

    public function saveBorrow(BorrowRequest $request)
    {
        if($request->type != 'personal')
        {
            $childrenIds = Section::where('section_id' , $request->parent_section)->pluck('id');
            if(!$childrenIds->isEmpty() && is_null($request->child_section))
            {
                return back()->with("error","لازم تختار القسم الفرعي");
            }
        }

        if($request->status == 0)
        {
            return $this->borrowToRent($request);
        }
        return $this->borrowToSale($request);
    }

    public function borrowToRent($request)
    {
        $product_count_free = static::getCountOrdersProductBySectionId($request->from , $request->product_id)->count;
        $product_quantity = Product::find($request->product_id)->quantity;
        $freeQuantity = $product_quantity - ($product_count_free + $this->getBorrowQuantity($request->product_id));
        if($freeQuantity < $request->quantity)
        {
            return back()->with("error","قلل العدد شويه");
        }

        $this->insert($request);

        return back()->with('success' , 'تم الاستلاف بنجاح');
    }
    public function borrowToSale($request)
    {
        $product_count_free = static::getCountOrdersProductBySectionId($request->from , $request->product_id ,'rent')->count;
        $product_quantity = Product::find($request->product_id)->quantity;
        $freeQuantity = $product_quantity - ($product_count_free + $this->getBorrowQuantity($request->product_id));
        if($freeQuantity < $request->quantity)
        {
            return back()->with("error","قلل العدد شويه");
        }

        return  $this->insert($request);
    }

    public static function getBorrowQuantity($product_id)
    {
        return DB::table('borrows')->where('product_id','=',$product_id)->sum('quantity');
    }

    public static function insert($request)
    {

        try{
            DB::beginTransaction();
            $sectionId = $request->child_section ?? $request->parent_section;
            if($sectionId){
                $product_borrow = DB::table('borrows')
                ->where('product_id' , $request->product_id)
                ->where('to' , $sectionId);

                $product = DB::table('products')->where('id' , $product_borrow->value('new_product_id'));
                if($product_borrow->count() > 0 && $product->count() > 0)
                {
                    $product->increment('quantity', $request->quantity);
                    $product_borrow->increment('quantity', $request->quantity);
                }else{
                    $product = DB::table('products')->where('id' , $request->product_id)->first();
                    $id = DB::table('products')->insertGetId([
                        'section_id' => $sectionId,
                        'color' =>  $product->color,
                        'model' => $product->model,
                        'size' => $product->size,
                        'image'  =>$product->image,
                        'quantity' => $request->quantity,
                        'title' => $product->title,
                        'type' => 'borrow'
                    ]);
                    DB::table('borrows')->insert([
                        "quantity" => $request->quantity,
                        "type" => $request->type,
                        "from" => $request->from,
                        "to" => $request->child_section	 ?? $request->parent_section,
                        'new_product_id' => $id,
                        'product_id' => $request->product_id,
                        'borrowed_at' => now()
                    ]);
                }

            }else{
                $borrows = DB::table('borrows')
                ->where('product_id' , $request->product_id)
                ->where('type' , '=', 'personal')
                ->where('from' , $request->from);
                if($borrows->count() > 0)
                {
                    $borrows->increment('quantity' , $request->quantity);
                }else{

                    DB::table('borrows')->insert([
                        "quantity" => $request->quantity,
                        "type" => $request->type,
                        "from" => $request->from,
                        'product_id' => $request->product_id,
                        'borrowed_at' => now()
                    ]);
                }
            }
            DB::commit();
            return back()->with('success' , 'تم الاستلاف بنجاح');
        }catch(\Exception){
            DB::rollback();
            return back()->with('error' , 'error');
        }
    }

    public function destroy(Request $request ,$id)
    {
        DB::table('borrows')->delete($id);
        return back()->with('success' , 'تم حذف الاستلاف بنجاح');
    }
}
