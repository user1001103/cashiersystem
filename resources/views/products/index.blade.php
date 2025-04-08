@extends('layouts.master')
@section('title' , 'المنتجات')
@section('content')
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
              <h2 class="mb-2 page-title">قسم : {{ $section->name }} </h2>
              <div class="row my-4">
                <!-- Small table -->
                <div class="col-md-12">
                    <form class="form-inline">
                        <div class="form-row">
                          <div class="form-group col-auto">
                            <label for="search" class="sr-only">بحث</label>
                            <input type="text" class="form-control" id="search" value="" placeholder="بحث">
                          </div>
                          {{-- <div class="form-group col-auto ml-3">
                            <label class="my-1 mr-2 sr-only" for="status">الحالة</label>
                            <select name="status" class="custom-select my-1 mr-sm-2" id="status">
                                <option value="">جميع الحالات</option>
                                <option value="active">في المخزن</option>
                                <option value="pending">متاجر</option>
                                <option value="inactive">مباع</option>
                            </select>
                        </div> --}}
                        </div>
                      </form>
                    <div class="card shadow">
                        <div class="card-body">
                       <!-- Add a create button to trigger the creation of a new item -->
                        <a href="{{ route('products.create' , $section->id) }}" class="btn btn-success">تسجيل منتج جديد
                            <input type="hidden" name="section_name" value="{{ $section->name }}">
                        </a>

                        <!-- table -->
                      <table class="table datatables" id="productsTable">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>العدد الاساسي</th>
                            @if ($status)
                            <th>عدد الايجار</th>
                            @else
                            <th>عدد البيع</th>
                            @endif
                            <th>العدد اللي تم استلافه</th>
                            <th>اللون</th>
                            <th>المقاس</th>
                            <th>الموديل</th>
                            {{-- <th>الحاله</th> --}}
                            <th>الصوره</th>
                            <th>تاريخ الانشاء</th>
                            <th class="text-center">العمليه</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->quantity ?? "لا توجد"}}</td>
                            <td style="color: {{ $status == 1 ? 'green' : 'red' }}">
                                {{ $product->orders_count }}
                            </td>
                            <td style="color: blue; font-weight: bold; text-align: center;">{{$product?->type == 'borrow' ? 'مستلف': $product->sum_borrow ?? 0 }}</td>
                            <td>{{ $product->color ?? "لا توجد"}}</td>
                            <td>{{ $product->size  ?? "لا توجد"}}</td>
                            <td>{{ $product->model ?? "لا توجد" }}</td>
                            {{-- <td>
                            @switch($product->status)
                                @case('active')
                                    <button type="button" class="btn btn-sm btn-success">في المخزن</button>
                                    @break
                                @case('inactive')
                                    <button type="button" class="btn btn-sm btn-danger">مباع</button>
                                    @break
                                @default
                                    <button type="button" class="btn btn-sm btn-warning">متأجر</button>
                            @endswitch

                            </td> --}}
                            @if (isset($product->image))<td>
                                <a target="_blank" href="{{ Storage::url($product->image) }}"><img style="width: 50px;height: 50px;border: 1px solid #ccc;padding: 5px;margin: 0 auto;background-color: #f2f2f2;" src="{{ Storage::url($product->image) }}" alt=""></a>
                            </td>
                            @else
                            <td>لا توجد</td>
                            @endif
                            <td>{{\Carbon\Carbon::parse($product->created_at)->format('Y-m-d') }}</td>
                            <td class="text-center">
                                @if ($product->type === 'basic')
                                    <div class="btn-group">
                                        <a href="{{ route('products.edit' , $product->id) }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fe fe-16 fe-edit"></i>&nbsp;تعديل
                                            </button>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-product-id="{{ $product->id }}" disabled>
                                            <i class="fe fe-16 fe-delete"></i>&nbsp;حذف
                                        </button>

                                        <a href="#" class="openBorrowModal"
                                            data-toggle="modal"
                                            data-target="#borrowModal"
                                            data-product-id="{{ $product->id }}"
                                            data-status="{{ $status }}"
                                            >
                                            <button type="button" class="btn btn-sm btn-outline-primary">
                                                <i class="fe fe-16 fe-arrow-right"></i>&nbsp;استلاف
                                            </button>
                                            </a>


                                    </div>
                                @else
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-product-id="{{ $product->id }}" disabled>
                                    <i class="fe fe-16 fe-delete"></i>&nbsp;حذف
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                    {{ $products->links() }}
                      <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        هل انت متاكد من حذف المنتج ؟
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                        <form id="delete-form" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" id="delete-btn">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

<!-- Borrow Button -->
{{-- <a href="#" data-toggle="modal" data-target="#borrowModal" data-product-id="{{ $product->id }}">
    <button type="button" class="btn btn-sm btn-outline-primary">
        <i class="fe fe-16 fe-arrow-right"></i>&nbsp;استلاف
    </button>
</a> --}}

<div class="modal fade" id="borrowModal" tabindex="-1" role="dialog" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="borrowModalLabel">استلاف المنتج</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('products.saveBorrow'  , ['status' => $status ] ) }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id">
                    {{-- <input type="hidden" name="status" id="status"> --}}
                    <div class="form-group">
                        <label for="quantity">العدد</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="type">النوع</label>
                        <select name="type" id="type" class="form-control">
                            <option value="personal">شخصي</option>
                            <option value="impersonal">غير شخصي</option>
                        </select>
                    </div>

                    <input type="hidden" name="from" value="{{ $section->id }}">
                    <div id="impersonalFields" style="display: none;">
                        <div class="form-group">
                            <label for="parent_section">القسم الرئيسي</label>
                            <select name="parent_section" id="parent_section" class="form-control" required>
                                <option value="">اختر قسم</option>
                                @foreach($parent_section as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="child_section">القسم الفرعي</label>
                            <select name="child_section" id="child_section" class="form-control">
                                <option value="">اختر قسم فرعي</option>
                                <!-- Child sections will be populated based on parent section -->
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>


                    </div>
                  </div>
                </div> <!-- simple table -->
              </div> <!-- end section -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
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
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>


<script>
    // Toggle impersonal fields visibility
    document.getElementById('type').addEventListener('change', function () {
        var impersonalFields = document.getElementById('impersonalFields');
        impersonalFields.style.display = this.value === 'impersonal' ? 'block' : 'none';
    });

    // Fetch child sections based on parent section
    document.getElementById('parent_section').addEventListener('change', function () {
        var parentId = this.value;
        var childSectionSelect = document.getElementById('child_section');

        if (parentId) {
            fetch(`/get-section?parent_id=${parentId}`)
                .then(response => response.json())
                .then(data => {
                    childSectionSelect.innerHTML = '<option value="">اختر قسم فرعي</option>';
                    data.data.forEach(function (child) {
                        var option = document.createElement('option');
                        option.value = child.id;
                        option.textContent = child.name;
                        childSectionSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching child sections:', error);
                });
        } else {
            childSectionSelect.innerHTML = '<option value="">اختر قسم فرعي</option>';
        }
    });

    // Set product ID in hidden input when modal opens & disable button
    // document.querySelectorAll('.borrow-btn').forEach(function (btn) {
    //     btn.addEventListener('click', function () {
    //         const productId = this.getAttribute('data-product-id');
    //         const status = this.getAttribute('data-status');
    //         const invoiceCount = this.getAttribute('data-invoice-count');

    //         document.getElementById('product_id').value = productId;
    //         document.getElementById('status').value = status;

    //         const innerButton = this.querySelector('button');
    //         if (innerButton) {
    //             innerButton.disabled = true;
    //             innerButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جارٍ المعالجة...';
    //         }
    //     });
    // });

    // $('#borrowModal').on('hidden.bs.modal', function () {
    //     document.querySelectorAll('.borrow-btn button').forEach(btn => {
    //         btn.disabled = false;
    //         btn.innerHTML = '<i class="fe fe-16 fe-arrow-right"></i>&nbsp;استلاف';
    //     });
    // });

    // Optional: Re-enable button when modal is closed (if needed)
    $('#borrowModal').on('hidden.bs.modal', function () {
        document.querySelectorAll('.borrow-btn button').forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fe fe-16 fe-arrow-right"></i>&nbsp;استلاف';
        });
    });

    document.querySelectorAll('.openBorrowModal').forEach(function (el) {
    el.addEventListener('click', function () {
        // Dynamically get the product ID and status when the button is clicked
        const productId = this.dataset.productId;
        const status = this.dataset.status;

        // Set the values in hidden inputs within the modal form
        document.getElementById('product_id').value = productId;
        document.getElementById('status').value = status;

        // console.log('Product ID:', productId);  // Debugging log
        // console.log('Status:', status);  // Debugging log
    });
});


</script>

<script>
    $(document).ready(function() {
        // Enable all buttons with class "delete-btn"
        $('.delete-btn').prop('disabled', false);

        // Enable the button when the modal is shown
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var productId = button.data('product-id');
            var form = $('#delete-form');
            form.attr('action', '{{ route("products.destroy", ":product") }}'.replace(':product', productId));
            $('#delete-form button[type="submit"]').removeClass('disabled').prop('disabled', false);
        });
    });
</script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');

    $(document).ready(function(){
      let debounceTimer;

      function fetchProducts(page = 1) {
          let search = $('#search').val();
          let status = $('#status').val();
          $.ajax({
              url: '{{ route('product.search') }}',
              method: 'GET',
              data: {
                sectionId: @json($section->id), // Ensure proper escaping
                search: search,
                status: status,
                page: page
            },
              success: function(data) {
                console.log(data);

                  $('#productsTable tbody').html(data.tableRows);
                  $('.pagination').html(data.pagination);
              },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error:', error);
            }

          });
      }

      function debounce(func, delay) {
          return function(...args) {
              clearTimeout(debounceTimer);
              debounceTimer = setTimeout(() => func.apply(this, args), delay);
          };
      }

      const debouncedFetchProducts = debounce(function() {
          fetchProducts();
      }, 500); // 500ms delay

      $('#search, #status').on('keyup change', debouncedFetchProducts);

      $(document).on('click', '.pagination a', function(event) {
          event.preventDefault();
          let url = $(this).attr('href');
          let page = new URL(url).searchParams.get('page');
          fetchProducts(page);
      });
  });

</script>

@endsection
