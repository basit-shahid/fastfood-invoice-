<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice_number }}</title>
    
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 40px;
            font-size: 13px;
            line-height: 1.5;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            border-radius: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
        }
        .brand h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -1px;
            color: #ffb300;
            text-transform: uppercase;
        }
        .brand p { margin: 5px 0; color: #666; font-size: 12px; }
        
        .meta-info { text-align: right; }
        .meta-info h2 { margin: 0; font-size: 18px; font-weight: 900; color: #ddd; }
        .meta-info p { margin: 2px 0; font-weight: 700; color: #444; }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-item label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #999; margin-bottom: 4px; }
        .detail-item span { font-weight: 700; color: #1a1a1a; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th {
            text-align: left;
            padding: 12px 10px;
            background: #f8f9fa;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #eee;
        }
        table.items td {
            padding: 15px 10px;
            border-bottom: 1px solid #f1f1f1;
        }
        .item-name { font-weight: 700; display: block; }
        .item-description { font-size: 11px; color: #888; }

        .totals-container {
            display: flex;
            justify-content: flex-end;
        }
        .totals-table {
            width: 250px;
        }
        .totals-table tr td { padding: 8px 0; }
        .totals-table tr td:first-child { text-align: left; color: #888; font-weight: 500; }
        .totals-table tr td:last-child { text-align: right; font-weight: 700; }
        .grand-total { border-top: 2px solid #1a1a1a; padding-top: 15px !important; }
        .grand-total td { font-size: 18px; font-weight: 900 !important; color: #1a1a1a !important; }

        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #f1f1f1;
            padding-top: 20px;
            font-size: 11px;
            color: #aaa;
        }

        @media print {
            body { padding: 0; }
            .invoice-box { border: none; padding: 0; }
            .grand-total td { color: #000 !important; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="brand">
                <h1>{{ config('app.name', 'DOCTOR SHAWARMA') }}</h1>
                <p>Kahna Nau Lahore | +92 3299696192</p>
                <p>Deliciously Crafting Quality Since 2024</p>
            </div>
            <div class="meta-info">
                <h2 style="color:black">INVOICE</h2>
                <p>#{{ $order->invoice_number }}</p>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <label>Date & Time</label>
                <span>{{ $order->created_at->format('M d, Y | h:i A') }}</span>
            </div>
            <div class="detail-item" style="text-align: right;">
                <label>Cashier</label>
                <span>{{ $order->user->name ?? 'System' }}</span>
            </div>
            <div class="detail-item">
                <label>Payment Mode</label>
                <span>{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="detail-item" style="text-align: right;">
                <label>Order Status</label>
                <span>COMPLETED</span>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ $item->menuItem->name ?? 'Unknown Item' }}</span>
                        @if($item->instructions)
                        <span class="item-description">Note: {{ $item->instructions }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₱{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right; font-weight: 700;">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-container">
            <table class="totals-table">
                <tr>
                    <td>Subtotal</td>
                    <td>₱{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->tax > 0)
                <tr>
                    <td>Tax</td>
                    <td>₱{{ number_format($order->tax, 2) }}</td>
                </tr>
                @endif
                @if($order->discount > 0)
                <tr>
                    <td>Discount</td>
                    <td style="color: #dc3545;">-₱{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>TOTAL</td>
                    <td>₱{{ number_format($order->total, 2) }}</td>
                </tr>
                @if($order->payment_method === 'cash')
                <tr>
                    <td>Cash Received</td>
                    <td>₱{{ number_format($order->cash_received, 2) }}</td>
                </tr>
                <tr>
                    <td>Change</td>
                    <td>₱{{ number_format($order->change_amount, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ config('app.name', 'Doctor Shawarma') }}!</p>
            <p>Visit us again soon for more delicious treats.</p>
        </div>
    </div>

    @if(isset($print) && $print)
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    @endif
</body>
</html>
