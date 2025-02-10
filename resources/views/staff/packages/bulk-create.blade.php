@extends('staff.layout_staff.app_staff')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Multiple Packages</h5>
                    <a href="{{ route('staff.packages.create') }}" class="btn btn-outline-secondary btn-sm">Single Package Entry</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.packages.bulk-store') }}" method="POST" id="bulk-package-form">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="packages-table">
                                <thead>
                                    <tr>
                                        <th>Tracking Number</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Delivery Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="package-row">
                                        <td>
                                            <input type="text" name="packages[0][tracking_number]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="packages[0][name]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="tel" name="packages[0][phone_number]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="date" name="packages[0][delivery_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled>Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="add-row">Add Another Package</button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.packages.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add All Packages</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const packagesTable = document.getElementById('packages-table').getElementsByTagName('tbody')[0];
    const addRowBtn = document.getElementById('add-row');
    let rowCount = 1;

    // Function to update remove buttons state
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-row');
        const rowCount = document.querySelectorAll('.package-row').length;
        removeButtons.forEach(button => {
            button.disabled = rowCount <= 1;
        });
    }

    // Add new row
    addRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.className = 'package-row';
        newRow.innerHTML = `
            <td>
                <input type="text" name="packages[${rowCount}][tracking_number]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="packages[${rowCount}][name]" class="form-control" required>
            </td>
            <td>
                <input type="tel" name="packages[${rowCount}][phone_number]" class="form-control" required>
            </td>
            <td>
                <input type="date" name="packages[${rowCount}][delivery_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        `;
        packagesTable.appendChild(newRow);
        rowCount++;
        updateRemoveButtons();
    });

    // Remove row
    packagesTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateRemoveButtons();
        }
    });
});
</script>
@endpush
@endsection 