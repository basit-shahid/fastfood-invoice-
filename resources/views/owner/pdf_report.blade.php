<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Report - {{ $monthName }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0;
            color: #64748b;
        }
        .summary-box {
            width: 48%;
            display: inline-block;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            box-sizing: border-box;
            vertical-align: top;
        }
        .summary-title {
            font-size: 14px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 10px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
        }
        .section-title {
            font-size: 18px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f8f9fa;
            color: #64748b;
            font-size: 14px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Dr. Shawarma - Business Report</h1>
        <p>Report Period: <strong>{{ $monthName }}</strong></p>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div>
        <div class="summary-box" style="margin-right: 2%;">
            <div class="summary-value">PKR {{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">Orders Processed ({{ $monthName }})</div>
            <div class="summary-value">{{ $totalOrders }} Orders</div>
        </div>
    </div>

    <div class="section-title">Highlights</div>
    <table>
        <tr>
            <th>Metric</th>
            <th>Detail</th>
        </tr>
        <tr>
            <td><strong>Top Performing Staff</strong></td>
            <td>
                @if($topStaff)
                    {{ $topStaff->name }} (PKR {{ number_format($topStaff->total_sales, 2) }} across {{ $topStaff->orders_count }} orders)
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Best Selling Item</strong></td>
            <td>
                @if($bestSeller && $bestSeller->menuItem)
                    {{ $bestSeller->menuItem->name }} ({{ $bestSeller->total_quantity }} units sold)
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>
    
    <div class="footer">
        Confidential Business Report for Dr. Shawarma Management.
    </div>

</body>
</html>
