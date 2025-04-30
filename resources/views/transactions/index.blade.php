@extends('layouts.master')
@section('title', 'قائمة الملاحظات')
@section('content')
<style>
.note-cell {
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    position: relative;
    cursor: pointer;
}

.note-cell:hover {
    overflow: visible; /* Ensure content is visible when hovering */
}

.note-cell:hover::after {
    content: attr(data-note);
    position: absolute;
    top: 0;
    left: 30%;
    background-color: #fff;
    color: #333;
    padding: 5px;
    border: 1px solid #ccc;
    white-space: normal;
    width: 300px; /* Adjust the width as needed */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 999;
    word-wrap: break-word; /* Make sure long words break properly */
}


</style>
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h4 class="alert-heading text-center">Error!</h4>
    <hr>
    <ul class="list-unstyled mb-0">
        @foreach ($errors->all() as $error)
            <li class="text-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="mb-2 page-title">قائمة الملاحظات</h2>
            <div class="row my-4">
                <div class="col-md-12 text-left mb-2">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary fe fe-24 fe-plus">
                        <span class="ml-1 item-text">إضافة ملاحظة جديدة</span>
                    </a>
                </div>
                <!-- Small table -->
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <!-- table -->
                            <table class="table datatables" id="dataTable-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>الملاحظة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $transaction->name }}</td>
                                        {{-- <td>{{ $transaction->note }}</td> --}}
                                        <td class="note-cell" data-note="{{ $transaction->note }}">
                                            {{ $transaction->note }}
                                        </td>


                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                {{-- <a href="{{ route('transactions.show', $transaction->id) }}">
                                                    <button type="button" class="btn btn-sm btn-outline-info">
                                                        <i class="fe fe-16 fe-eye"></i>&nbsp;عرض
                                                    </button>
                                                </a> --}}
                                                <a href="{{ route('transactions.edit', $transaction->id) }}">
                                                    <button type="button" class="btn btn-sm btn-outline-warning">
                                                        <i class="fe fe-16 fe-edit"></i>&nbsp;تعديل
                                                    </button>
                                                </a>
                                                <!-- Trigger the modal on delete button click -->
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteModal" data-transaction-id="{{ $transaction->id }}">
                                                    <i class="fe fe-16 fe-delete"></i>&nbsp;حذف
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- simple table -->
            </div> <!-- end section -->
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalTitle">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">هل أنت متأكد من أنك تريد حذف هذه الملاحظة؟</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="delete-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="/js/jquery.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/moment.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/simplebar.min.js"></script>
<script src='/js/daterangepicker.js'></script>
<script src='/js/jquery.stickOnScroll.js'></script>
<script src="/js/tinycolor-min.js"></script>
<script src="/js/config.js"></script>
<script src="/js/apps.js"></script>
<script src='/js/jquery.dataTables.min.js'></script>
<script src='/js/dataTables.bootstrap4.min.js'></script>
<script>
    $(document).ready(function() {
        // When the delete button is clicked, populate the modal form with the correct transaction ID
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var transactionId = button.data('transaction-id'); // Extract transaction ID
            var form = $('#delete-form');
            var actionUrl = '{{ route("transactions.destroy", ":id") }}'.replace(':id', transactionId); // Set the action URL dynamically
            form.attr('action', actionUrl);
        });
    });
</script>
@endsection
