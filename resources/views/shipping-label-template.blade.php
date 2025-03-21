<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return Shipping Label</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .label-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .rma-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .address-box {
            width: 45%;
            border: 1px solid #000;
            padding: 10px;
        }
        .address-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
        }
        .barcode img {
            max-width: 80%;
        }
        .instructions {
            border-top: 1px solid #ccc;
            padding-top: 15px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="label-title">RETURN SHIPPING LABEL</div>
            <div class="rma-number">RMA: {{ $rma_number }}</div>
        </div>
        
        <div class="addresses">
            <div class="address-box">
                <div class="address-title">FROM:</div>
                <div>{{ $customer_name }}</div>
                <div>{{ $customer_address }}</div>
                <div>{{ $customer_city }}, {{ $customer_state }} {{ $customer_zip }}</div>
                <div>{{ $customer_country }}</div>
            </div>
            
            <div class="address-box">
                <div class="address-title">TO:</div>
                <div>{{ $warehouse_name }}</div>
                <div>{{ $warehouse_address }}</div>
                <div>{{ $warehouse_city }}, {{ $warehouse_state }} {{ $warehouse_zip }}</div>
                <div>{{ $warehouse_country }}</div>
            </div>
        </div>
        
        <div class="barcode">
            <div>*{{ $tracking_number }}*</div>
            <div>Tracking #: {{ $tracking_number }}</div>
        </div>
        
        <div class="instructions">
            <p><strong>Instructions:</strong></p>
            <ol>
                <li>Print this shipping label and attach it securely to your package.</li>
                <li>Drop off the package at any {{ $carrier }} location or schedule a pickup.</li>
                <li>Keep your receipt as proof of shipment.</li>
                <li>Your return will be processed once received at our warehouse.</li>
            </ol>
        </div>
        
        <div class="footer">
            <p>This shipping label is provided by Product Return Management System.</p>
            <p>For questions or assistance, please contact support@prms.example.com or call 1-800-RETURNS.</p>
        </div>
    </div>
</body>
</html>
