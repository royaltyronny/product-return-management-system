@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-light p-3 min-vh-100">
            <h5 class="mb-3">Return Management</h5>
            <div class="list-group mb-4">
                <a href="{{ route('admin.returns.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-list-ul me-2"></i> All Returns
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-clock me-2"></i> Pending
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'approved']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-check me-2"></i> Approved
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'in_transit']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shipping-fast me-2"></i> In Transit
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'received']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box-open me-2"></i> Received
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'inspected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-search me-2"></i> Inspected
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'refund_processed']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-money-bill-wave me-2"></i> Refunded
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'rejected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-times me-2"></i> Rejected
                </a>
            </div>
            
            <h5 class="mb-3">Analytics</h5>
            <div class="list-group">
                <a href="{{ route('admin.returns.reports') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Return Analytics & Reports</h3>
                <div>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#dateRangeCollapse">
                        <i class="fas fa-calendar-alt me-1"></i> Change Date Range
                    </button>
                </div>
            </div>
            
            <!-- Date Range Filter -->
            <div class="collapse mb-4" id="dateRangeCollapse">
                <div class="card card-body">
                    <form action="{{ route('admin.returns.reports') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Apply Date Range</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Returns</h6>
                                    <h3 class="mb-0">{{ array_sum(array_column($returnsByStatus->toArray(), 'count')) }}</h3>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <i class="fas fa-undo-alt text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Refund Amount</h6>
                                    <h3 class="mb-0">${{ number_format($totalRefundAmount, 2) }}</h3>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Avg. Processing Time</h6>
                                    <h3 class="mb-0">{{ round($avgProcessingTime->avg_days ?? 0, 1) }} days</h3>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Return Rate</h6>
                                    <h3 class="mb-0">{{ number_format(($totalReturnRate ?? 0) * 100, 1) }}%</h3>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <i class="fas fa-percentage text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Returns by Status</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="returnsByStatusChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Returns by Category</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="returnsByCategoryChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Timeline Chart -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Returns Timeline</h5>
                </div>
                <div class="card-body">
                    <canvas id="returnsTimelineChart" height="200"></canvas>
                </div>
            </div>
            
            <!-- Detailed Tables Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Returns by Status</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-end">Count</th>
                                            <th class="text-end">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCount = array_sum(array_column($returnsByStatus->toArray(), 'count')); @endphp
                                        @foreach($returnsByStatus as $status)
                                            <tr>
                                                <td>
                                                    <span class="badge 
                                                        @if($status->status == 'pending') bg-warning text-dark
                                                        @elseif($status->status == 'approved') bg-info
                                                        @elseif($status->status == 'rejected') bg-danger
                                                        @elseif($status->status == 'in_transit') bg-primary
                                                        @elseif($status->status == 'received') bg-secondary
                                                        @elseif($status->status == 'inspected') bg-dark
                                                        @elseif($status->status == 'refund_pending') bg-warning text-dark
                                                        @elseif($status->status == 'refund_processed') bg-success
                                                        @elseif($status->status == 'completed') bg-success
                                                        @elseif($status->status == 'cancelled') bg-danger
                                                        @endif me-2">
                                                    </span>
                                                    {{ ucwords(str_replace('_', ' ', $status->status)) }}
                                                </td>
                                                <td class="text-end">{{ $status->count }}</td>
                                                <td class="text-end">{{ number_format(($status->count / $totalCount) * 100, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">{{ $totalCount }}</th>
                                            <th class="text-end">100%</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Returns by Category</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Count</th>
                                            <th class="text-end">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCategoryCount = array_sum(array_column($returnsByCategory->toArray(), 'count')); @endphp
                                        @foreach($returnsByCategory as $category)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_', ' ', $category->return_category)) }}</td>
                                                <td class="text-end">{{ $category->count }}</td>
                                                <td class="text-end">{{ number_format(($category->count / $totalCategoryCount) * 100, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">{{ $totalCategoryCount }}</th>
                                            <th class="text-end">100%</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Returns by Status Chart
        const statusCtx = document.getElementById('returnsByStatusChart').getContext('2d');
        const statusLabels = {!! json_encode($returnsByStatus->pluck('status')->map(function($status) { 
            return ucwords(str_replace('_', ' ', $status)); 
        })) !!};
        const statusData = {!! json_encode($returnsByStatus->pluck('count')) !!};
        const statusColors = [
            '#ffc107', // pending - warning
            '#17a2b8', // approved - info
            '#dc3545', // rejected - danger
            '#007bff', // in_transit - primary
            '#6c757d', // received - secondary
            '#343a40', // inspected - dark
            '#28a745', // refund_processed - success
            '#28a745', // completed - success
            '#dc3545'  // cancelled - danger
        ];
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Returns by Category Chart
        const categoryCtx = document.getElementById('returnsByCategoryChart').getContext('2d');
        const categoryLabels = {!! json_encode($returnsByCategory->pluck('return_category')->map(function($category) { 
            return ucwords(str_replace('_', ' ', $category)); 
        })) !!};
        const categoryData = {!! json_encode($returnsByCategory->pluck('count')) !!};
        const categoryColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#5a5c69', '#858796', '#6610f2', '#6f42c1', '#e83e8c'
        ];
        
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Returns Timeline Chart
        const timelineCtx = document.getElementById('returnsTimelineChart').getContext('2d');
        const timelineDates = {!! json_encode($returnsByDay->pluck('date')) !!};
        const timelineCounts = {!! json_encode($returnsByDay->pluck('count')) !!};
        
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: timelineDates,
                datasets: [{
                    label: 'Returns',
                    data: timelineCounts,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
