<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Repayment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .success-badge {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 25px 0;
        }

        .detail-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .amount-highlight {
            background: #e8f5e8;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
        }

        .amount-highlight .currency {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
        }

        .info-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✅</div>
            <h1>Payment Received Successfully</h1>
            <p>Your loan repayment has been processed</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-badge">Payment Confirmed</div>

            <p>Dear <strong><?php echo e($client->name); ?></strong>,</p>

            <p>We are pleased to confirm that your loan repayment has been successfully received and processed. Here are
                the details of your transaction:</p>

            <!-- Amount Highlight -->
            <div class="amount-highlight">
                <div class="detail-label">Amount Paid</div>
                <div class="currency">৳<?php echo e(number_format($repayment->amount_paid, 2)); ?></div>
            </div>

            <!-- Payment Details Grid -->
            <div class="details-grid">
                <div class="detail-box">
                    <div class="detail-label">Transaction Reference</div>
                    <div class="detail-value"><?php echo e($repayment->reference_no); ?></div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Payment Date</div>
                    <div class="detail-value">
                        <?php echo e(\Carbon\Carbon::parse($repayment->payment_date)->format('d M Y')); ?></div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value"><?php echo e(ucfirst(strtolower($repayment->payment_mode))); ?></div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Processing Time</div>
                    <div class="detail-value"><?php echo e($repayment->created_at->format('d M Y H:i A')); ?></div>
                </div>
            </div>

            <!-- Loan Information -->
            <div class="info-section">
                <h3 style="margin-top: 0; color: #1976d2;">📋 Loan Information</h3>
                <div class="details-grid">
                    <div>
                        <div class="detail-label">Loan Amount</div>
                        <div class="detail-value">৳<?php echo e(number_format($loan->loan_amount, 2)); ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Interest Rate</div>
                        <div class="detail-value"><?php echo e($loan->interest_rate); ?>% per annum</div>
                    </div>
                    <div>
                        <div class="detail-label">Loan Status</div>
                        <div class="detail-value"><?php echo e(ucfirst(strtolower($loan->status))); ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Branch</div>
                        <div class="detail-value"><?php echo e($branch->name); ?></div>
                    </div>
                </div>
            </div>

            <!-- Updated Loan Balance -->
            <?php
            $totalRepaid = $loan->repayments->sum('amount_paid');
            $remainingBalance = $loan->loan_amount - $totalRepaid;
            $interestAmount = ($loan->loan_amount * $loan->interest_rate * $loan->tenure_months) / (12 * 100);
            $totalWithInterest = $loan->loan_amount + $interestAmount;
            $remainingWithInterest = $totalWithInterest - $totalRepaid;
            ?>

            <div class="info-section">
                <h3 style="margin-top: 0; color: #1976d2;">💰 Updated Loan Balance</h3>
                <div class="details-grid">
                    <div>
                        <div class="detail-label">Total Repaid</div>
                        <div class="detail-value" style="color: #28a745;">
                            ৳<?php echo e(number_format($totalRepaid, 2)); ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Remaining Balance</div>
                        <div class="detail-value" style="color: #dc3545;">
                            ৳<?php echo e(number_format(max(0, $remainingWithInterest), 2)); ?></div>
                    </div>
                </div>
                <?php if ($remainingWithInterest <= 0): ?>
                    <div
                        style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-top: 15px; text-align: center;">
                        <strong>🎉 Congratulations! Your loan has been fully repaid!</strong>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Next Steps -->
            <div style="margin-top: 30px;">
                <h3 style="color: #333;">📞 Need Help?</h3>
                <p>If you have any questions about this transaction or your loan account, please don't hesitate to
                    contact us:</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin: 8px 0;">📧 Email: <a
                            href="mailto:your_smtp_mail@gmail.com">your_smtp_mail@gmail.com</a></li>
                    <li style="margin: 8px 0;">🏢 Branch: <?php echo e($branch->name); ?>,
                        <?php echo e($branch->district); ?></li>
                    <li style="margin: 8px 0;">🌐 Region: <?php echo e($branch->region); ?></li>
                </ul>
            </div>

            <div
                style="margin-top: 30px; padding: 20px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;">
                <p style="margin: 0; color: #856404;"><strong>📋 Important Note:</strong> Please keep this email as a
                    record of your payment. This serves as your official payment confirmation receipt.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>BRAC Microfinance Loan Management System</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© <?php echo e(date('Y')); ?> BRAC Microfinance. All rights reserved.</p>
            <p><a href="mailto:your_smtp_mail@gmail.com">Contact Support</a> | <a href="#">Privacy Policy</a></p>
        </div>
    </div>
</body>

</html><?php /**PATH /var/www/html/resources/views/emails/repayment-confirmation.blade.php ENDPATH**/ ?>