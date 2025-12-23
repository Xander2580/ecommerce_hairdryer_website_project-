<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sewa API Integration</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="../assests/css/app.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="apibox">
        <img src="./api.png" alt="">
        <div class="apibox__detail">
            <h2 class="apibox__title">Purchase a Product</h2>
            <div clas="meta-box">
                <span class="meta-box__item">
                    Rs. <strong>1000</strong>
                </span>
            </div>
            <div class="text-box">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Recusandae, praesentium.</p>
            </div>
            <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" onsubmit="generateSignature()" target="_blank">
                <div class="field-group">
                    <label form="amount">Amount:</label>
                    <input type="text" id="amount" name="amount" value="1000" class="form" required=""> <br>
                </div>
                <div class="field-group">
                    <label for="tax_amount">Tax Amount:</label>
                    <input type="text" id="tax_amount" name="tax_amount" value="0" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="total_amount">Total Amount:</label>
                    <input type="text" id="total_amount" name="total_amount" value="1000" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="transaction_uuid">Transaction UUID:</label>
                    <input type="text" id="transaction_uuid" name="transaction_uuid" value="11-200-111sss1" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="product_code">Product Code:</label>
                    <input type="text" id="product_code" name="product_code" value="EPAYTEST" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="product_service_charge">Product Service Charge:</label>
                    <input type="text" id="product_service_charge" name="product_service_charge" value="0" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="product_delivery_charge">Product Delivery Charge:</label>
                    <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="success_url">Success URL:</label>
                    <input type="text" id="success_url" name="success_url" value="https://developer.esewa.com.np/success" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="failure_url">Failure URL:</label>
                    <input type="text" id="failure_url" name="failure_url" value="https://developer.esewa.com.np/failure" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="signed_field_names">signed Field Names:</label>
                    <input type="text" id="signed_field_names" name="signed_field_names" value="total_amount,transaction_uuid,product_code" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="signature">Signature:</label>
                    <input type="text" id="signature" name="signature" value="4Ov7pCI1zIOdwtV2BRMUNjz1upIlT/COTxfLhWvVurE=" class="form" required="">
                </div>
                <div class="field-group">
                    <label for="secret">Secret Key:</label>
                    <input type="text" id="secret" name="secret" value="8gBm/:&amp;EnhH.1/q" class="form" required="">
                </div>
                <button type="submit" class="button">Pay with eSewa</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/hmac-sha256.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js"></script>
    <script>
        // Function to auto-generate signature
        function generateSignature() {
            var currentTime = new Date();
            var formattedTime = currentTime.toISOString().slice(2, 10).replace(/-/g, '') + '-' + currentTime.getHours() +
                currentTime.getMinutes() + currentTime.getSeconds();

            document.getElementById("transaction_uuid").value = formattedTime;

            var total_amount = document.getElementById("total_amount").value;
            var transaction_uuid = document.getElementById("transaction_uuid").value;
            var product_code = document.getElementById("product_code").value;
            var secret = document.getElementById("secret").value;

            var hash = CryptoJS.HmacSHA256(
                `total_amount=${total_amount},transaction_uuid=${transaction_uuid},product_code=${product_code}`,
                `${secret}`);
                
            var hashInBase64 = CryptoJS.enc.Base64.stringify(hash);
            document.getElementById("signature").value = hashInBase64;
        }

        // Event listeners to call generateSignature() when inputs are changed
        document.getElementById("total_amount").addEventListener("input", generateSignature);
        document.getElementById("transaction_uuid").addEventListener("input", generateSignature);
        document.getElementById("product_code").addEventListener("input", generateSignature);
        document.getElementById("secret").addEventListener("input", generateSignature);
    </script>
</body>

</html>