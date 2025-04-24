<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('transactions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'note' => 'required|string',
        ], [
            'name.nullable' => 'اسم العامل يمكن أن يكون فارغًا.',
            'name.string' => 'اسم العامل يجب أن يكون نصًا.',
            'name.max' => 'اسم العامل يجب أن لا يتجاوز 255 حرفًا.',
            'note.required' => 'الملاحظة مطلوبة.',
            'note.string' => 'الملاحظة يجب أن تكون نصًا.',
        ]);


        Transaction::create($request->all());

        return redirect()->route('transactions.index')->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('transactions.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'note' => 'required|string',
        ], [
            'name.nullable' => 'اسم العامل يمكن أن يكون فارغًا.',
            'name.string' => 'اسم العامل يجب أن يكون نصًا.',
            'name.max' => 'اسم العامل يجب أن لا يتجاوز 255 حرفًا.',
            'note.required' => 'الملاحظة مطلوبة.',
            'note.string' => 'الملاحظة يجب أن تكون نصًا.',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());

        return redirect()->route('transactions.index')->with('success', 'تم تحديث الملاحظة بنجاح');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'تم حذف الملاحظة بنجاح');
    }

}
