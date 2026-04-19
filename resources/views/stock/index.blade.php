@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-900 border-bottom border-3 border-warning pb-2">Stock Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
            <i class="fas fa-plus me-2"></i>Add New Item
        </button>
    </div>

    <div class="card shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Item Name</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Last Updated By</th>
                        <th>Last Updated At</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $stock->item_name }}</td>
                            <td>
                                <form action="{{ route('stock.update', $stock) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $stock->quantity }}" step="0.01" class="form-control form-control-sm" style="width: 100px;">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Update Quantity">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td><span class="badge bg-secondary">{{ $stock->unit }}</span></td>
                            <td>{{ $stock->updater->name ?? 'N/A' }}</td>
                            <td>{{ $stock->updated_at->format('M d, h:i A') }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('stock.destroy', $stock) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open d-block mb-3 fs-1"></i>
                                No stock items found. Add your first item above!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">Add Stock Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stock.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" name="item_name" class="form-control rounded-3" placeholder="e.g. Shawarma Bread" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Initial Quantity</label>
                            <input type="number" name="quantity" step="0.01" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unit</label>
                            <select name="unit" class="form-select rounded-3">
                                <option value="pcs">Pcs</option>
                                <option value="kg">Kg</option>
                                <option value="ltr">Ltr</option>
                                <option value="pkts">Pkts</option>
                                <option value="dozen">Dozen</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
